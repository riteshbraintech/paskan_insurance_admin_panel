<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Models\Role;
use App\Models\Admin;

class AdminIdScope implements Scope
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
        if (admin()->check()) {
            if (admin()->user()->role_id == Role::MANAGER) {
                $managerId = admin()->user()->id;
                $ids = Admin::testfilter()->where('created_by', $managerId)
                    ->get()
                    ->pluck('id')
                    ->toArray();
                array_push($ids, $managerId);

                $builder->whereIn('admin_id', $ids);
            } elseif (admin()->user()->role_id == Role::STAFF) {
                // if staff loggedin  only there bids will be show
                $adminId = admin()->user()->id;
                $builder->where('admin_id', $adminId);
            }
        }
    }
}
