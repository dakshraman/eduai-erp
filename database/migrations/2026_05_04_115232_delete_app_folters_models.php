<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $appDir = base_path('/app');
        $exceptions = [
            realpath($appDir.'/SmWeekend.php'),
            realpath($appDir.'/User.php'),
        ];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($appDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );



        foreach (new DirectoryIterator($appDir) as $file) {

            if ($file->isFile()) {
                $realPath = $file->getRealPath();
                if (! in_array($realPath, $exceptions)) {
                    unlink($realPath);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
