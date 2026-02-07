<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Url extends Model
{
	use HasFactory, SoftDeletes;
	protected $fillable = [
		'original_url',
		'short_code',
		'clicks',
		'expires_at'
	];
	protected $casts = [
		'expires_at' => 'datetime',
	];
	protected $dates = ['deleted_at'];
}
