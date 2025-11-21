<?php

namespace App\Observers;

use App\Models\AdminLog;
use App\Models\Article;

class ArticleObserver
{
    /**
     * Handle the Article "created" event.
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function created(Article $article)
    {
         $admin = auth('admin')->user();

        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'Article Created By ' . $admin->name,
            'event_type' => 'create',
            'notes'      => $article->toArray(),
        ]);
    }

    /**
     * Handle the Article "updated" event.
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function updated(Article $article)
    {
        //  dd("hii");
        $admin = auth('admin')->user();
        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'Article Updated By ' . $admin->name,
            'event_type' => 'update',
            'notes'      => [
                'old' => $article->getOriginal(),
                'new' => $article->getChanges(),
            ],
        ]);
    }

    /**
     * Handle the Article "deleted" event.
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function deleted(Article $article)
    {
        // dd($cMSPage);
        $admin = auth('admin')->user();
        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'Article Deleted By ' . $admin->name,
            'event_type' => 'delete',
            'notes'      => $article->toArray(),
        ]);
    }

    /**
     * Handle the Article "restored" event.
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function restored(Article $article)
    {
        //
    }

    /**
     * Handle the Article "force deleted" event.
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function forceDeleted(Article $article)
    {
        //
    }
}
