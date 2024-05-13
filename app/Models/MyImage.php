<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;

	class MyImage extends Model
	{
		use HasFactory;

		protected $fillable = ['image_name', 'folder', 'notes', 'upscale_name', 'upscale_result'];
	}
