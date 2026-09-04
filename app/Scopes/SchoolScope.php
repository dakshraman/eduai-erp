<?php

namespace App\Scopes;

use App\Support\ModuleRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SchoolScope implements Scope
{
    /**
     * OPTIMIZED: Replaced moduleStatusCheck('Saas') with ModuleRegistry::isActive('Saas').
     * Called on every query — zero DB overhead now.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::check()) {
            return;
        }

        $table = $model->getTable();
        $user = auth()->user();
        $isSuperAdmin = ModuleRegistry::isActive('Saas')
            && $user->is_administrator === 'yes'
            && Session::get('isSchoolAdmin') === false
            && $user->role_id === 1;

        if (request('school_id')) {
            $builder->where($table.'.school_id', request('school_id'));
        } elseif ($isSuperAdmin) {
            // Super admin: unrestricted across schools
        } elseif (app()->bound('school')) {
            $builder->where($table.'.school_id', app('school')->id);
        } else {
            $builder->where($table.'.school_id', $user->school_id);
        }
    }
}
