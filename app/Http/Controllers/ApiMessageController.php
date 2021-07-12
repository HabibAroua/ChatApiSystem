<?php

	namespace App\Http\Controllers;

	use Illuminate\Http\Request;
	use App\Chat;
	use App\User;
	use App\MobileUser;
	use App\Conversation;
	use App\Events\ChatEvent;

	class ApiMessageController extends Controller
	{
		private $user;
		private $conversation;
		private $userMobile;

        private function isValidEmail($email)
        {
            if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $email))
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        public function __construct()
		{
			$this->userMobile = new MobileUser();
			$this->user = new User();
			$this->conversation = new Conversation();
		}

		public function CreateUser(Request $request)
		{
			if($this->isValidEmail(strtolower($request->email))!=1)
            {
                return response()->json(["message"=>"You should enter a valid email format"],406);
            }
			else
            {
                $userM = MobileUser::where('email',$request->email)->get()->first();
                if(isset($userM))
                {
                    return response()->json(["message"=>"This user is already exist"],406);
                }
                else
                {
                    $this->userMobile = new MobileUser();
                    $this->userMobile->email = strtolower($request->email);
                    $this->userMobile->first_name = ucfirst($request->first_name);
                    $this->userMobile->last_name = ucfirst($request->last_name);
                    $this->userMobile->created_at = date('Y-m-d H:i:s');
                    $this->userMobile->updated_at = date('Y-m-d H:i:s');
                    $this->userMobile->save();
                    return response()->json(["message"=>"The insertion of a new user is carried out with success"],200);
                }
            }
		}

		private function start_conversation($id1, $id2)
		{
			$this->conversation = new Conversation();
			if($id1<$id2)
			{
				$this->conversation->users = json_encode(array(["id1" => $id1, "id2" => $id2]));
			}
			else
			{
				if($id1>$id2)
				{
					$this->conversation->users = json_encode(array(["id1" => $id2, "id2" => $id1]));
				}
			}
			$this->conversation->save();
		}

		private function insertMessage($id_sender, $id_receiver, $content, $conversation_id)
		{
			$chat = $this->conversation->messages()->create
			(
				[
					'id_sender' => $id_sender,
					'id_receiver' => $id_receiver,
					'content' => $content,
					'conversation_id' => $conversation_id,
					'date_message' => date('Y-m-d H:i:s'),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				]
			);
			broadcast(new ChatEvent($chat->load('conversation')))->toOthers();
		}

		public function sendMessageOneToOne(Request $request)
		{
			$this->conversation = Conversation::where('users' , json_encode(array(["id1" => $request->id_sender, "id2" => $request->id_receiver])))
					->OrWhere('users' , json_encode(array(["id1" => $request->id_receiver, "id2" => $request->id_sender])))->get()->first();
			if(isset($this->conversation))
			{
				$this->insertMessage($request->id_sender, $request->id_receiver, $request->contents, $this->conversation->id);
				return ['status' => 'success'];
			}
			else
			{
				$this->start_conversation($request->id_sender, $request->id_receiver);
				$this->insertMessage($request->id_sender, $request->id_receiver, $request->contents, $this->conversation->id);
				return ['status' => 'success'];
			}
		}

		public function allMobileUser()
		{
			return MobileUser::all();
		}
	}
?>
