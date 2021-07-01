<?php

	namespace App;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Notifications\Notifiable;

	class Message extends Model
	{
		protected $fillable = [
				'id_sender', 'id_receiver', 'content','conversation_id', 'date_message',
			];
		public function conversation()
		{
			return $this->belongsTo(Conversation::class, 'id');
		}
	}

?>