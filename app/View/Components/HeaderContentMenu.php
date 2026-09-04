<?php

namespace App\View\Components;

use App\Models\SmHeaderMenuManager;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeaderContentMenu extends Component
{
    /**
     * Fetch menus once per request, shared with HeaderContentMobileMenu.
     * Eager-loads all nested children in a single recursive query set,
     * eliminating the N+1 pattern (9 queries per component → 1 shared load).
     */
    public static function getMenus(): \Illuminate\Support\Collection
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        $schoolId = app()->bound('school') ? app('school')->id : 1;
        $theme = activeTheme() ?: 'edulia';

        $cache = SmHeaderMenuManager::with('childs')
            ->where('school_id', $schoolId)
            ->where('theme', $theme)
            ->whereNull('parent_id')
            ->orderBy('position')
            ->get();

        return $cache;
    }

    public function render(): View|Closure|string
    {
        $menus = self::getMenus();

        return view('components.'.activeTheme().'.header-content-menu', ['menus' => $menus]);
    }
}
