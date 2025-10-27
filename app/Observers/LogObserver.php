<?php

namespace App\Observers;

use App\Models\Log;

class LogObserver
{
    /**
     * Handle the Log "created" event.
     *
     * @param  \App\Models\Log  $log
     * @return void
     */
    public function created(Log $log)
    {
        
        $log->is_test = admin()->user()->is_test;
        $log->save();
    }

    /**
     * Handle the Log "updated" event.
     *
     * @param  \App\Models\Log  $log
     * @return void
     */
    public function updated(Log $log)
    {
        //
    }

    /**
     * Handle the Log "deleted" event.
     *
     * @param  \App\Models\Log  $log
     * @return void
     */
    public function deleted(Log $log)
    {
        //
    }

    /**
     * Handle the Log "restored" event.
     *
     * @param  \App\Models\Log  $log
     * @return void
     */
    public function restored(Log $log)
    {
        //
    }

    /**
     * Handle the Log "force deleted" event.
     *
     * @param  \App\Models\Log  $log
     * @return void
     */
    public function forceDeleted(Log $log)
    {
        //
    }
}
