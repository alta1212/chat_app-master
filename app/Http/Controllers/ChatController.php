<?php

namespace App\Http\Controllers;

use App\Models\boxchat;
use Illuminate\Support\Facades\DB;
use App\Models\friends;
use App\Models\message;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use App\Events\sendmes;
class ChatController extends Controller
{

    function index()
    {
       if(session()->has("id"))
       { 
       
         //$value = session()->get('id');
         $dataUser=[
            "userInfo"=> User::where('userid','=',session()->get('id'))->first(),
            "listFriend"=> boxchat::where('user1',session()->get('id'))
            ->orWhere('user2',session()->get('id'))
            ->get(),
            "listChat"=>User::whereIn('userid',function($query) {
               $query->from('boxchat')
               ->whereNotIn('chanel',function($query) {
                  $query->from('boxChatMessageStatus')
               ->select('chanel');
            })->where('userid','!=',session()->get('id'))
               ->select('user1');
            })
            ->orWhereIn('userid',function($query) {
                     $query->from('boxchat')
                           ->whereNotIn('chanel',function($query) {
                              $query->from('boxChatMessageStatus')
                           ->select('chanel');
                        })->where('userid','!=',session()->get('id'))
                           ->select('user2');
                        })->select('userid','name','avata')
            ->get()
         ];
        
       }
       return view('main.chat',$dataUser);
    }
    function logout()
    {
 
      if(session()->has("id"))
      {
         session()->pull("id");
        
         return redirect('/');
      }
    }

    function filetrans(Request $r)
    {
     
      $fileName = time().'_'. $r->file->getClientOriginalName();
      $file= $r->file->storeAs('upload/file/'.session()->get('id'), $fileName, 'public');
      try{
         message::insert(
            [
               'sender' =>session()->get('id'),
               'type'=>'file',
               'chanel'=>$r->chanel.'_'.session()->get('id'),
               'time'=>Carbon::now()->toDateString(),
               'content'=>$file
            ]
         );
      }
      catch(\Exception $e)
      {
         message::insert(
            [
               'sender' =>session()->get('id'),
               'type'=>'file',
               'chanel'=>session()->get('id').'_'.$r->chanel,
               'time'=>Carbon::now()->toDateString(),
               'content'=>$file
            ]
         );
         
      }
   }
    function sendMes(Request $r){
     
      $r->validate([
         'boxchat'=>'required|max:20',
         'message'=>'required',
      ]);

     
      try{
         $query=message::insert(
            [
               'sender' =>session()->get('id'),#mail           
               'content' =>$r->message,
               'type'=>'text',
               'chanel'=>session()->get('id').'_'.$r->boxchat,
               'time'=>Carbon::now()->toDateString(),
            ]
         );
      }
      catch(\Exception $e)
      {
         message::insert(
            [
               'sender' =>session()->get('id'),#mail           
               'content' =>$r->message,
               'type'=>'text',
               'chanel'=>$r->boxchat.'_'.session()->get('id'),
               'time'=>Carbon::now()->toDateString(),
            ]
         );
      }
      event(new sendmes($r->message,session()->get('id').'_'.$r->boxchat));
      event(new sendmes($r->message,$r->boxchat.'_'.session()->get('id')));
      
    }
    function loadMessage(Request $r)
    { 

      $data=[

         message::where('chanel',$r->chanel.'_'.session()->get('id'))->orWhere('chanel',session()->get('id').'_'.$r->chanel)
         ->orderBy('time','DESC')
         ->get()
      ];
      return $data;
    }
}
   