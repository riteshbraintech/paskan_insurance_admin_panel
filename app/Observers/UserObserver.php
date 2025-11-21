<?php

namespace App\Observers;

use App\Models\AdminLog;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $admin = auth('admin')->user();

        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'User Added By ' . $admin->name,
            'event_type' => 'create',
            'notes'      => $user->toArray(),
        ]);
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        // dd("hii");
        $admin = auth('admin')->user();
        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'User Updated By ' . $admin->name,
            'event_type' => 'update',
            'notes'      => [
                'old' => $user->getOriginal(),
                'new' => $user->getChanges(),
            ],
        ]);
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        // dd($cMSPage);
        $admin = auth('admin')->user();
        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'user Deleted By ' . $admin->name,
            'event_type' => 'delete',
            'notes'      => $user->toArray(),
        ]);
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
