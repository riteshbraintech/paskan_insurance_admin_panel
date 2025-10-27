<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;


class IsTestScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // dd(admin()->check());
        if (admin()->check() && admin()->user()->role_id !== 'superadmin') {
            $isTest = admin()->user()->is_test;
            $builder->where('is_test', $isTest);
        }

        if (admin()->check() && admin()->user()->role_id == 'superadmin') {
            $istest = session('is_test') ? session('is_test') : false;
            $val = $istest ? 1:0;
            $builder->where('is_test', $val);
        }

    }
}
