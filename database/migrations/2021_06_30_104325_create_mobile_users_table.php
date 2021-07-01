<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	class CreateMobileUsersTable extends Migration
	{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
			Schema::create
			('mobile_users', function (Blueprint $table) 
				{
					$table->bigIncrements('id');
					$table->string('email')->unique();
					$table->string('first_name');
					$table->string('last_name');
					$table->timestamps();
				}
			);
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down()
		{
			Schema::dropIfExists('mobile_users');
		}
	}
?>