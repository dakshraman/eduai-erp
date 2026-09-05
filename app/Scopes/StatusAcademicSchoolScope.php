<?php

namespace App\Scopes;

use App\Support\ModuleRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class StatusAcademicSchoolScope implements Scope
{
    /**
     * OPTIMIZED: Replaced both moduleStatusCheck() calls with ModuleRegistry::isActive().
     * Applied on every query — eliminates DB + filesystem overhead per query.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $table = $model->getTable();
        $academicCol = $table.'.academic_id';

        if (ModuleRegistry::isActive('University')) {
            $columns = Schema::getColumnListing($table);
            if (in_array('un_academic_id', $columns, true)) {
                $academicCol = $table.'.un_academic_id';
            }
        }

        if (! Auth::check()) {
            $builder->where($table.'.active_status', 1);

            return;
        }

        $user = Auth::user();
        $isSuperAdmin = ModuleRegistry::isActive('Saas')
            && $user->is_administrator === 'yes'
            && Session::get('isSchoolAdmin') === false
            && $user->role_id === 1;

        if ($isSuperAdmin) {
            $builder->where($table.'.active_status', 1)
                ->where($academicCol, getAcademicId());
        } else {
            $builder->where($table.'.active_status', 1)
                ->where($academicCol, getAcademicId())
                ->where($table.'.school_id', $user->school_id);
        }
    }
}
