<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'languages';
    public $timestamps = true;
    protected $guarded = [];
    protected $fillable = ["code", "title"];


}
