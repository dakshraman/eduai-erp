<?php

namespace App\Services;

use App\Contracts\FileUploadServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * FileUploadService — Centralized file upload handler.
 *
 * Replaces 3 competing upload systems that existed in the codebase:
 *
 *   BEFORE (scattered across 173+ controller methods):
 *     Pattern A: $file->move('public/uploads/leave_request/', $fileName)
 *     Pattern B: fileUpload($file, 'public/uploads/student/')
 *     Pattern C: ImageStore::saveImage($file)
 *
 *   AFTER (one service, called from helpers + controllers):
 *     app(FileUploadServiceInterface::class)->upload($file, 'leave_request')
 *     // or via unchanged helper:
 *     fileUpload($file, 'public/uploads/leave_request/')
 *
 * KEY DESIGN DECISIONS:
 *
 * 1. PATH COMPATIBILITY: Stored paths remain 'public/uploads/{dir}/{file}' to maintain
 *    backward compatibility with all existing DB values and views using asset().
 *
 * 2. DIRECTORY CREATION: Auto-creates target directory if missing (0755, safer than 0777).
 *
 * 3. FILENAME GENERATION: Uses md5(originalName + time()) for uniqueness — matches
 *    existing fileUpload() helper so output is consistent.
 *
 * 4. NO STORAGE FACADE: Uses raw PHP file operations to stay compatible with shared
 *    hosting setups where storage symlinks may not exist.
 *
 * 5. SILENT ERRORS: Missing files / delete failures are logged but never throw, so
 *    a failed delete never crashes an otherwise successful update operation.
 */
class FileUploadService implements FileUploadServiceInterface
{
    /**
     * Upload a new file to the given directory.
     *
     * @param  string  $directory  Directory key (from config/uploads.php) or raw path
     * @param  string|null  $prefix  Optional filename prefix
     * @return string Stored path relative to project root (e.g. 'public/uploads/student/abc.jpg')
     */
    public function upload(?UploadedFile $file, string $directory, ?string $prefix = null): string
    {
        if (! $file || ! $file->isValid()) {
            return '';
        }

        $targetDir = $this->resolvePath($directory);
        $fileName = $this->generateFileName($file, $prefix);
        $targetDir = rtrim($targetDir, '/').'/';

        $this->ensureDirectoryExists($targetDir);

        $file->move($targetDir, $fileName);

        return $targetDir.$fileName;
    }

    /**
     * Replace an existing file.
     * Deletes old file from disk before storing new one.
     * Returns existing path unchanged if no new file provided.
     *
     * @param  string|null  $existingPath  Current DB-stored path
     */
    public function update(?string $existingPath, ?UploadedFile $newFile, string $directory, ?string $prefix = null): string
    {
        if (! $newFile || ! $newFile->isValid()) {
            // No new file — keep existing
            return $existingPath ?? '';
        }

        // Delete the old file before uploading the new one
        $this->delete($existingPath);

        return $this->upload($newFile, $directory, $prefix);
    }

    /**
     * Delete a file from disk.
     * Silently ignores missing files.
     */
    public function delete(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        if (file_exists($path)) {
            try {
                unlink($path);

                return true;
            } catch (Throwable $e) {
                Log::warning("FileUploadService: failed to delete file '{$path}': ".$e->getMessage());

                return false;
            }
        }

        return false;
    }

    /**
     * Validate a file against allowed MIME types and max size.
     *
     * @param  array  $allowedTypes  Empty array = allow all types
     * @param  int|null  $maxSizeMb  Null = use system generalSetting()->file_size
     *
     * @throws ValidationException
     */
    public function validate(UploadedFile $file, array $allowedTypes = [], ?int $maxSizeMb = null): void
    {
        $rules = [];

        // Size validation
        $maxMb = $maxSizeMb ?? $this->getSystemMaxFileSizeMb();
        if ($maxMb > 0) {
            $maxKb = $maxMb * 1024;
            $rules['file'] = "max:{$maxKb}";
        }

        // MIME type validation
        if (! empty($allowedTypes)) {
            $mimes = implode(',', $this->mimeTypesToExtensions($allowedTypes));
            $rules['file'] = ($rules['file'] ?? '')."|mimes:{$mimes}";
        }

        if (empty($rules)) {
            return;
        }

        $validator = Validator::make(
            ['file' => $file],
            ['file' => ltrim($rules['file'], '|')]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Resolve a directory key or raw path to the full filesystem path.
     */
    public function resolvePath(string $directoryOrKey): string
    {
        // If it already looks like a path (contains /), use it directly
        if (str_contains($directoryOrKey, '/')) {
            return rtrim($directoryOrKey, '/').'/';
        }

        // Look up in config/uploads.php
        $path = config("uploads.{$directoryOrKey}");

        if ($path) {
            return rtrim($path, '/').'/';
        }

        // Fallback: treat as raw directory name under public/uploads/
        Log::warning("FileUploadService: unknown directory key '{$directoryOrKey}', falling back to public/uploads/{$directoryOrKey}/");

        return "public/uploads/{$directoryOrKey}/";
    }

    /**
     * Generate a unique filename preserving the original extension.
     * Uses md5(name + time()) to match the existing fileUpload() helper output.
     */
    private function generateFileName(UploadedFile $file, ?string $prefix = null): string
    {
        $hash = md5($file->getClientOriginalName().time());
        $extension = $file->getClientOriginalExtension();
        $safeName = ($prefix ? $prefix : '').$hash.'.'.$extension;

        return $safeName;
    }

    /**
     * Create the target directory if it does not exist.
     * Uses 0755 instead of the old 0777 for better security.
     */
    private function ensureDirectoryExists(string $path): void
    {
        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * Get the system's maximum file size setting (in MB) from generalSetting().
     * Falls back to 5MB if not configured.
     */
    private function getSystemMaxFileSizeMb(): int
    {
        try {
            return (int) (generalSetting()->file_size ?? 5);
        } catch (Throwable $e) {
            return 5;
        }
    }

    /**
     * Convert MIME types to file extensions for Laravel's mimes validation rule.
     * e.g. ['image/jpeg', 'image/png'] → ['jpeg', 'jpg', 'png']
     */
    private function mimeTypesToExtensions(array $mimeTypes): array
    {
        $map = [
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'image/gif' => ['gif'],
            'image/webp' => ['webp'],
            'image/svg+xml' => ['svg'],
            'application/pdf' => ['pdf'],
            'application/msword' => ['doc'],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
            'application/vnd.ms-excel' => ['xls'],
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],
            'text/plain' => ['txt'],
        ];

        $extensions = [];
        foreach ($mimeTypes as $mime) {
            if (isset($map[$mime])) {
                $extensions = array_merge($extensions, $map[$mime]);
            }
        }

        return array_unique($extensions);
    }
}
