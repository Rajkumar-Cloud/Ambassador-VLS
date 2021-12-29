<?php
namespace App\Http\Traits;
use Illuminate\Http\Request;
use App\ambassadorUser;
use Mail;
trait sendMail
{
    public function send_m(Request $request, $data){
        Mail::send($mail_blade,['user_id'=>$user_id,
        'first_name' => $first_name
        ], function($message)
        
       
            use ($mail_id)
            {
                $message->to($mail_id)->subject('Reset Password');
                
            });
  
        
        }
}