<?php

namespace Webkul\API\Http\Controllers\Shop;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Webkul\Customer\Http\Requests\CustomerForgotPasswordRequest;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use JWTAuth; #SKP
use Illuminate\Support\Facades\Mail;
use Webkul\Customer\Mail\VerificationMobile;

use Illuminate\Support\Facades\Session;

use \App\Otp;
//use \App\MSG91;
use Webkul\Customer\Models\Customer;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerForgotPasswordRequest $request)
    {
        $request->validated();

        $response = $this->broker()->sendResetLink($request->only(['email']));

        return $response == Password::RESET_LINK_SENT
            ? response()->json([
                'message' => trans($response),
            ])
            : response()->json([
                'error' => trans($response),
            ]);
    }

    public function sendOtp(Request $request){

        $response = array();
    
        if ( isset($request->phone) && $request->phone =="" ) {
            return response()->json([
                'error'   => 1,
                'message' => 'Phone number is not valid.',
           ]);
           
        } else {


            $customer=Customer::select('customers.id')
            ->distinct()
            ->where('phone',$request->phone)
            ->limit(1)
           ->get()
           ->first();

           if(!isset($customer['id'])){
                return response()->json([
                    'error'   => 1,
                    'message' => 'Phone number is not exist.',
                    
                ]);
            }
    
            $otp = rand(100000, 999999);
    
            Otp::where("phone",$request->phone)->delete();
            Otp::create(["otp"=>$otp,"phone"=>$request->phone]);
    
           /*$MSG91 = new MSG91();
    
            $msg91Response = $MSG91->sendSMS($otp,$users['phone']);
    
            if($msg91Response['error']){
                $response['error'] = 1;
                $response['message'] = $msg91Response['message'];
                $response['loggedIn'] = 1;
            }else{*/
    
               // Session::put('OTP', $otp);
                //sendemail
                Mail::queue(new VerificationMobile(['email' =>$request->phone.'@yopmail.com' ,'otp' => $otp]));
                return response()->json([
                    'error'   => 0,
                    'message' => 'Your OTP is created.'
               ]);
               
               // $response['OTP'] = $otp;
                
            //}
           
        }
        
       // return response()->json($response);
        //echo json_encode($response);
    }
    
    public function verifyOtp(Request $request){
    
        $this->validate(request(), [
            'otp' => 'required'
         ]);

        $response = array();
    
        //$userId = Auth::user()->id;  //Getting UserID.
       
        $expire_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." -10 minutes"));
            //$OTP = $request->session()->get('OTP');
            $otpdata=Otp::select('otps.*')
            ->distinct()
            ->where('otp',$request->otp)
            //->where('created_at', '>=', 'DATE_SUB(NOW(), INTERVAL 10 MINUTE)')
            ->where('created_at', '>=', $expire_time)
            ->orderby("id","desc")
            ->limit(1)
           ->get()
           ->first();
    
           
            $OTP=null;
            $Phone=null;
            if(isset($otpdata['otp'])){
                $OTP=$otpdata['otp'];
                $Phone=$otpdata['phone'];
            }
    
            if($OTP == $request->otp){
                /*Session::forget('OTP');
                Session::forget('phone');
              
            */

                if(isset($request->password) && $request->password!='' && $Phone!=null) {
                        Otp::where("otp",$OTP)->delete();
                        Otp::where("phone",$Phone)->delete();

                        $customer = Customer::where("phone",$Phone)->update(["password"=>bcrypt($request->get('password'))]);

                        return response()->json([
                            'error'   => 0,
                            'is_verified'   => 1,
                            'message' => 'Reset password successfully.',  
                            //"data"=>$otpdata    
                        ]);
                } else {
                    return response()->json([
                        'error'   => 0,
                        'is_verified'   => 1,
                        'message' => 'Your Number is Verified.',
                        //"data"=>$otpdata 
                    ]);
                }
                
            }else{
                return response()->json([
                    'error'   => 1,
                    'is_verified'   => 0,
                    'message' => 'OTP does not match.',
                    //"data"=>$otpdata 
                ]);
    
            }
        //echo json_encode($response);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('customers');
    }
}
