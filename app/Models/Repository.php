<?php

namespace App\Models;

class Repository extends BaseModel
{
    protected $table = 'repositories';

    protected $fillable = [
        'name',
        'description',
        'icon',
        'github_url',
        'git_url',
        'packagist_url',
    ];

    public function getLatestVersionAttribute()
    {
        return $this->versions()->where('name', '!=', 'master')->orderBy('latest_release', 'desc')->first()->name;
    }

    public function getLatestReleaseAttribute()
    {
        return $this->versions->where('name', '!=', 'master')->orderBy('latest_release', 'desc')->first()->latest_release;
    }

    public function versions()
    {
        return $this->hasMany(Version::class, 'repository_id');
    }
}
