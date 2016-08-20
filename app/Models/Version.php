<?php

namespace App\Models;

class Version extends BaseModel
{
    protected $table = 'versions';

    protected $fillable = [
        'repository_id',
        'name',
        'latest_release',
        'sha',
        'commit_hash',
    ];

    public function getPathAttribute()
    {
        return base_path('resources/docs/'
                         . $this->repository->name . '/'
                         . $this->name . '/docs');
    }

    public function repository()
    {
        return $this->belongsTo(Repository::class, 'repository_id');
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'version_id');
    }
}
