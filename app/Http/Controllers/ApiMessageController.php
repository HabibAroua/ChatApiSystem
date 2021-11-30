<?php

	namespace App\Http\Controllers;

	use App\Message;
    use Illuminate\Http\Request;
	use App\Chat;
	use App\User;
	use App\MobileUser;
	use App\Conversation;
	use App\Events\ChatEvent;
    use JetBrains\PhpStorm\ArrayShape;
    use JetBrains\PhpStorm\Pure;

    class ApiMessageController extends Controller
	{
		private $user;
		private $conversation;
		private $userMobile;

        private function isValidEmail($email): bool
        {
            if (filter_var($email, FILTER_VALIDATE_EMAIL))
                return true;
            return false;
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
                return response()->json(["message"=>"You should enter a valid email format"],406);
			else
            {
                $userM = MobileUser::where('email',$request->email)->get()->first();
                if(isset($userM))
                    return response()->json(["message"=>"This user is already exist"],406);
                else
                {
                    $this->userMobile = new MobileUser();
                    $this->userMobile->email = strtolower($request->email);
                    $this->userMobile->first_name = ucfirst($request->first_name);
                    $this->userMobile->last_name = ucfirst($request->last_name);
                    $this->userMobile->created_at = date('Y-m-d H:i:s');
                    $this->userMobile->updated_at = date('Y-m-d H:i:s');
                    $this->userMobile->save();
                    return response()->json
					(
					    [
					        "message" => "The insertion of a new user is carried out with success"
						],200
					);
                }
            }
		}

		private function start_conversation($id1, $id2)
		{
			$this->conversation = new Conversation();
			if($id1<$id2)
				$this->conversation->users = json_encode(array("id1" => $id1, "id2" => $id2));
			else
				if($id1>$id2)
					$this->conversation->users = json_encode(array("id1" => $id2, "id2" => $id1));
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
			$this->conversation = Conversation::where('users' , json_encode(array("id1" => $request->id_sender, "id2" => $request->id_receiver)))
					->OrWhere('users' , json_encode(array("id1" => $request->id_receiver, "id2" => $request->id_sender)))->get()->first();
			if(isset($this->conversation))
			{
				$this->insertMessage($request->id_sender, $request->id_receiver, $request->contents, $this->conversation->id);
				return ['status' => 'success'];
			}
			$this->start_conversation($request->id_sender, $request->id_receiver);
			$this->insertMessage($request->id_sender, $request->id_receiver, $request->contents, $this->conversation->id);
			return ['status' => 'success'];
		}

		public function allMobileUser()
		{
			return MobileUser::all();
		}

		public function getConversationByUser($id)
        {
            $this->conversation = Conversation::where('users','like','%\"'.$id.'\"%')->get();
            if(count($this->conversation) == 0)
                return response()->json(["message"=>"There is no any conversation"],404);
            else
            {
                $tab = array();
                foreach ($this->conversation as $v)
                    $tab[] = array("conversation" => $v, "last_message" => Message::where('conversation_id',$v['id'])->get()->last()->content);
                return $tab;
            }
        }

        public function allConversations($id)
        {
            $listConversation = Conversation::all();
            $myConversation = [];
            foreach ($listConversation as $v)
            {
                $usersIdS = json_decode($v->users);
                if($id == $usersIdS->id1)
                {
                    $messages = Message::where('conversation_id',$v->id)->orderBy('created_at', 'DESC')->get();
                    $myConversation[] = array
                    (
                        "user" => MobileUser::find($usersIdS->id2),
                        "messages" => $messages,
                        "last_message" => $messages->first(),
                        "time" => $messages->first()->created_at,
                        "message_id" => $messages->first()->id
                    );
                }
                elseif ($id == $usersIdS->id2)
                {
                    $messages = Message::where('conversation_id',$v->id)->orderBy('created_at', 'DESC')->get();
                    $myConversation[] = array
                    (
                        "user" => MobileUser::find($usersIdS->id1),
                        "messages" => $messages,
                        "last_message" => $messages->first(),
                        "time" => $messages->first()->created_at,
                        "message_id" => $messages->first()->id
                    );
                }
            }
            $time = array_column($myConversation, 'time');
            array_multisort($time, SORT_DESC, $myConversation);
            return $myConversation;
            return $myConversation;
            $json = response()->json($myConversation);
            //return $json->ord;
        }

        public function findMyConversation($id)
        {
            $tab=[];
            foreach($this->allConversations() as $v)
            {
                $tabUser = json_decode($v->users);
                if (($tabUser['id1'] == $id) OR ($tabUser['id2'] == $id))
                    $tab[] = $v;
                return response()->json($tab, 200);
            }
        }

		public function fetchAllMessages()
        {
		    return Message::all();
		}
	}
?>
