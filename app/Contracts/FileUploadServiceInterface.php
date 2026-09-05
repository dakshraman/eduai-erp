<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

/**
 * FileUploadServiceInterface
 *
 * Contract for the centralized file upload service.
 * All file uploads in the application must go through an implementation of this interface.
 *
 * Previously there were 3 competing upload systems:
 *   A) Raw $file->move('public/uploads/...') — 173 occurrences — bypasses all abstraction
 *   B) fileUpload() / fileUpdate() helpers  —  152 occurrences — procedural, no validation
 *   C) ImageStore / FileStore traits         —    4 occurrences — partial, inconsistent paths
 *
 * All three are now replaced by this interface, implemented by FileUploadService.
 * The global helpers fileUpload() and fileUpdate() are kept as thin wrappers for
 * backward compatibility but now delegate here.
 */
interface FileUploadServiceInterface
{
    /**
     * Upload a new file to the given disk-relative path.
     *
     * @param  UploadedFile|null  $file  The uploaded file from the request
     * @param  string  $directory  Target directory key (from config/uploads.php)
     *                             OR a raw path like 'public/uploads/student/'
     * @param  string|null  $prefix  Optional filename prefix (e.g. 'logo-', 'photo-')
     * @return string The stored path relative to project root,
     *                compatible with asset() — e.g. 'public/uploads/student/abc123.jpg'
     *                Returns '' if $file is null/invalid.
     */
    public function upload(?UploadedFile $file, string $directory, ?string $prefix = null): string;

    /**
     * Replace an existing uploaded file.
     * Deletes the old file from disk if it exists, uploads the new one.
     * If no new file is provided, returns the existing path unchanged.
     *
     * @param  string|null  $existingPath  Current stored path (from DB column)
     * @param  UploadedFile|null  $newFile  New file from request (null = keep existing)
     * @param  string  $directory  Target directory key or raw path
     * @param  string|null  $prefix  Optional filename prefix
     * @return string New stored path, or original $existingPath if unchanged
     */
    public function update(?string $existingPath, ?UploadedFile $newFile, string $directory, ?string $prefix = null): string;

    /**
     * Delete an uploaded file from disk.
     * Silently ignores missing files (no exception thrown).
     *
     * @param  string|null  $path  The stored path to delete (e.g. 'public/uploads/student/abc123.jpg')
     * @return bool True if deleted, false if not found or path empty
     */
    public function delete(?string $path): bool;

    /**
     * Validate a file before uploading.
     * Throws a ValidationException if the file fails any check.
     *
     * @param  UploadedFile  $file  The file to validate
     * @param  array  $allowedTypes  Allowed MIME types (e.g. ['image/jpeg', 'image/png'])
     * @param  int|null  $maxSizeMb  Max file size in MB (null = use system setting)
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(UploadedFile $file, array $allowedTypes = [], ?int $maxSizeMb = null): void;

    /**
     * Resolve a directory key to its full filesystem path.
     * If given a raw path like 'public/uploads/student/', returns it as-is.
     * If given a key like 'student_photos', resolves from config/uploads.php.
     *
     * @return string Resolved filesystem path (e.g. 'public/uploads/student/')
     */
    public function resolvePath(string $directoryOrKey): string;
}
