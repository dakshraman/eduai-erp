<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Modules\MenuManage\Entities\SmMenu;

class MenuGenerateController extends Controller
{
    public function routeGen()
    {
        $admin_menu = DB::table('sm_menus')->where('route', 'online_exam')->where('role_id', 1)->where('school_id', 1)->first();
        $module_section = DB::table('sm_menus')->where('route', 'module_section')->where('role_id', 1)->first();
        $hasOne = SmMenu::where('route', $admin_menu->route)
            ->where('parent_id', $module_section->id)
            ->where('role_id', 1)
            ->first();
        $schools = DB::table('sm_schools')->get();
        $menus = [];
        if (! $hasOne) {
            $parent = SmMenu::create([
                'parent' => $module_section->id,
                'parent_id' => $module_section->id,
                'name' => $admin_menu->name,
                'lang_name' => $admin_menu->lang_name,
                'route' => $admin_menu->route,
                'status' => 1,
                'menu_status' => 1,
                'icon' => $admin_menu->icon,
                'role_id' => 1,
                'module' => 'OnlineExam',
                'position' => 13,
                'default_position' => 13,
                'school_id' => 1,
                'ignore' => 1,
            ]);
            foreach ($schools as $school) {
                $menus = [
                    // Admin
                    [
                        'parent' => $parent->id,
                        'parent_id' => $parent->id,
                        'name' => 'Question Group',
                        'lang_name' => 'onlineexam::onlineExam.question_group',
                        'route' => 'online-question-group',
                        'status' => 1,
                        'menu_status' => 1,
                        'role_id' => 1,
                        'module' => 'OnlineExam',
                        'position' => 1,
                        'default_position' => 1,
                        'school_id' => $school->id,
                        'ignore' => 1,
                    ],
                    [
                        'parent' => $parent->id,
                        'parent_id' => $parent->id,
                        'name' => 'Question Bank',
                        'lang_name' => 'onlineexam::onlineExam.question_bank',
                        'route' => 'online-question-bank',
                        'status' => 1,
                        'menu_status' => 1,
                        'role_id' => 1,
                        'module' => 'OnlineExam',
                        'position' => 2,
                        'default_position' => 2,
                        'school_id' => $school->id,
                        'ignore' => 1,

                    ],
                    [
                        'parent' => $parent->id,
                        'parent_id' => $parent->id,
                        'name' => 'Add Online Exam',
                        'lang_name' => 'onlineexam::onlineExam.add_online_exam',
                        'route' => 'om-online-exam-add',
                        'status' => 1,
                        'menu_status' => 1,
                        'role_id' => 1,
                        'module' => 'OnlineExam',
                        'position' => 3,
                        'default_position' => 3,
                        'school_id' => $school->id,
                        'ignore' => 1,

                    ],
                    [
                        'parent' => $parent->id,
                        'parent_id' => $parent->id,
                        'name' => 'Online Exam',
                        'lang_name' => 'onlineexam::onlineExam.online_exam',
                        'route' => 'om-online-exam',
                        'status' => 1,
                        'menu_status' => 1,
                        'role_id' => 1,
                        'module' => 'OnlineExam',
                        'position' => 4,
                        'default_position' => 4,
                        'school_id' => $school->id,
                        'ignore' => 1,

                    ],
                    [
                        'parent' => $parent->id,
                        'parent_id' => $parent->id,
                        'name' => 'Written Exam',
                        'lang_name' => 'onlineexam::onlineExam.written_exam',
                        'route' => 'written_exam',
                        'status' => 1,
                        'menu_status' => 1,
                        'role_id' => 1,
                        'module' => 'OnlineExam',
                        'position' => 5,
                        'default_position' => 5,
                        'school_id' => $school->id,
                        'ignore' => 1,

                    ],
                    [
                        'parent' => $parent->id,
                        'parent_id' => $parent->id,
                        'name' => 'Setting',
                        'lang_name' => 'onlineexam::onlineExam.settings',
                        'route' => 'online-exam-setting',
                        'status' => 1,
                        'menu_status' => 1,
                        'role_id' => 1,
                        'module' => 'OnlineExam',
                        'position' => 6,
                        'default_position' => 6,
                        'school_id' => $school->id,
                        'ignore' => 1,
                    ],
                ];
            }
            DB::table('sm_menus')->insert($menus);
        }
    }
}
