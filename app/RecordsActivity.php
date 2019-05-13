<?php

namespace App;

trait RecordsActivity
{
    public  $old = [];

    public function activity()
    {
        return $this->morphMany(Activity::class,'subject')->latest();
    }

    public function recordActivity($description = '')
    {

        $this->activity()->create([
            'description' => $description,
            'changes' => $this->activityChanges(),
            'project_id' => (class_basename($this)  === 'Project') ? $this->id : $this->project_id,
        ]);

    }

    public function activityChanges()
    {
        if($this->wasChanged()) {
            return  [
                'before' => array_diff($this->old, $this->getAttributes()),
                'after' => $this->getChanges()
            ];
        }

    }

}