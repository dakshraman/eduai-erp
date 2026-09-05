<?php

namespace App\Traits;

use Carbon\Carbon;
use File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * FileStore — handles non-image file uploads (documents, attachments).
 * Stores files under uploads/file/{date}/
 *
 * NOTE: Do NOT merge with ImageStore trait.
 * ImageStore.saveFile() stores under uploads/images/{date}/ — different path.
 * FileStore.saveFile() stores under uploads/file/{date}/ — for attachments/documents.
 * GroupChatController uses both traits intentionally, calling each for different file types.
 */
trait FileStore
{
    public static function saveFile(UploadedFile $uploadedFile): ?string
    {
        $current_date = Carbon::now()->format('d-m-Y');
        if (! File::isDirectory('uploads/file/'.$current_date)) {
            File::makeDirectory('uploads/file/'.$current_date, 0777, true, true);
        }

        $s = Storage::disk('custom')->put('file/'.$current_date, $uploadedFile);

        return 'uploads/'.$s;
    }
}
