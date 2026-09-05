<?php

namespace App\Models;

use App\Scopes\StatusAcademicSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Forum\Entities\ForumCategory;
use Modules\MenuManage\Entities\SmMenu;
use Modules\Saas\Entities\SmSubscriptionPayment;

class SmSchool extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function subscription()
    {
        return $this->hasOne(SmSubscriptionPayment::class, 'school_id')->latest();
    }

    public function academicYears()
    {
        return $this->hasMany(SmAcademicYear::class, 'school_id', 'id');
    }

    public function sections()
    {
        return $this->hasMany(SmSection::class, 'school_id');
    }

    public function classes()
    {
        return $this->hasMany(SmClass::class, 'school_id');
    }

    public function classTimes()
    {
        return $this->hasMany(SmClassTime::class, 'school_id')->where('type', 'class');
    }

    public function weekends()
    {
        return $this->hasMany(SmWeekend::class, 'school_id')->where('active_status', 1);
    }

    public function routineUpdates()
    {
        return $this->hasMany(SmClassRoutineUpdate::class, 'school_id', 'id')->where('active_status', 1);
    }

    public function saasRoutineUpdates()
    {
        return $this->hasMany(SmClassRoutineUpdate::class, 'school_id', 'id')->withoutGlobalScope(StatusAcademicSchoolScope::class)->where('active_status', 1);
    }

    public function forumCategories()
    {
        return $this->belongsToMany(ForumCategory::class, 'forum_category_school', 'school_id', 'forum_category_id');
    }

    public function settings()
    {
        return $this->hasOne(SmGeneralSettings::class, 'school_id');
    }

    protected static function booted()
    {
        static::created(function ($school) {
            $school->insertMenu();
        });

    }

    private function insertMenu()
    {
        $menus = DB::table('default_menus')->where('permission_section', 1)->orderBy('role_id', 'ASC')->get();

        foreach ($menus as $menu) {
            $first_menu = SmMenu::create([
                'name' => $menu->name,
                'route' => $menu->route,
                'module' => $menu->module,
                'lang_name' => $menu->lang_name,
                'icon' => $menu->icon,
                'status' => $menu->status,
                'is_saas' => $menu->is_saas,
                'school_id' => $this->id,
                'menu_status' => 1,
                'permission_section' => $menu->permission_section,
                'position' => $menu->position,
                'default_position' => $menu->default_position,
                'role_id' => $menu->role_id,
                'permission_id' => null,
                'parent' => null,
                'parent_id' => null,
            ]);
            $secound_menus = DB::table('default_menus')->where('parent_id', $menu->id)->orderBy('position', 'ASC')->get();
            if (count($secound_menus) > 0) {
                foreach ($secound_menus as $secound) {
                    $secound_menu = SmMenu::create([
                        'name' => $secound->name,
                        'route' => $secound->route,
                        'module' => $secound->module,
                        'lang_name' => $secound->lang_name,
                        'icon' => $secound->icon,
                        'status' => $secound->status,
                        'is_saas' => $secound->is_saas,
                        'school_id' => $this->id,
                        'menu_status' => 1,
                        'permission_section' => $secound->permission_section,
                        'position' => $secound->position,
                        'default_position' => $secound->default_position,
                        'role_id' => $secound->role_id,
                        'permission_id' => $secound->permission_id,
                        'parent' => $first_menu->id,
                        'parent_id' => $first_menu->id,
                    ]);

                    $third_menus = DB::table('default_menus')->where('parent_id', $secound->id)->orderBy('position', 'ASC')->get();
                    if (count($third_menus) > 0) {
                        foreach ($third_menus as $third) {
                            SmMenu::create([
                                'name' => $third->name,
                                'route' => $third->route,
                                'module' => $third->module,
                                'lang_name' => $third->lang_name,
                                'icon' => $third->icon,
                                'status' => $third->status,
                                'is_saas' => $third->is_saas,
                                'school_id' => $this->id,
                                'menu_status' => 1,
                                'permission_section' => $third->permission_section,
                                'position' => $third->position,
                                'default_position' => $third->default_position,
                                'role_id' => $third->role_id,
                                'permission_id' => $third->permission_id,
                                'parent' => $secound_menu->id,
                                'parent_id' => $secound_menu->id,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
