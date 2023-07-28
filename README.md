#  RealTime Chat Using Laravel.
### Here you will find summary steps to implement realTime Chat app using laravel 
##### Note : In this repo , I 've implemented the required role of backend only . I avoided the frontend role.
___________________________________________________________________________________
First , Let's suppose There is a request comming from mobile application has the message written by the user, So in api.php file I prepared the routes for that like this below .. 

            Route::post("SendMessage",[\App\Http\Controllers\ChatController::class,"SendMessage"]);
            Route::get("load",[\App\Http\Controllers\MessagesController::class,"LoadThePreviousMessages"]);

Surely , after that you need to create ChatController to process that request .

As below , I 've created the chat controller class and the function SendMessage inside it .. 

            <?php
            
            namespace App\Http\Controllers;
            
            use App\Events\MessageSent;
            use App\Events\SendMessageEvent;
            use App\Http\Requests\SendMessageRequest;
            use App\Models\Chat;
            use App\Models\Message;
            use App\Models\User;
            use Illuminate\Http\Request;
            use Illuminate\Support\Facades\DB;
            
            class ChatController extends Controller
            {
                public function SendMessage(SendMessageRequest $request)
                  {
                  }
            }

The "SendMessage" function is responsible for :-
    1. Make sure of the validity of comming id from the request.
    2. Make sure if there is previous chat between two users or not.
        . If there is not , the function will create a new chat in databse for them.
        . If there is previous chat , the function will only add the message to the data base.
    3.broadcast the message to the event .and from the event to pusher.

_____________________________________________________________________________________________________
There is the "IsTherePreviousChat" Function 



        public function IsTherePreviousChat($OtherUserId,$user_id){
            $collection = Message::whereHas('chat' , function($q) use ($OtherUserId,$user_id){
                $q->where('from_user',$OtherUserId)
                ->where('to_user', $user_id);
        })->orWhere(function ($q) use ($OtherUserId,$user_id) {
                $q->where('from_user',$user_id)
                ->where('to_user', $OtherUserId);
        })->get();

        if (count($collection) > 0){
            return $collection;
        }
        return false;
    }

_____________________________________________________________________________________________________
Let's go to another part , Now we will create the MessagesController That will load the previous messages of the chats between Users..
Note the difference between the function that check only if there is previous chat in the ChatController ... and the function that load the previous messages in the messages controller ..

In messagesController You will find many ways to implement the function ..
    1.loading if the request has id of both sender and reciever.
    2.loading if the chat_id is only in the request.
    3.loading with scrolling
_____________________________________________________________________________________________________
The most important part of this repo is comming, Here you will find the event that send your message to pusher 
                <?php
                
                namespace App\Events;
                
                use App\Models\Message;
                use Illuminate\Broadcasting\Channel;
                use Illuminate\Broadcasting\InteractsWithSockets;
                use Illuminate\Broadcasting\PresenceChannel;
                use Illuminate\Broadcasting\PrivateChannel;
                use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
                use Illuminate\Foundation\Events\Dispatchable;
                use Illuminate\Queue\SerializesModels;
                
                class SendMessageEvent implements ShouldBroadcast
                {
                    use Dispatchable, InteractsWithSockets, SerializesModels;
                
                
                    private array $arr ;
                
                
                    public function __construct($message)
                    {
                        $this->arr = $message;
                    }
                
                
                    public function broadcastOn()
                    {
                        return new PrivateChannel('chat');
                    }
                
                    public function broadcastWith()
                    {
                        return $this->arr;
                    }
                }
______________________________________________________
Don't forget to change the BROADCAST_DRIVER IN .env file from log to pusher ...
