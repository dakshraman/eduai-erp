<?php

namespace App\View\Components;

use App\Models\SmParent;
use App\Models\SmStudent;
use App\Traits\SidebarDataStore;
use Illuminate\View\Component;

class SidebarComponent extends Component
{
    use SidebarDataStore;

    public function __construct()
    {
        //
    }

    public function render()
    {
        $data = [];

        $data['paid_modules'] = $this->allActivePaidModules();

        if (auth()->check() && auth()->user()->role_id === 3) {
            $parent = SmParent::where('user_id', auth()->id())->first();
            $data['children'] = $parent
                ? SmStudent::where('parent_id', $parent->id)->get()
                : collect();
            $data['menus'] = getMenus('parent');
        } elseif (auth()->check() && auth()->user()->role_id === 2) {
            $data['menus'] = getMenus('student');
        } else {
            $data['menus'] = getMenus('staff');
        }

        return view('components.sidebar-component', $data);
    }
}
