<?php

namespace App\Traits;

use App\Contracts\FileUploadServiceInterface;
use Carbon\Carbon;
use File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/**
 * ImageStore — Image-specific upload trait for API chat controllers.
 *
 * NOTE ON SCOPE:
 * This trait handles image uploads that need Intervention/Image processing
 * (resize, mime extraction). It is intentionally NOT merged into FileUploadService
 * because FileUploadService handles plain file moves, while this trait uses
 * Intervention\Image for dimension-constrained resizing.
 *
 * USED BY:
 *   - Admin\Chat\AdminChatController
 *   - Admin\Chat\GroupChatController
 *   - Admin\SystemSettings\PreloaderSettingController
 *
 * STORAGE PATHS:
 *   saveImage()         → uploads/images/{date}/{uniqid}.ext
 *   saveSettingsImage() → uploads/settings/{uniqid}.ext
 *   saveAvatar/Image()  → uploads/avatar/{date}/{uniqid}.ext
 *
 * NOTE: These paths use 'uploads/' (not 'public/uploads/') — they go through
 * the 'custom' disk (base_path().'/uploads'). Separate convention from controllers.
 *
 * PERMISSION FIX: 0777 → 0755 for shared hosting security.
 */
trait ImageStore
{
    public static function saveImage($image, $height = null, $lenght = null): ?string
    {
        if (! isset($image)) {
            return null;
        }

        $currentDate = Carbon::now()->format('d-m-Y');
        $dir = 'uploads/images/'.$currentDate;

        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        $extension = str_replace('image/', '', Image::make($image)->mime());
        $img = ($height !== null && $lenght !== null)
            ? Image::make($image)->resize($height, $lenght)
            : Image::make($image);

        $imgName = $dir.'/'.uniqid().'.'.$extension;
        $img->save($imgName);

        return $imgName;
    }

    public static function saveFile(UploadedFile $uploadedFile): ?string
    {
        $currentDate = Carbon::now()->format('d-m-Y');
        $dir = 'uploads/file/'.$currentDate;

        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        $s = Storage::disk('custom')->put('file/'.$currentDate, $uploadedFile);

        return 'uploads/'.$s;
    }

    public static function saveSettingsImage($image, $height = null, $lenght = null): ?string
    {
        if (! isset($image)) {
            return null;
        }

        $dir = 'uploads/settings';
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        $extension = str_replace('image/', '', Image::make($image)->mime());
        $img = ($height !== null && $lenght !== null)
            ? Image::make($image)->resize($height, $lenght)
            : Image::make($image);

        $imgName = $dir.'/'.uniqid().'.'.$extension;
        $img->save($imgName);

        return $imgName;
    }

    public static function saveAvatarImage($image, $height = null, $lenght = null): ?string
    {
        if (! isset($image)) {
            return null;
        }

        $extension = str_replace('image/', '', Image::make($image)->mime());
        $img = ($height !== null && $lenght !== null)
            ? Image::make($image)->resize($height, $lenght)
            : Image::make($image);

        $imgName = 'uploads/avatar/'.uniqid().'.'.$extension;
        $img->save($imgName);

        return $imgName;
    }

    /**
     * Delete an image file.
     * Delegates to FileUploadService for consistent error handling and logging.
     */
    public static function deleteImage(?string $url): ?bool
    {
        if (! isset($url)) {
            return null;
        }

        return app(FileUploadServiceInterface::class)->delete($url);
    }

    public function saveAvatar($image, $height = null, $lenght = null): ?string
    {
        if (! isset($image)) {
            return null;
        }

        $currentDate = Carbon::now()->format('d-m-Y');
        $dir = 'uploads/avatar/'.$currentDate;

        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        $extension = str_replace('image/', '', Image::make($image)->mime());
        $img = ($height !== null && $lenght !== null)
            ? Image::make($image)->resize($height, $lenght)
            : Image::make($image);

        $imgName = $dir.'/'.uniqid().'.'.$extension;
        $img->save($imgName);

        return $imgName;
    }
}
