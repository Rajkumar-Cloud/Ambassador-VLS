<?php

//Api Created By Karthick 04-06-2021 to writecomman function to use any place

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CommonFunction\CommonFunctionController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\AmbassadorUser;
use Mail;
use App\Http\Traits\sendMail;
use Validator;

// use Illuminate\Http\Request;

class ApiController extends Controller {

    use sendMail;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function login(Request $request) {
        $common = new CommonFunctionController();
        if ($common->isJson($request->getContent())) {
            $data = json_decode($request->getContent());
            $datas = get_object_vars($data);
            $username = $password = $deviceId = $osType = '';
            if ($datas['userName']) {
                $username = $datas['userName'];
            }
            if ($datas['password']) {
                $password = $common->encode($datas['password']);
            }
            if ($datas['deviceId']) {
                $deviceId = $datas['deviceId'];
            }
            if ($datas['osType']) {
                $osType = $datas['osType'];
            }
            if ($username && $password && $deviceId && $osType) {

                $auth = AmbassadorUser::where([
                            'user_name' => $username,
                            'password' => $password,
                        ])->first();
                if ($auth) {
                    if ($auth['status'] == 1) {
                        $api_key = $common->get_token();
                        $input = [
                            'api_key' => $api_key
                        ];
                        AmbassadorUser::where('id', $auth['id'])
                                ->update($input);
                        $user = array();
                        $user['id'] = $auth['id'];
                        $user['role_id'] = $auth['role_id'];
                        $user['first_name'] = $auth['first_name'];
                        $user['last_name'] = $auth['last_name'];
                        $user['age'] = $auth['age'];
                        $user['gender'] = $auth['gender'];
                        $user['email'] = $auth['email'];
                        $user['mobile_number'] = $auth['mobile_number'];
                        $user['aadhar_number'] = $auth['aadhar_number'];
                        $user['pan_number'] = $auth['pan_number'];
                        $user['address'] = $auth['address'];
                        $response = array('loginSuccessful' => true, 'code' => '200', 'authenticationToken' => $api_key, 'userDetails' => $user);
                    } else {
                        $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'your account has been disabled');
                    }
                } else {
                    $response = array('loginSuccessful' => 'false', 'code' => '401', 'message' => 'Bad Credentials');
                }
            } else {
                if (empty($username)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'UserName is Empty');
                }
                if (empty($password)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Password is Empty');
                }
                if (empty($deviceId)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'DeviceId is Empty');
                }
                if (empty($osType)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'osType is Empty');
                }
            }
        } else {

            $response = array('requestSuccessful' => false, 'code' => '400', 'message' => 'JSON is Not Valid');
        }
        return response($response, $response['code'])
                        ->header('Content-Type', 'application/json');
    }

    public function dashboard(Request $request) {
        $common = new CommonFunctionController();
        if ($common->isJson($request->getContent())) {
            $data = json_decode($request->getContent());
            $datas = get_object_vars($data);
            $userid = $api_key = '';
            if ($datas['userId']) {
                $userid = $datas['userId'];
            }
            if ($datas['authenticationToken']) {
                $api_key = $datas['authenticationToken'];
            }

            if ($userid && $api_key) {

                $auth = AmbassadorUser::where([
                            'id' => $userid,
                            'api_key' => $api_key,
                        ])->first();
                if ($auth) {
                    if ($auth['status'] == 1) {
                        $dashboard = array();
                        $dashboard['count1'] = '30';
                        $dashboard['count2'] = '50';
                        $dashboard['count3'] = '10';
                        $dashboard['count4'] = '5';

                        $response = array('loginSuccessful' => true, 'code' => '200', 'dashboard' => $dashboard);
                    } else {
                        $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'your account has been disabled');
                    }
                } else {
                    $response = array('loginSuccessful' => 'false', 'code' => '401', 'message' => 'Unauthorized');
                }
            } else {
                if (empty($userid)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'UserId is Empty');
                }
                if (empty($api_key)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'API Key is Empty');
                }
            }
        } else {

            $response = array('requestSuccessful' => false, 'code' => '400', 'message' => 'JSON is Not Valid');
        }
        return response($response, $response['code'])
                        ->header('Content-Type', 'application/json');
    }

    public function forgot_password(Request $request) {
        $common = new CommonFunctionController();
        if ($common->isJson($request->getContent())) {
            $data = json_decode($request->getContent());
            $datas = get_object_vars($data);
            $userName = $api_key = '';
            if ($datas['userName']) {
                $userName = $datas['userName'];
            }


            if ($userName) {

                $auth = AmbassadorUser::where([
                            'user_name' => $userName,
                        ])->first();
                if ($auth) {
                    if ($auth['status'] == 1) {
                        //mail  start
						$mail_blade = "forgotPassword";	
                        $mail_id = $auth['email'];	
                        $first_name = $auth['first_name'];	
                        $user_id = $auth['user_id'];	
                        $org_password = $auth['org_password'];
                        $subject = "Reset password request";
                        $data = array("email"=>$mail_id,'id'=>$user_id,'first_name'=>$first_name);	
                        $send = $common->send_m($mail_blade, $subject, $data);	
                        // mail  end

                        $response = array('loginSuccessful' => true, 'code' => '200', 'message' => "Password has been sent your mail");
                    } else {
                        $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'your account has been disabled');
                    }
                } else {
                    $response = array('loginSuccessful' => 'false', 'code' => '401', 'message' => 'Unauthorized');
                }
            } else {
                if (empty($userName)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'UserName is Empty');
                }
            }
        } else {

            $response = array('requestSuccessful' => false, 'code' => '400', 'message' => 'JSON is Not Valid');
        }
        return response($response, $response['code'])
                        ->header('Content-Type', 'application/json');
    }

    public function profile_update(Request $request) {
        $common = new CommonFunctionController();
        if ($common->isJson($request->getContent())) {
            $data = json_decode($request->getContent());
            $datas = get_object_vars($data);
            $userid = $api_key = '';
            if ($datas['userId']) {
                $userid = $datas['userId'];
            }
            if ($datas['authenticationToken']) {
                $api_key = $datas['authenticationToken'];
            }

            if ($userid && $api_key) {

                $auth = AmbassadorUser::where([
                            'id' => $userid,
                            'api_key' => $api_key,
                        ])->first();
                if ($auth) {
                    if ($auth['status'] == 1) {
                        $input = [
                            'first_name' => $datas['first_name'],
                            'last_name' => $datas['last_name'],
                            'age' => $datas['age'],
                            'gender' => $datas['gender'],
                            'email' => $datas['email'],
                            'mobile_number' => $datas['mobile_number'],
                            'aadhar_number' => $datas['aadhar_number'],
                            'pan_number' => $datas['pan_number'],
                            'address' => $datas['address'],
                        ];

                        if (AmbassadorUser::where('id', $userid)->update($input)) {
                            $response = array('loginSuccessful' => true, 'code' => '200', 'userDetails' => $input);
                        } else {
                            $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => "Profile not updated");
                        }
                    } else {
                        $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'your account has been disabled');
                    }
                } else {
                    $response = array('loginSuccessful' => 'false', 'code' => '401', 'message' => 'Unauthorized');
                }
            } else {
                if (empty($userid)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'UserId is Empty');
                }
                if (empty($api_key)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'API Key is Empty');
                }
            }
        } else {

            $response = array('requestSuccessful' => false, 'code' => '400', 'message' => 'JSON is Not Valid');
        }
        return response($response, $response['code'])
                        ->header('Content-Type', 'application/json');
    }

    public function registration(Request $request) {
        $common = new CommonFunctionController();
        if ($common->isJson($request->getContent())) {
            $data = json_decode($request->getContent());
            $datas = get_object_vars($data);
            $accept_terms = $experience = $current_occupation = $first_name = $last_name = $DOB = $gender = $email = $mobile_number = $mobile_number = $pan_number = $address = '';
            if ($datas['first_name']) {
                $first_name = $datas['first_name'];
            }
            if ($datas['last_name']) {
                $last_name = $datas['last_name'];
            }
            if ($datas['DOB']) {
                $DOB = $datas['DOB'];
            }
            if ($datas['gender']) {
                $gender = $datas['gender'];
            }
            if ($datas['email']) {
                $email = $datas['email'];
            }
            if ($datas['mobile_number']) {
                $mobile_number = $datas['mobile_number'];
            }
            if ($datas['aadhar_number']) {
                $aadhar_number = $datas['aadhar_number'];
            }
            if ($datas['pan_number']) {
                $pan_number = $datas['pan_number'];
            }
            if ($datas['address']) {
                $address = $datas['address'];
            }
            if ($datas['current_occupation']) {
                $current_occupation = $datas['current_occupation'];
            }
            if ($datas['experience']) {
                $experience = $datas['experience'];
            }
            if ($datas['accept_terms']) {
                $accept_terms = $datas['accept_terms'];
            }
            if ($first_name && $last_name && $DOB && $gender && $email && $mobile_number && $aadhar_number && $pan_number && $address && $current_occupation && $experience && $accept_terms) {
                $find_email = AmbassadorUser::where([
                            'email' => $email,
                        ])->count();
                if ($find_email == 0) {
                    $validator = Validator::make($datas, [
                                'mobile_number' => 'required|digits:10',
                                'aadhar_number' => 'digits:12',
                                'pan_number' => 'string|min:1|max:10',
                                'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
                                'experience' => 'required|numeric',
                                'accept_terms' => 'required|string',
                                'current_occupation' => 'required|string',
                    ]);
                    if (!$validator->fails()) {
                        $User = new AmbassadorUser();
                        $User->first_name = $datas['first_name'];
                        $User->last_name = $datas['last_name'];
                        $User->DOB = $datas['DOB'];
                        $User->gender = $datas['gender'];
                        $User->email = $datas['email'];
                        $User->mobile_number = $datas['mobile_number'];
                        $User->aadhar_number = $datas['aadhar_number'];
                        $User->pan_number = $datas['pan_number'];
                        $User->address = $datas['address'];
                        $User->user_name = $datas['email'];
                        $User->password = $common->encode($datas['mobile_number']);
                        $User->org_password = $datas['mobile_number'];
                        $User->current_occupation = $datas['current_occupation'];
                        $User->experience = $datas['experience'];
                        $User->accept_terms = $datas['accept_terms'];
                        $User->referal_code = substr(str_shuffle("0123456789ABCDEFGHIKLMNOPQRSTVXYZ"), 0, 8);
						$User->otp = uniqid(substr(str_shuffle("0123456789"), 0, 4));
                        $User->save();
                        $mobile = urlencode($datas['mobile_number']);
                        $mobile = $datas['mobile_number'];
$message = "Dear Veranda Team members Failure Alert :'+corder.vertical +'Name :'+output.billing_name+'Email :'+output.billing_email+'Mobile :'+output.delivery_tel+'Product :'+order.product+'Status :'+output.order_status+'Payment Gate :'+order.payment_gate +'Payment Mode :'+output.payment_mode";
$url_feilds = "key=460B64E103AF12&campaign=0&routeid=9&type=text&contacts=$mobile&senderid=VRANDA&msg=$message&template_id=1707162280347614080";
$url = "http://bulksms.rmbassociates.in/app/smsapi/index.php";
$params = "key=460B64E103AF12&campaign=0&routeid=9&type=text&contacts=$mobile&senderid=VRANDA&msg=$message&template_id=1707162280347614080";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
                        $response = array('loginSuccessful' => true, 'code' => '200', 'message' => "User registration Successfully");
                    } else {
                        $error = $validator->errors();
                        $show = get_object_vars(json_decode($error->toJson()));
                        $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => $show);
                    }
                } else {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Email already exists');
                }
            } else {
                if (empty($first_name)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'First Name is Empty');
                }
                if (empty($last_name)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Last Name is Empty');
                }
                if (empty($DOB)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'DOB is Empty');
                }
                if (empty($gender)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Gender is Empty');
                }
                if (empty($email)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Email is Empty');
                }
                if (empty($mobile_number)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Mobile Number is Empty');
                }
                if (empty($aadhar_number)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Aadhar Number is Empty');
                }
                if (empty($pan_number)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Pan Number is Empty');
                }
                if (empty($address)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Address is Empty');
                }
                if (empty($current_occupation)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Current Occupation is Empty');
                }
                if (empty($experience)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Experience is Empty');
                }
                if (empty($accept_terms)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Terms And Conditions is Empty');
                }
            }
        } else {

            $response = array('requestSuccessful' => false, 'code' => '400', 'message' => 'JSON is Not Valid');
        }
        return response($response, $response['code'])
                        ->header('Content-Type', 'application/json');
    }
	public function otp_verification(Request $request) {
        $common = new CommonFunctionController();
        if ($common->isJson($request->getContent())) {
            $data = json_decode($request->getContent());
            $datas = get_object_vars($data);
            $mobileNumber =  '';
            if ($datas['userId']) {
                $userid = $datas['userId'];
            }
            if ($datas['authenticationToken']) {
                $api_key = $datas['authenticationToken'];
            }
            if ($datas['otp']) {
                $otp = $datas['otp'];
            }

            if ($userid && $api_key&& $otp) {

                  $auth = AmbassadorUser::where([
                            'id' => $userid,
                            'api_key' => $api_key,
                        ])->first();
                if ($auth) {
    
                
                    if($otp==$auth['otp']){
                        $mail_blade = "mailToAmbassador";
                        $mail_id = $auth['email'];
                        $first_name = $auth['first_name'];
                        $user_id = $auth['user_id'];
                        $subject = "Veranda credentials";
                        $data = array("email"=>$mail_id,'id'=>$user_id,'first_name'=>$first_name);	
                        $send = $common->send_m($mail_blade, $subject, $data);		

                        $get_admin = AmbassadorUser::where(['role_id' => 1])->get();
                        foreach($get_admin as $admin)
                        {
                        $mail_blade = "notificationMailToAdmin";
                        $mail_id = $admin['email'];
                        $first_name = $admin['first_name'];
                        $user_id = $admin['user_id'];
                        $subject = "New Ambassador Registered";
                        $data = array("email"=>$mail_id,'id'=>$user_id,'first_name'=>$first_name);	
                        $send = $common->send_m($mail_blade, $subject, $data);	
                       
                        }
                       

                        $response = array('loginSuccessful' => true, 'code' => '200', 'message' => "OTP Authentication Sucessful");
                    }else{
                        $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'OTP is Invalid'); 
                    }
                    
                } else {
                    $response = array('loginSuccessful' => 'false', 'code' => '401', 'message' => 'Unauthorized');
                }
            } else {
                if (empty($userid)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'UserId is Empty');
                }
                if (empty($api_key)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'API Key is Empty');
                }
                if (empty($otp)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'OTP is Empty');
                }
            }
        } else {

            $response = array('requestSuccessful' => false, 'code' => '400', 'message' => 'JSON is Not Valid');
        }
        return response($response, $response['code'])
                        ->header('Content-Type', 'application/json');
    }

    public function admin_ambassador_approval(Request $request) {
        $common = new CommonFunctionController();
        if ($common->isJson($request->getContent())) {
            $data = json_decode($request->getContent());
            $datas = get_object_vars($data);
            $status = $userid = $api_key = '';
            if ($datas['userId']) {
                $userid = $datas['userId'];
            }
            if ($datas['authenticationToken']) {
                $api_key = $datas['authenticationToken'];
            }
            if ($datas['status']) {
                $status = $datas['status'];
            }

            if ($userid && $api_key && $status) {

                $auth = AmbassadorUser::where([
                            'id' => $userid,
                            'api_key' => $api_key,
                        ])->first();
                if ($auth) {
                    if ($auth['status'] == 0) {
                        $input = [
                            'status' => $status
                        ];
                        AmbassadorUser::where('id', $auth['id'])
                                ->update($input);
                        $msg = $status == 1 ? 'Approved' : 'Rejected';
                        $response = array('loginSuccessful' => true, 'code' => '200', 'message' => "Admin has been $msg successfully");
                        //mail
                        $mail_blade = "credentialMailToAmbassador";	
                        $mail_id = $auth['email'];	
                        $first_name = $auth['first_name'];	
                        $user_id = $auth['user_id'];	
                        $subject = "Credentials";
                        $password = $auth['org_password'];
                        $data = array("email"=>$mail_id,'id'=>$user_id,'first_name'=>$first_name,'org_password'=>$password);	
                        $send = $common->send_m($mail_blade, $subject, $data);
                    } else {
                        if ($auth['status'] == 1) {
                            $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Admin already Approved');
                        }if ($auth['status'] == 2) {
                            $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Admin already Rejected');
                        }
                    }
                } else {
                    $response = array('loginSuccessful' => 'false', 'code' => '401', 'message' => 'Unauthorized');
                }
            } else {
                if (empty($userid)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'UserId is Empty');
                }
                if (empty($api_key)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'API Key is Empty');
                }
                if (empty($status)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Status is Empty');
                }
            }
        } else {

            $response = array('requestSuccessful' => false, 'code' => '400', 'message' => 'JSON is Not Valid');
        }
       return response($response, $response['code'])->header('Content-Type', 'application/json');
    }

    public function ambassador_document_upload(Request $request) {
//echo"<pre>";print_r($request->all());exit;
        $common = new CommonFunctionController();
//        if ($common->isJson($request->getContent())) {
//            $data = json_decode($request->getContent());
//            $datas = get_object_vars($data);
        $has_file = $userid = $api_key = $key = '';
        if ($request['userId']) {
            $userid = $request['userId'];
        }
        if ($request['authenticationToken']) {
            $api_key = $request['authenticationToken'];
        }
        if ($request['key']) {
            $key = $request['key'];
        } if ($request->hasFile('filenames')) {
            $has_file = 1;
        }

        if ($userid && $api_key && $key && $has_file) {
            if ($key == "image" || $key == "aadhar_card_image" || $key == "pan_card_image") {
                $auth = AmbassadorUser::where([
                            'id' => $userid,
                            'api_key' => $api_key,
                        ])->first();
                if ($auth) {

                    if ($auth['status'] == 1) {
                        if ($request->hasFile('filenames')) {
                            $user_img_name = $request->file('filenames');
                            $user_name = date('YmdHis') . rand(0, 10) . rand(0, 10) . '_l.' . $user_img_name->getClientOriginalExtension();
                            $destinationPath = public_path('/files');
                            $user_img_name->move($destinationPath, $user_name);

                            $image = [
                                $key => $user_name
                            ];
                            AmbassadorUser::where('id', $auth['id'])
                                    ->update($image);
                        }

                        $response = array('loginSuccessful' => true, 'code' => '200', 'message' => "$key has been upload successfully");
                    } else {
                        $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'your account has been disabled');
                    }
                } else {
                    $response = array('loginSuccessful' => 'false', 'code' => '401', 'message' => 'Unauthorized');
                }
            } else {
                $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Invalid Key Please Use these key"image","aadhar_card_image","pan_card_image"');
            }
        } else {
            if (empty($userid)) {
                $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'UserId is Empty');
            }
            if (empty($api_key)) {
                $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'API Key is Empty');
            }if (empty($key)) {
                $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Key is Empty');
            }
            if (empty($has_file)) {
                $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'File is Missing');
            }
        }
