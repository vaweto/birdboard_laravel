<?php

namespace App;

trait RecordsActivity
{
    public  $old_attributes = [];

    public static function bootRecordsActivity()
    {


        foreach (self::recortableEvents() as $event) {
            static::$event(function ($model) use ($event) {

                $model->recordActivity($model->activityDescription($event));

            });

            if($event === 'updated') {
                static::updating(function ($model) {
                    $model->old_attributes = $model->getOriginal();
                });
            }
        }
    }

    protected function activityDescription($description)
    {
        return "{$description}_" .strtolower(class_basename($this));
    }

    protected static function recortableEvents() {
        if (isset(static::$recordableEvents)) {
            return static::$recordableEvents;
        }

        return ['created', 'updated', 'deleted'];

    }

    public function activity()
    {
        if (get_class($this) === Project::class) {
            return $this->hasMany(Activity::class)->latest();
        }
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    public function recordActivity($description)
    {
        $this->activity()->create([
            'description' => $description,
            'changes' => $this->activityChanges(),
            'project_id' => class_basename($this) === 'Project' ? $this->id : $this->project_id
        ]);

    }

    public function activityChanges()
    {
        if($this->wasChanged()) {
            return  [
                'before' => array_diff($this->old_attributes, $this->getAttributes()),
                'after' => $this->getChanges()
            ];
        }
    }

}