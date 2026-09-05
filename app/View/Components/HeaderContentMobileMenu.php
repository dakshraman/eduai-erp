<?php

namespace App\View\Components;

use App\Models\SmHeaderMenuManager;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeaderContentMobileMenu extends Component
{
    public function __construct()
    {
        //
    }

    public function render(): View|Closure|string
    {
        $menus = HeaderContentMenu::getMenus();

        return view('components.'.activeTheme().'.header-content-mobile-menu', ['menus' => $menus]);
    }
}
