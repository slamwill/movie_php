<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AvVideo extends Model
{

	//protected $fillable = ['id', 'avkey', 'title', 'content', 'is_free', 'duration', 'views', 'video_source', 'video_type', 'origin_cover'];
	protected $fillable = ['id', 'avkey', 'title', 'content', 'is_free', 'duration', 'views', 'video_source', 'video_type', 'origin_cover', 'enable'];

	//protected $guarded = ['tags'];
	//public function AvTags(){

	//	return $this->belongsToMany(\App\AvTag::class);
	//}
    public function hasManyAvVideoTag()
    {
        return $this->hasMany('\App\AvVideoTag', 'video_id');
    }


    public function getDurationAttribute($value)
    {
		if (is_numeric($value)) {
			$hours = floor($value / 3600);
			$mins = floor($value / 60 % 60);
			$secs = floor($value % 60);
			$duration  = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
			return $duration;
		}
		return $value;
    }

    public function setDurationAttribute($value)
    {
		sscanf($value, "%d:%d:%d", $hours, $minutes, $seconds);
		$duration = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
        $this->attributes['duration'] = $duration;
    }

    public function setActorsAttribute($value)
    {
        $this->attributes['actors'] = $value ? implode(',', $value) : null;
    }

	public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = $value ? implode(',', $value) : null;
    }


}
