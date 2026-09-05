<?php

namespace App\Scopes;

use App\Support\ModuleRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ActiveStatusSchoolScope implements Scope
{
    /**
     * OPTIMIZED: Replaced moduleStatusCheck('Saas') with ModuleRegistry::isActive('Saas').
     */
    public function apply(Builder $builder, Model $model): void
    {
        $table = $model->getTable();

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
            $builder->where($table.'.active_status', 1);
        } elseif (app()->bound('school')) {
            $builder->where($table.'.active_status', 1)
                ->where($table.'.school_id', app('school')->id);
        } else {
            $builder->where($table.'.active_status', 1)
                ->where($table.'.school_id', $user->school_id);
        }
    }
}
