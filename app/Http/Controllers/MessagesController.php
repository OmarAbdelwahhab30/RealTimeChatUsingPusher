<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class MessagesController extends Controller
{

    public function LoadThePreviousMessages(Request $request)
    {


        /*
         *
         * loading if the request has id of both sender and reciever
         *
         * */
        return Message::where(function($query) use ($request) {
            $query->where('from_user', auth("sanctum")->user()->id)->where('to_user', $request->other);
        })->orWhere(function ($query) use ($request) {
            $query->where('from_user', $request->other)->where('to_user', auth("sanctum")->user()->id);
        })->orderBy('created_at', 'ASC')->limit(10)->get();



        /*
         * loading if the chat_id is only in the request
         * */

        return Chat::where("id",$request->chat_id)->with(["messages" => function($q) use ($request){
            $q->where("messages.chat_id",$request->chat_id)->orderBy("id", "asc");
        }])->get();

        /*
         *
         * loading with scrolling
         *
         * **/

//        if(!$request->old_message_id || !$request->to_user)
//            return;
//        $message = Message::find($request->old_message_id);
//        $lastMessages = Message::where(function($query) use ($request, $message) {
//            $query->where('from_user', Auth::user()->id)
//                ->where('to_user', $request->to_user)
//                ->where('created_at', '<', $message->created_at);
//        })
//            ->orWhere(function ($query) use ($request, $message) {
//                $query->where('from_user', $request->to_user)
//                    ->where('to_user', Auth::user()->id)
//                    ->where('created_at', '<', $message->created_at);
//            })
//            ->orderBy('created_at', 'ASC')->limit(10)->get();

    }
}
