<?php

namespace App\Observers;

use App\Models\AdminLog;
use App\Models\Category;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function created(Category $category)
    {
        $admin = auth('admin')->user();

        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'Category Created By ' . $admin->name,
            'event_type' => 'create',
            'notes'      => $category->toArray(),
        ]);
    }

    /**
     * Handle the Category "updated" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function updated(Category $category)
    {
        // dd("hii");
        $admin = auth('admin')->user();
        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'Category Updated By ' . $admin->name,
            'event_type' => 'update',
            'notes'      => [
                'old' => $category->getOriginal(),
                'new' => $category->getChanges(),
            ],
        ]);
    }

    /**
     * Handle the Category "deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function deleted(Category $category)
    {
        // dd("hii");
        $admin = auth('admin')->user();
        AdminLog::create([
            'admin_id'   => $admin->id,
            'event_name' => 'Category Deleted By ' . $admin->name,
            'event_type' => 'delete',
            'notes'      => $category->toArray(),
        ]);
    }

    /**
     * Handle the Category "restored" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function restored(Category $category)
    {
        
    }

    /**
     * Handle the Category "force deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function forceDeleted(Category $category)
    {
        
    }
}
