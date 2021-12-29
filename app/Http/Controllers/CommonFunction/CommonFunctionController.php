<?php

//Master Created By Karthick 04-06-2021 to writecomman function to use any place

namespace App\Http\Controllers\CommonFunction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Str;
use Mail;

// use Illuminate\Http\Request;

class CommonFunctionController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    public function get_token() {

        return md5(rand(0, 60) . date('Y-m-d H:i:s')) . Str::random(50);
    }

    function decode($id) {
        if ($id) {

            $afterdecode = base64_decode($id);
            $sremove = substr($afterdecode, 0, -64);
            $ge64 = substr($afterdecode, -64);
            $ge_last_32 = substr($ge64, -32);
            $ge_first_32 = substr($ge64, 0, 32);
            if ((md5('125ad4d85f5gh21') == $ge_first_32) && (md5('lodkel145djd129') == $ge_last_32)) {
                return $sremove;
            } else {
                return false;
            }
        }
    }

    function encode($id) {
        if ($id) {
            $str = $id . md5('125ad4d85f5gh21') . md5('lodkel145djd129');
            $decode = base64_encode($str);
        }
        return $decode;
    }

    public function isJson($data) {
        return (json_decode($data) != NULL) ? true : false;
    }
    public function send_m($mail_blade,$subject,$data){
        $mail_id = $data['email'];
        Mail::send($mail_blade,['data'=>$data], function($message)
        
       
            use ($mail_id,$subject)
            {
                $message->to($mail_id)->subject($subject);
                
            });
  
        
        }

}

