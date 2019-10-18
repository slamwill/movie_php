<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AvUserVideo extends Model
{
    //
    public function AvVideo()
    {
        return $this->hasMany('\App\AvVideo', 'video_id');
    }

}
