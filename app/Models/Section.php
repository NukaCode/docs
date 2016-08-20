<?php

namespace App\Models;

class Section extends BaseModel
{
    protected $table = 'sections';

    protected $fillable = [
        'chapter_id',
        'name',
        'number',
        'path',
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'id');
    }

}
