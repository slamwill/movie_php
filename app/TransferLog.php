<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferLog extends Model
{
	protected $hidden = ['logs'];
    protected $casts = [
        'json' => 'array',
        'logs' => 'array',
    ];
    //
	public function user() {
		return $this->belongsTo('App\User');
	}
}
