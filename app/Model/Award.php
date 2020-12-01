<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }
}
