<?php

namespace Webkul\API\Http\Controllers\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Webkul\Customer\Http\Requests\CustomerRegistrationRequest;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use JWTAuth; #SKP
use Illuminate\Support\Facades\Mail;
use Webkul\Customer\Mail\VerificationMobile;

use Illuminate\Support\Facades\Session;

use \App\Otp;
//use \App\MSG91;

class CustomerController extends Controller
{
    /**
     * Contains current guard
     *
     * @var array
     */
    protected $guard;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Repository object
     *
     * @var \Webkul\Customer\Repositories\CustomerRepository
     */
    protected $customerRepository;

    /**
     * Repository object
     *
     * @var \Webkul\Customer\Repositories\CustomerGroupRepository
     */
    protected $customerGroupRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerRepository  $customerRepository
     * @param  \Webkul\Customer\Repositories\CustomerGroupRepository  $customerGroupRepository
     * @return void
     */
    public function __construct(
        CustomerRepository $customerRepository,
        CustomerGroupRepository $customerGroupRepository
    )   {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        $this->_config = request('_config');

        if (isset($this->_config['authorization_required']) && $this->_config['authorization_required']) {

            auth()->setDefaultDriver($this->guard);
            // $this->middleware('auth:' . $this->guard)
            $this->middleware('auth:' . $this->guard)->except('refresh','create','sendOtp','verifyOtp'); #SKP
        }

        $this->customerRepository = $customerRepository;

        $this->customerGroupRepository = $customerGroupRepository;
    }


#SKP
public function sendOtp(Request $request){

    $response = array();

    if ( isset($request->phone) && $request->phone =="" ) {
        $response['error'] = 1;
        $response['message'] = 'Invalid mobile number';
    } else {

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

            $response['error'] = 0;
            $response['message'] = 'Your OTP is created.';
           // $response['OTP'] = $otp;
            
        //}
       
    }
    
    return response()->json($response);
    //echo json_encode($response);
}

public function verifyOtp(Request $request){

    $response = array();

    //$userId = Auth::user()->id;  //Getting UserID.
   
    
        //$OTP = $request->session()->get('OTP');
        $otpdata=Otp::select('otps.*')
        ->distinct()
        ->where('otp',$request->otp)
        //->where('DATE_ADD(created_at, INTERVAL 10 MINUTE)', '>=', 'NOW()')
        ->orderby("id","desc")
        ->limit(1)
       ->get()
       ->first()
        ;
        
        $OTP=null;
        if(isset($otpdata['otp']))$OTP=$otpdata['otp'];

        if($OTP == $request->otp){
            /*Session::forget('OTP');
            Session::forget('phone');
         
        */

            return response()->json([
                'error'   => 0,
                'is_verified'   => 1,
                'message' => 'Your Number is Verified.',      
            ]);
            
            
        }else{
            return response()->json([
                'error'   => 1,
                'is_verified'   => 0,
                'message' => 'OTP does not match.'
            ]);

        }
    //echo json_encode($response);
}


    /**
     * Method to store user's sign up form data to DB.
     *
     * @return \Illuminate\Http\Response
     */
public function create(){
    $request=request();
    //$request->validated();
    $this->validate(request(), [
       // 'email' => 'required',
        'phone' => 'required',
        'password' => 'required',
    ]);
   
    $users = $this->customerRepository->get()->where('phone', $request->phone)->first();
    if(isset($users->id)){
        return response()->json([
            'message' => 'Phone  already exist.',
            'error'    => 1,
        ]);  
    }

    $data = [
        'first_name'  => $request->get('first_name'),
        'last_name'   => $request->get('last_name'),
        //'email'       => $request->get('email'),
        'phone'       => $request->get('phone'),
        'password'    => $request->get('password'),
        'password'    => bcrypt($request->get('password')),
        'channel_id'  => core()->getCurrentChannel()->id,
        'is_verified' => 1,
        'customer_group_id' => $this->customerGroupRepository->findOneWhere(['code' => 'general'])->id
    ];
    

    Event::dispatch('customer.registration.before');

    $customer = $this->customerRepository->create($data);

    Event::dispatch('customer.registration.after', $customer);

   $jwtToken = null;

        if (! $jwtToken = auth()->guard($this->guard)->attempt($request->only(['phone', 'password']))) {
            return response()->json([
                'error' => 'Invalid Phone or Password',
            ], 401);
        }

        Event::dispatch('customer.after.login', $request->get('phone'));

       
        return response()->json([
            'token'   => $jwtToken,
            'message' => 'Logged in successfully.',
            'data'    => $customer,
        ]);
    
    

    return response()->json([
        'message' => 'Your account has been created successfully.',
        'data'    => $customer,
    ]);
}

    /**
     * Returns a current user data.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        if (Auth::user($this->guard)->id === (int) $id) {
            return new $this->_config['resource'](
                $this->customerRepository->findOrFail($id)
            );
        }

        return response()->json([
            'message' => 'Invalid Request.',
        ], 403);
    }

    public function refresh()
    {
        #SKP
       /* $jwtToken=auth()->guard($this->guard)->refresh(true, true);

       // $customer = auth($this->guard)->user();
       
        return response()->json([
            'token'   =>  $jwtToken,
            'message' => 'Refreshed successfully.'
        ]);
            */
           

            try {
                if (!JWTAuth::parseToken()->authenticate()) {
                    return response()->json(["message" => "Token is still valid"], 400);
                }
                //refresh token
                $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                  $user = JWTAuth::setToken($refreshed)->toUser();
                  request()->headers->set('Authorization','Bearer '.$refreshed);
                  return response()->json([
                    'token'   =>  $refreshed,
                    'message' => 'Refreshed successfully.'
                ],200);
            } catch (\Exception $e) {
                // Access token has expired
                try {
                    $refreshed = JWTAuth::refresh();
                    request()->headers->set('Authorization','Bearer '.$refreshed);
                    return response()->json([
                      'token'   =>  $refreshed,
                      'message' => 'Refreshed successfully.'
                  ],200);
                } catch (\Exception $e) {
                    return response()->json(["message" => $e->getMessage()], 401);
                }
            }
    }
}