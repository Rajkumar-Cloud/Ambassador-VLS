<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\ambassadorLogin;
use App\ambassadorUser;
use Mail;
use App\Http\Traits\sendMail;
class webLoginController extends Controller
{
    
    public function validateUser(Request $request)
    {
        $date = date('Y-m-d');  
        $check_user = ambassadorUser::select('*')->where('email',$request->email)->where('password',$request->password)->get();
        if(count($check_user)>0)
        {
        $Login = new ambassadorLogin();
        $Login->user_id = $check_user[0]['user_id'];
        $Login->role_id = $check_user[0]['role_id'];
        $Login->email = $check_user[0]['email'];
        $Login->status = 1;
        $Login->date = $date;
        $Login->save();
        $user_detail = array(
            'role_id' => $check_user[0]['role_id'],
            'user_id' => $check_user[0]['user_id'],
            'email' => $check_user[0]['email'],
            'name' => $check_user[0]['name'],
            'mobile' => $check_user[0]['mobile']
        );
        $json_array = ['type'=>'success','message'=>'Login successfully','user_detail'=>$user_detail, 'code'=>'201'];
        }
        else{
            $json_array = ['type'=>'error','message'=>'Invalid email or password','code'=>'401'];
        }
        return json_encode($json_array);
    }
}
