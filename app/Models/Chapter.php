<?php

namespace App\Models;

class Chapter extends BaseModel
{
    protected $table = 'chapters';

    protected $fillable = [
        'version_id',
        'name',
        'number',
        'path',
    ];

    public function version()
    {
        return $this->belongsTo(Version::class, 'id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'chapter_id');
    }

}