//        } else {
//
//            $response = array('requestSuccessful' => false, 'code' => '400', 'message' => 'JSON is Not Valid');
//        }
        return response($response, $response['code'])
                        ->header('Content-Type', 'application/json');
    }

    public function admin_view_document(Request $request) {
        $common = new CommonFunctionController();
        if ($common->isJson($request->getContent())) {
            $data = json_decode($request->getContent());
            $datas = get_object_vars($data);
            $userid = $api_key = '';
            if ($datas['userId']) {
                $userid = $datas['userId'];
            }
            if ($datas['authenticationToken']) {
                $api_key = $datas['authenticationToken'];
            }
            if ($userid && $api_key) {

                $auth = AmbassadorUser::where([
                            'id' => $userid,
                            'api_key' => $api_key,
                        ])->first();
                if ($auth) {
                    if ($auth['status'] == 1) {
                        $select_images = AmbassadorUser::where([
                                    'role_id' => 3,
                                ])->get();
                        foreach ($select_images as $select_image) {
                            $ambassador_user [] = array(
                                'first_name' => $select_image['first_name'],
                                'last_name' => $select_image['last_name'],
                                'DOB' => $select_image['DOB'],
                                'gender' => $select_image['gender'],
                                'email' => $select_image['email'],
                                'experience' => $select_image['experience'],
                                'mobile_number' => $select_image['mobile_number'],
                                'address' => $select_image['address'],
                                'image' => url('/files/' . $select_image['image']),
                                'aadhar_card_image' => url('/files/' . $select_image['aadhar_card_image']),
                                'pan_card_image' => url('/files/' . $select_image['pan_card_image'])
                            );
                        }


                        $response = array('loginSuccessful' => true, 'code' => '200', 'ambassador_user' => $ambassador_user);
                    } else {
                        $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'your account has been disabled');
                    }
                } else {
                    $response = array('loginSuccessful' => 'false', 'code' => '401', 'message' => 'Unauthorized');
                }
            } else {
                if (empty($userid)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'UserId is Empty');
                }
                if (empty($api_key)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'API Key is Empty');
                }
            }
        } else {

            $response = array('requestSuccessful' => false, 'code' => '400', 'message' => 'JSON is Not Valid');
        }
        return response($response, $response['code'])
                        ->header('Content-Type', 'application/json');
    }

    public function admin_document_approval(Request $request) {
        $common = new CommonFunctionController();
        if ($common->isJson($request->getContent())) {
            $data = json_decode($request->getContent());
            $datas = get_object_vars($data);
            $status = $userid = $api_key = '';
            if ($datas['userId']) {
                $userid = $datas['userId'];
            }
            if ($datas['authenticationToken']) {
                $api_key = $datas['authenticationToken'];
            }
            if ($datas['status']) {
                $status = $datas['status'];
            }

            if ($userid && $api_key && $status) {

                $auth = AmbassadorUser::where([
                            'id' => $userid,
                            'api_key' => $api_key,
                        ])->first();
//                echo"<pre>";print_r($auth/);exit;
                if ($auth) {
                    if ($auth['status'] == 1) {
                        $input = [
                            'status' => $status
                        ];
                        AmbassadorUser::where('id', $auth['id'])
                                ->update($input);
                        $msg = $status == 3 ? 'Approved' : 'Rejected';
                        $response = array('loginSuccessful' => true, 'code' => '200', 'message' => "Document has been $msg successfully");
                    } else {
//                        
                        if ($auth['status'] == 3) {
                            $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Document already Approved');
                        }
                        if ($auth['status'] == 4) {
                            $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Document already Rejected');
                        }
                    }
                } else {
                    $response = array('loginSuccessful' => 'false', 'code' => '401', 'message' => 'Unauthorized');
                }
            } else {
                if (empty($userid)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'UserId is Empty');
                }
                if (empty($api_key)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'API Key is Empty');
                }
                if (empty($status)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Status is Empty');
                }
            }
        } else {

            $response = array('requestSuccessful' => false, 'code' => '400', 'message' => 'JSON is Not Valid');
        }
        return response($response, $response['code'])
                        ->header('Content-Type', 'application/json');
    }
    public function reset_password(Request $request)
    {
        $common = new CommonFunctionController();
        if ($common->isJson($request->getContent())) {
            $data = json_decode($request->getContent());
            $datas = get_object_vars($data);
            $userid = $api_key = '';
            if ($datas['userId']) {
                $userid = $datas['userId'];
            }
            if ($datas['authenticationToken']) {
                $api_key = $datas['authenticationToken'];
            }

            if ($userid && $api_key) {

                $auth = AmbassadorUser::where([
                            'id' => $userid,
                            'api_key' => $api_key,
                        ])->first();
                if ($auth) {
                    
            $old_password = $new_password = $confirm_password = '';
            if ($datas['old_password']) {
                $old_password = $datas['old_password'];
            }
            if ($datas['new_password']) {
                $new_password = $datas['new_password'];
            }
            if ($datas['confirm_password']) {
                $confirm_password = $datas['confirm_password'];
            }
            if ($old_password && $new_password && $confirm_password) {
                if($old_password != $new_password)
                {
                    if($new_password == $confirm_password)
                    {
                        AmbassadorUser::where('id', $datas['userId'])
                                ->update(['password'=>$common->encode($datas['new_password']),'org_password'=>$datas['new_password']]);
                        $response = array('loginSuccessful' => true, 'code' => '200', 'message' => "Password reseted successfully");
                    } else {
                        $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'Confirm password should be same as new password');
                    }
                }
                else {

                    $response = array('requestSuccessful' => false, 'code' => '400', 'message' => 'New password should not be your old password');
                }
            }
            else {

                if (empty($old_password)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'old_password is Empty');
                }
                if (empty($new_password)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'new_password is Empty');
                }
                if (empty($confirm_password)) {
                    $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'confirm_password is Empty');
                }
            }
            } else {
                $response = array('loginSuccessful' => 'false', 'code' => '401', 'message' => 'Unauthorized');
            }
        }
        else {
            if (empty($userid)) {
                $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'userid is Empty');
            }
            if (empty($api_key)) {
                $response = array('loginSuccessful' => 'false', 'code' => '400', 'message' => 'api_key is Empty');
            }
        }

                
            
        } else {

            $response = array('requestSuccessful' => false, 'code' => '400', 'message' => 'JSON is Not Valid');
        }
        return response($response, $response['code'])
                        ->header('Content-Type', 'application/json');

    }



        

}
