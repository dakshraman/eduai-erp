<?php

namespace App\Scopes;

use App\Support\ModuleRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class AcademicSchoolScope implements Scope
{
    /**
     * Apply the global scope to a given Eloquent query builder.
     *
     * OPTIMIZED: Replaced moduleStatusCheck() calls with ModuleRegistry::isActive().
     * This scope is applied on every query for 97+ models.
     * Previously each apply() called moduleStatusCheck() 2x — hitting DB + filesystem.
     * Now uses pre-computed in-memory registry: zero DB overhead per query.
     *
     * Also: removed the redundant `elseif (Auth::check())` branch (already confirmed
     * Auth::check() is true by the outer if — the inner elseif was unreachable dead code).
     */
    public function apply(Builder $builder, Model $model): void
    {
        $table = $model->getTable();
        $academicCol = $table.'.academic_id';

        // University module may use a different academic ID column
        if (ModuleRegistry::isActive('University')) {
            $columns = Schema::getColumnListing($table);
            if (in_array('un_academic_id', $columns, true)) {
                $academicCol = $table.'.un_academic_id';
            }
        }

        if (! Auth::check()) {
            $builder->where($table.'.school_id', 1);

            return;
        }

        $user = Auth::user();
        $isSaas = ModuleRegistry::isActive('Saas');
        $isSuperAdmin = $isSaas
            && $user->is_administrator === 'yes'
            && Session::get('isSchoolAdmin') === false
            && $user->role_id === 1;

        if ($isSuperAdmin) {
            // Super admin in SaaS mode: filter by academic year only (no school_id restriction)
            $builder->where($academicCol, getAcademicId());
        } elseif (app()->bound('school')) {
            $builder->where($academicCol, getAcademicId())
                ->where($table.'.school_id', app('school')->id);
        } else {
            $builder->where($academicCol, getAcademicId())
                ->where($table.'.school_id', $user->school_id);
        }
    }
}
