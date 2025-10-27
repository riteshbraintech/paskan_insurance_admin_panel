<?php

namespace App\Observers;

use App\Models\Lead;
use App\Models\Log;

class LeadObserver
{
    /**
     * Handle the Lead "created" event.
     *
     * @param  \App\Models\Lead  $lead
     * @return void
     */
    public function created(Lead $lead)
    {
        
        $id = strlen($lead->id) >= 2 ? $lead->id :"0".$lead->id;
        $ldId = 'LEAD-'.$id;

        // Log::create([
        //     'admin_id'=>admin()->user()->id,
        //     'user_name'=>admin()->user()->email,
        //     'lead_id'=>$lead->id,
        //     'lead_show_id'=> $ldId,
        //     'old_status'=>$lead->old->status ?? '',
        //     'new_status'=>$lead->status,
        //     'page'=>'lead',
        //     'messages'=>'Lead : '.$ldId. ' Created By '.admin()->user()->email,
        //     'extra'=>json_encode(collect($lead)->toArray())
        // ]);

        $lead->lead_id = $ldId;
        $lead->is_test = admin()->user()->is_test;
        
        $lead->save();

    }

    /**
     * Handle the Lead "updated" event.
     *
     * @param  \App\Models\Lead  $lead
     * @return void
     */
    public function updated(Lead $lead)
    {

        $insData = [
            'admin_id'=>admin()->user()->id,
            'user_name'=>admin()->user()->email,
            'lead_id'=>$lead->id,
            'lead_show_id'=>$lead->lead_id,
            'extra'=>json_encode(collect($lead->old)->toArray())
        ];

        if($lead->isDirty('status') && !empty($lead->getOriginal('status'))){
            $oldStatus = isset(statusList()[$lead->getOriginal('status')]) ? statusList()[$lead->getOriginal('status')] : '';
            $newStatus = isset(statusList()[$lead->status]) ? statusList()[$lead->status] : '';

            $msg = 'Lead status change '. $oldStatus .' to '. $newStatus .' By '.admin()->user()->email;
            $insData['old_status'] = $oldStatus; 
            $insData['new_status'] = $newStatus; 
            $insData['page'] = 'Status';
            $insData['messages'] = $msg;
            Log::create($insData);

            $data = Lead::find($lead->id);
            if(!blank($data)){
                if($oldStatus == "Follow UP" && $newStatus != "Follow UP"){
                    $data->followup_count = 0;
                }else if($oldStatus != "Follow UP" && $newStatus == "Follow UP"){
                    $data->followup_count = ++$data->followup_count;
                }
                $data->save();
            }
        }

        if($lead->isDirty('next_followup') && !empty($lead->getOriginal('next_followup'))){
            // $oldStatus = isset(statusList()[$lead->getOriginal('next_followup')]) ? statusList()[$lead->getOriginal('next_followup')] : '';
            // $newStatus = isset(statusList()[$lead->next_followup]) ? statusList()[$lead->next_followup] : '';
            $msg = 'Lead next followup date change '. $lead->getOriginal('next_followup') .' to '. $lead->next_followup.' By '.admin()->user()->email;
            $insData['old_status'] = $lead->getOriginal('next_followup'); 
            $insData['new_status'] = $lead->next_followup;
            $insData['page'] = 'Follow Up';
            $insData['messages'] = $msg;
            Log::create($insData);

            $data = Lead::find($lead->id);
            if(!blank($data) && $data->status == "followup"){
                $data->followup_count = ++$data->followup_count;
                $data->save();
            }
        }

        if($lead->isDirty('job_title') && !empty($lead->getOriginal('job_title'))){
            // $oldStatus = isset(statusList()[$lead->getOriginal('job_title')]) ? statusList()[$lead->getOriginal('job_title')] : '';
            // $newStatus = isset(statusList()[$lead->job_title]) ? statusList()[$lead->job_title] : '';
            $msg = 'Lead job title change '. $lead->getOriginal('job_title') .' to '. $lead->job_title.' By '.admin()->user()->email;
            $insData['old_status'] = $lead->getOriginal('job_title'); 
            $insData['new_status'] = $lead->job_title;
            $insData['page'] = 'Job Title';
            $insData['messages'] = $msg;
            Log::create($insData);
        }

        if($lead->isDirty('job_link') && !empty($lead->getOriginal('job_link'))){
            // $oldStatus = isset(statusList()[$lead->getOriginal('job_link')]) ? statusList()[$lead->getOriginal('job_link')] : '';
            // $newStatus = isset(statusList()[$lead->job_link]) ? statusList()[$lead->job_link] : '';
            $msg = 'Lead job link change '. $lead->getOriginal('job_link') .' to '. $lead->job_link.' By '.admin()->user()->email;
            $insData['old_status'] = $lead->getOriginal('job_link'); 
            $insData['new_status'] = $lead->job_link;
            $insData['page'] = 'Job Link';
            $insData['messages'] = $msg;
            Log::create($insData);
        }

        if($lead->isDirty('project_type') && !empty($lead->getOriginal('project_type'))){
            // $oldStatus = isset(statusList()[$lead->getOriginal('project_type')]) ? statusList()[$lead->getOriginal('project_type')] : '';
            // $newStatus = isset(statusList()[$lead->project_type]) ? statusList()[$lead->project_type] : '';
            $msg = 'Lead project type change '. $lead->getOriginal('project_type') .' to '. $lead->project_type.' By '.admin()->user()->email;
            $insData['old_status'] = $lead->getOriginal('project_type'); 
            $insData['new_status'] = $lead->project_type;
            $insData['page'] = 'Project Type';
            $insData['messages'] = $msg;
            Log::create($insData);
        }

        if($lead->isDirty('portal') && !empty($lead->getOriginal('portal'))){
            // $oldStatus = isset(statusList()[$lead->getOriginal('portal')]) ? statusList()[$lead->getOriginal('portal')] : '';
            // $newStatus = isset(statusList()[$lead->portal]) ? statusList()[$lead->portal] : '';
            $msg = 'Lead portal change '. $lead->getOriginal('portal') .' to '. $lead->portal.' By '.admin()->user()->email;
            $insData['old_status'] = $lead->getOriginal('portal'); 
            $insData['new_status'] = $lead->portal;
            $insData['page'] = 'Portal';
            $insData['messages'] = $msg;
            Log::create($insData);
        }

        if($lead->isDirty('bid_quote') && !empty($lead->getOriginal('bid_quote'))){
            // $oldStatus = isset(statusList()[$lead->getOriginal('bid_budget')]) ? statusList()[$lead->getOriginal('bid_budget')] : '';
            // $newStatus = isset(statusList()[$lead->bid_budget]) ? statusList()[$lead->bid_budget] : '';
            $msg = 'Lead bid quote change '. $lead->getOriginal('bid_quote') .' to '. $lead->bid_quote.' By '.admin()->user()->email;
            $insData['old_status'] = $lead->getOriginal('bid_quote'); 
            $insData['new_status'] = $lead->bid_quote;
            $insData['page'] = 'Bid Quote';
            $insData['messages'] = $msg;
            Log::create($insData);
        }

        if($lead->isDirty('client_budget') && !empty($lead->getOriginal('client_budget'))){
            // $oldStatus = isset(statusList()[$lead->getOriginal('client_budget')]) ? statusList()[$lead->getOriginal('client_budget')] : '';
            // $newStatus = isset(statusList()[$lead->client_budget]) ? statusList()[$lead->client_budget] : '';
            $msg = 'Lead client budget change '. $lead->getOriginal('client_budget') .' to '. $lead->client_budget.' By '.admin()->user()->email;
            $insData['old_status'] = $lead->getOriginal('client_budget'); 
            $insData['new_status'] = $lead->client_budget;
            $insData['page'] = 'Client Budget';
            $insData['messages'] = $msg;
            Log::create($insData);
        }

        if($lead->isDirty('profile') && !empty($lead->getOriginal('profile'))){
            // $oldStatus = isset(statusList()[$lead->getOriginal('profile')]) ? statusList()[$lead->getOriginal('profile')] : '';
            // $newStatus = isset(statusList()[$lead->profile]) ? statusList()[$lead->profile] : '';
            $msg = 'Lead profile change '. $lead->getOriginal('profile') .' to '. $lead->profile.' By '.admin()->user()->email;
            $insData['old_status'] = $lead->getOriginal('profile'); 
            $insData['new_status'] = $lead->profile;
            $insData['page'] = 'Profile';
            $insData['messages'] = $msg;
            Log::create($insData);
        }

        if($lead->isDirty('technology') && !empty($lead->getOriginal('technology'))){
            // $oldStatus = isset(statusList()[$lead->getOriginal('technology')]) ? statusList()[$lead->getOriginal('technology')] : '';
            // $newStatus = isset(statusList()[$lead->technology]) ? statusList()[$lead->technology] : '';
            $msg = 'Lead technology change '. $lead->getOriginal('technology') .' to '. $lead->technology.' By '.admin()->user()->email;
            $insData['old_status'] = $lead->getOriginal('technology'); 
            $insData['new_status'] = $lead->technology;
            $insData['page'] = 'Technology';
            $insData['messages'] = $msg;
            Log::create($insData);
        }

        if($lead->isDirty('connects_needed') && !empty($lead->getOriginal('connects_needed'))){
            // $oldStatus = isset(statusList()[$lead->getOriginal('connects_needed')]) ? statusList()[$lead->getOriginal('connects_needed')] : '';
            // $newStatus = isset(statusList()[$lead->connects_needed]) ? statusList()[$lead->connects_needed] : '';
            $msg = 'Lead next followup date change '. $lead->getOriginal('connects_needed') .' to '. $lead->connects_needed.' By '.admin()->user()->email;
            $insData['old_status'] = $lead->getOriginal('connects_needed'); 
            $insData['new_status'] = $lead->connects_needed;
            $insData['page'] = 'Connects Needed';
            $insData['messages'] = $msg;
            Log::create($insData);
        }

    }

    /**
     * Handle the Lead "deleted" event.
     *
     * @param  \App\Models\Lead  $lead
     * @return void
     */
    public function deleted(Lead $lead)
    {
        //
    }

    /**
     * Handle the Lead "restored" event.
     *
     * @param  \App\Models\Lead  $lead
     * @return void
     */
    public function restored(Lead $lead)
    {
        //
    }

    /**
     * Handle the Lead "force deleted" event.
     *
     * @param  \App\Models\Lead  $lead
     * @return void
     */
    public function forceDeleted(Lead $lead)
    {
        //
    }
}
