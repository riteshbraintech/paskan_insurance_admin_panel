<?php

namespace App\Observers;

use App\Models\AdminLog;
use App\Models\CMSPage;

class CMSPageObserver
{
    /**
     * Handle the CMSPage "created" event.
     *
     * @param  \App\Models\CMSPage  $cMSPage
     * @return void
     */
    public function created(CMSPage $cMSPage)
    {
        $admin = auth('admin')->user();

        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'CMS Page Created by ' . $admin->name,
            'event_type' => 'create',
            'notes'      => $cMSPage->toArray(),
        ]);


    }

    /**
     * Handle the CMSPage "updated" event.
     *
     * @param  \App\Models\CMSPage  $cMSPage
     * @return void
     */
    public function updated(CMSPage $cMSPage)
    {
        // dd($cMSPage);
        $admin = auth('admin')->user();
        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'CMS Page Updated' . $admin->name,
            'event_type' => 'update',
            'notes'      => [
                'old' => $cMSPage->getOriginal(),
                'new' => $cMSPage->getChanges(),
            ],
        ]);
    }
    

    /**
     * Handle the CMSPage "deleted" event.
     *
     * @param  \App\Models\CMSPage  $cMSPage
     * @return void
     */
    public function deleted(CMSPage $cMSPage)
    {
        // dd($cMSPage);
        $admin = auth('admin')->user();
        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'CMS Page Deleted' . $admin->name,
            'event_type' => 'delete',
            'notes'      => $cMSPage->toArray(),
        ]);
    }

    /**
     * Handle the CMSPage "restored" event.
     *
     * @param  \App\Models\CMSPage  $cMSPage
     * @return void
     */
    public function restored(CMSPage $cMSPage)
    {
        //
    }

    /**
     * Handle the CMSPage "force deleted" event.
     *
     * @param  \App\Models\CMSPage  $cMSPage
     * @return void
     */
    public function forceDeleted(CMSPage $cMSPage)
    {
        //
    }
}
