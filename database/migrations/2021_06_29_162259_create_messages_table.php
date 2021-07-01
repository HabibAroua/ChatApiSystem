<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	class CreateMessagesTable extends Migration
	{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
			Schema::create
			(
				'messages', function (Blueprint $table) 
				{
					$table->bigIncrements('id');
					$table->integer('id_sender');
					$table->integer('id_receiver');
					$table->text('content');
					$table->date('date_message');
					$table->unsignedInteger('conversation_id');
					//$table->foreign('id_conversation')->references('id')->on('conversations')->onDelete('cascade');
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
			Schema::dropIfExists('messages');
		}
	}
?>