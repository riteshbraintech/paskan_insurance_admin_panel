<?php

namespace App\Observers;

use App\Models\AdminLog;
use App\Models\Banner;

class BannerObserver
{
    /**
     * Handle the Banner "created" event.
     *
     * @param  \App\Models\Banner  $banner
     * @return void
     */
    public function created(Banner $banner)
    {
        $admin = auth('admin')->user();

        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'Banner Created By ' . $admin->name,
            'event_type' => 'create',
            'notes'      => $banner->toArray(),
        ]);
    }

    /**
     * Handle the Banner "updated" event.
     *
     * @param  \App\Models\Banner  $banner
     * @return void
     */
    public function updated(Banner $banner)
    {
        // dd("hii");
        $admin = auth('admin')->user();
        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'Banner Updated By ' . $admin->name,
            'event_type' => 'update',
            'notes'      => [
                'old' => $banner->getOriginal(),
                'new' => $banner->getChanges(),
            ],
        ]);
    }

    /**
     * Handle the Banner "deleted" event.
     *
     * @param  \App\Models\Banner  $banner
     * @return void
     */
    public function deleted(Banner $banner)
    {
        // dd($cMSPage);
        $admin = auth('admin')->user();
        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'Banner Deleted By ' . $admin->name,
            'event_type' => 'delete',
            'notes'      => $banner->toArray(),
        ]);
    }

    /**
     * Handle the Banner "restored" event.
     *
     * @param  \App\Models\Banner  $banner
     * @return void
     */
    public function restored(Banner $banner)
    {
        //
    }

    /**
     * Handle the Banner "force deleted" event.
     *
     * @param  \App\Models\Banner  $banner
     * @return void
     */
    public function forceDeleted(Banner $banner)
    {
        //
    }
}
