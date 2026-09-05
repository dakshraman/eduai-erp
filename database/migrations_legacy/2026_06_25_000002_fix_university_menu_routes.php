<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->hideUniversityClassRoomPermission();
        $this->fixUniversityClassRoutinePermission();

        foreach (['sm_menus', 'default_menus'] as $table) {
            $this->hideUniversityClassRoomMenu($table);
            $this->fixUniversityClassRoutineMenu($table);
        }

        if (function_exists('clearPermissionMenuCache')) {
            clearPermissionMenuCache();
        }
    }

    public function down(): void
    {
        //
    }

    private function hideUniversityClassRoomPermission(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        DB::table('permissions')
            ->where('module', 'University')
            ->where('route', 'class-room')
            ->where(function ($query): void {
                $query->where('name', 'Class Room')
                    ->orWhere('lang_name', 'academics.class_room');
            })
            ->update($this->visibilityUpdate('permissions', 0));
    }

    private function fixUniversityClassRoutinePermission(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        DB::table('permissions')
            ->where('module', 'University')
            ->where(function ($query): void {
                $query->where('name', 'Class Routine')
                    ->orWhere('lang_name', 'academics.class_routine');
            })
            ->update($this->classRoutineUpdate('permissions'));
    }

    private function hideUniversityClassRoomMenu(string $table): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        DB::table($table)
            ->where('module', 'University')
            ->where('route', 'class-room')
            ->where(function ($query): void {
                $query->where('name', 'Class Room')
                    ->orWhere('lang_name', 'academics.class_room');
            })
            ->update($this->visibilityUpdate($table, 0));
    }

    private function fixUniversityClassRoutineMenu(string $table): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        DB::table($table)
            ->where('module', 'University')
            ->where(function ($query): void {
                $query->where('name', 'Class Routine')
                    ->orWhere('lang_name', 'academics.class_routine');
            })
            ->update($this->classRoutineUpdate($table));
    }

    private function visibilityUpdate(string $table, int $visible): array
    {
        return $this->onlyExistingColumns($table, [
            'is_menu' => $visible,
            'menu_status' => $visible,
            'status' => $visible,
            'updated_at' => now(),
        ]);
    }

    private function classRoutineUpdate(string $table): array
    {
        return $this->onlyExistingColumns($table, [
            'route' => 'university.academics.classRoutine',
            'parent_route' => 'university',
            'is_menu' => 1,
            'menu_status' => 1,
            'status' => 1,
            'updated_at' => now(),
        ]);
    }

    private function onlyExistingColumns(string $table, array $values): array
    {
        return array_filter(
            $values,
            fn (string $column): bool => Schema::hasColumn($table, $column),
            ARRAY_FILTER_USE_KEY
        );
    }
};
