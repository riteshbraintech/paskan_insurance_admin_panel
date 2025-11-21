<?php

namespace App\Observers;

use App\Models\AdminLog;
use App\Models\FAQ;

class FAQObserver
{
    /**
     * Handle the FAQ "created" event.
     *
     * @param  \App\Models\FAQ  $fAQ
     * @return void
     */
    public function created(FAQ $fAQ)
    {
        $admin = auth('admin')->user();

        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'FAQ Created By ' . $admin->name,
            'event_type' => 'create',
            'notes'      => $fAQ->toArray(),
        ]);
    }

    /**
     * Handle the FAQ "updated" event.
     *
     * @param  \App\Models\FAQ  $fAQ
     * @return void
     */
    public function updated(FAQ $fAQ)
    {
        // dd($fAQ->getChanges());

        $admin = auth('admin')->user();
        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'FAQ Updated By ' . $admin->name,
            'event_type' => 'update',
            'notes'      => [
                'old' => $fAQ->getOriginal(),
                'new' => $fAQ->getChanges(),
            ],
        ]);
    }

    /**
     * Handle the FAQ "deleted" event.
     *
     * @param  \App\Models\FAQ  $fAQ
     * @return void
     */
    public function deleted(FAQ $fAQ)
    {
        // dd($cMSPage);
        $admin = auth('admin')->user();
        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'FAQ Deleted By ' . $admin->name,
            'event_type' => 'delete',
            'notes'      => $fAQ->toArray(),
        ]);
    }

    /**
     * Handle the FAQ "restored" event.
     *
     * @param  \App\Models\FAQ  $fAQ
     * @return void
     */
    public function restored(FAQ $fAQ)
    {
        //
    }

    /**
     * Handle the FAQ "force deleted" event.
     *
     * @param  \App\Models\FAQ  $fAQ
     * @return void
     */
    public function forceDeleted(FAQ $fAQ)
    {
        //
    }
}
