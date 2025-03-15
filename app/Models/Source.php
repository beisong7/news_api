<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'base_url',
        'api_key',
        'has_pagination',
        'uses_header',
        'method',
        'active',
        'api_key_param',
        'default_params',
    ];

    public function fieldMappings(){
        return $this->hasMany(SourceFieldMapping::class);
    }

    public function news(){
        return $this->hasMany(News::class);
    }
}
