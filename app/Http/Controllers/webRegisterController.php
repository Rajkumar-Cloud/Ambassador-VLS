<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\ambassadorUser;
use App\userType;
use Illuminate\Support\Str;
use Mail;
use App\Http\Traits\sendMail;
use App\Notification;

class webRegisterController extends Controller
{
    use sendMail;
    public function userRegister(Request $request)
    {
        // // $refCode = isset($request->ref_code);
        // if(isset($request->ref_code)){
        //     // detail for based on ref code (name and code)
        //     // req details (name)
        //     // get all admin records
        //     // foreach for mail
        //     // block active btn
        // }
        $check_user = ambassadorUser::select('*')->where('email',$request->email)->get();
        if(count($check_user) <= 0)
        {
            
        $date = date('Y-m-d');
       // $name =  $request['first_name'] . $request['last_name'];
        $User = new ambassadorUser();
        $User->first_name = $request['first_name'];
        $User->last_name = $request['last_name'];
        $User->DOB = $request['DOB'];
        $User->gender = $request['gender'];
        $User->current_occupation = $request['current_occupation'];
        $User->experience = $request['experience'];
        $User->mobile_number = $request['mobile_number'];
        $User->email = $request['email'];
        $User->password = $request['mobile_number'];
        $User->aadhar_number = $request['aadhar_number'];
        $User->pan_number = $request['pan_number'];
        $User->address = $request['address'];
       // $User->role_id = $request['role_id'];
        $User->declaration = $request['declaration'];
        $User->accept_terms = $request['accept_terms'];
        $User->date = $date;
        $User->save();
        // if($request['role_id'] == 3)
        //     {
        if($User->id)
        {
            $mail_blade = "mailToAmbassador";
            $mail_id = $request->email;
            $first_name = $request['first_name'];
            $user_id = $User->id;
            $data = ['user_id'=>$user_id];
            $subject = "New Ambassador Registered";
            $send = $this->send_m($request, $request->email, $User->id, $mail_blade, $subject, $first_name);
        }
        $notification_msg = "New ambassador registered";
                    $notify = new Notification();  // notification insert
                    $notify->role_id = 3;
                    $notify->user_id = $user_id;//need to change admin id
                    $notify->message = $notification_msg;
                   $Res = $notify->save();
                    if($notify->save())
                    {
                        $admin_detail = ambassadorUser::select('*')->where('role_id',1)->first();
                        
                    $mail_blade = "notificationMailToAdmin";
            $mail_id = $admin_detail->email;
            $first_name = $request['first_name'];
            $user_id = $User->id;
            $data = ['user_id'=>$user_id];
            $subject = "New Ambassador Registered";
            $send = $this->send_m($request, $mail_id, $User->id, $mail_blade, $subject, $first_name);
                    }

        $random_code = Str::random(6);
        $referal_code = $random_code . $User->id;
        $add_referal_code = ambassadorUser::where('user_id',$User->id)->update(['referal_code'=>
        $referal_code]);
       
                
                
               // }
                $json_array = ['type'=>'success', 'message'=>'Registered successfully','code'=>'201'];
        }
        else{
            $json_array = ['type'=>'error', 'message'=>'Email already exists','code'=>'401'];

        }
        return json_encode($json_array);
    }
    public function usertype()
    {
        $user_type = userType::select('*')->get();
        return $user_type;
    }
    public function occupation()
    {
        $occupation = ambassador_occupation::select('*')->get();
        return $occupation;
    }
    public function notificationMessage()
    {
        $message = Notification::select('*')->where('status',1)->get();
        return $message;
    }
    public function ambassadorApproval(Request $request)
    {
        
        $check_user = ambassadorUser::select('*')->where('id',$request->user_id)->get();
        $update_status = ambassadorUser::where('id',$request->user_id)->update(['status'=>
        1]);
        if($update_status)
        {
            $mail_blade = "credentialMailToAmbassador";
            $mail_id = $check_user[0]->email;
            $first_name = $check_user[0]->first_name;
            $user_id = $request->user_id;
            $subject = "Veranda credentials";
            $data = [$mail_id,$user_id,$mail_blade,$subject,$first_name];
            $send = $this->send_m($request,$data);
        }
        $json_array = ['type' => 'success', 'message' => 'Approved successfully', 'code' => '201'];
        return json_encode($json_array);
    }
}
