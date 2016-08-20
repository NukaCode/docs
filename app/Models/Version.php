<?php

namespace App\Models;

class Version extends BaseModel
{
    protected $table = 'versions';

    protected $fillable = [
        'repository_id',
        'name',
        'latest_release',
    ];

    public function repository()
    {
        return $this->belongsTo(Repository::class, 'id');
    }

}
