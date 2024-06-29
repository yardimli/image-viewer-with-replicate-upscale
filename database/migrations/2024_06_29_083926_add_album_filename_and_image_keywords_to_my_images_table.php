<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration
	{
		public function up()
		{
			Schema::table('my_images', function (Blueprint $table) {
				$table->string('album_filename')->nullable();
				$table->text('image_keywords')->nullable();
			});
		}

		public function down()
		{
			Schema::table('my_images', function (Blueprint $table) {
				$table->dropColumn('album_filename');
				$table->dropColumn('image_keywords');
			});
		}
	};
