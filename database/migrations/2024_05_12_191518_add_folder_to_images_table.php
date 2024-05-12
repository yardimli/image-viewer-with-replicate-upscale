<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void
		{
			Schema::table('my_images', function (Blueprint $table) {
				$table->string('folder')->nullable()->after('image_name'); // Adds a 'folder' column after 'image_name'
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void
		{
			Schema::table('my_images', function (Blueprint $table) {
				$table->dropColumn('folder'); // Drops the 'folder' column
			});
		}
	};
