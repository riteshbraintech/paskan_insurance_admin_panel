<?php

namespace App\Observers;

use App\Models\AdminLog;
use App\Models\Categoryformfield;

class CategoryformfieldObserver
{
    /**
     * Handle the Categoryformfield "created" event.
     *
     * @param  \App\Models\Categoryformfield  $categoryformfield
     * @return void
     */
    public function created(Categoryformfield $categoryformfield)
    {
        $admin = auth('admin')->user();

        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'Category Form Field Created By ' . $admin->name,
            'event_type' => 'create',
            'notes'      => $categoryformfield->toArray(),
        ]);
    }

    /**
     * Handle the Categoryformfield "updated" event.
     *
     * @param  \App\Models\Categoryformfield  $categoryformfield
     * @return void
     */
    public function updated(Categoryformfield $categoryformfield)
    {
        // dd("hii");
        $admin = auth('admin')->user();
        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'Category form field Updated By ' . $admin->name,
            'event_type' => 'update',
            'notes'      => [
                'old' => $categoryformfield->getOriginal(),
                'new' => $categoryformfield->getChanges(),
            ],
        ]);
    }

    /**
     * Handle the Categoryformfield "deleted" event.
     *
     * @param  \App\Models\Categoryformfield  $categoryformfield
     * @return void
     */
    public function deleted(Categoryformfield $categoryformfield)
    {
        // dd($cMSPage);
        $admin = auth('admin')->user();
        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'Category form field Deleted By ' . $admin->name,
            'event_type' => 'delete',
            'notes'      => $categoryformfield->toArray(),
        ]);
    }

    /**
     * Handle the Categoryformfield "restored" event.
     *
     * @param  \App\Models\Categoryformfield  $categoryformfield
     * @return void
     */
    public function restored(Categoryformfield $categoryformfield)
    {
        //
    }

    /**
     * Handle the Categoryformfield "force deleted" event.
     *
     * @param  \App\Models\Categoryformfield  $categoryformfield
     * @return void
     */
    public function forceDeleted(Categoryformfield $categoryformfield)
    {
        //
    }
}
