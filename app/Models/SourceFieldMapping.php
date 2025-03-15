<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceFieldMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'source_field',
        'target_field',
        'path'
    ];

    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}
