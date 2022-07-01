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

use Illuminate\Support\Facades\Session;
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
   
    /*$userId = Auth::user()->id;

    $users = User::where('id', $userId)->first();
    */

    $users = $this->customerRepository->get()->where('phone', $request->phone)->first();

    if ( isset($users['phone']) && $users['phone'] =="" ) {
        $response['error'] = 1;
        $response['message'] = 'Invalid mobile number';
        $response['loggedIn'] = 0;
    } else {

        $otp = rand(100000, 999999);
       /*$MSG91 = new MSG91();

        $msg91Response = $MSG91->sendSMS($otp,$users['phone']);

        if($msg91Response['error']){
            $response['error'] = 1;
            $response['message'] = $msg91Response['message'];
            $response['loggedIn'] = 1;
        }else{*/

            Session::put('OTP', $otp);
            Session::put('phone',$request->phone);
            Session::put('password','');

            $response['error'] = 0;
            $response['message'] = 'Your OTP is created.';
            $response['OTP'] = $otp;
            $response['phone'] = $request->phone;
            $response['loggedIn'] = 1;
        //}
       
    }
    
    return response()->json($response);
    //echo json_encode($response);
}

public function verifyOtp(Request $request){

    $response = array();

    //$userId = Auth::user()->id;  //Getting UserID.
    $users = $this->customerRepository->get()->where('phone', $request->phone)->first();

    $userId=$users->id;
    if($userId == "" || $userId == null){
        $response['error'] = 1;
        $response['message'] = 'You are logged out, Login again.';
        $response['loggedIn'] = 1;
    }else{
        $OTP = $request->session()->get('OTP');
        $phone = $request->session()->get('phone');
        $email = $users->email;
        $users->password=$request->session()->get('password');
        $password = $request->session()->get('password');
        if($OTP === $request->otp){// && $phone===$request->phone

            // Updating user's status "is_verified" as 1.

            $this->customerRepository->where('id', $userId)->update(['is_verified' => 1]);

            //Removing Session variable
            Session::forget('OTP');
            Session::forget('phone');
            Session::forget('password');

            /*$response['error'] = 0;
            $response['is_verified'] = 1;
            $response['loggedIn'] = 1;
            $response['message'] = "Your Number is Verified.";*/

            return response()->json([
                'error'   => 0,
                'is_verified'   => 1,
                'loggedIn'   => 1,
                'message' => 'Your Number is Verified.',      
            ]);
            
            $jwtToken = null;
            
            if (! $jwtToken = auth()->guard($this->guard)->attempt(['email'=>$email, 'password'=>$password])) {
           // if (! $jwtToken = auth()->guard($this->guard)->attempt($users->only(['email', 'password']))) {
                return response()->json([
                    'error'   => 0,
                    'is_verified'   => 1,
                    'loggedIn'   => 1,
                    'message' => 'Your Number is Verified.',      
                ]);
            }

            Event::dispatch('customer.after.login', $email);

        
            return response()->json([
                'token'   => $jwtToken,
                'message' => 'Logged in successfully.',
                'data'    => $users,
            ]);

        }else{
            $response['error'] = 1;
            $response['is_verified'] = 0;
            $response['loggedIn'] = 1;
            $response['message'] = "OTP does not match.";
        }
    }
    return response()->json($response);
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
        'email' => 'required',
        //'phone' => 'required',
        'password' => 'required'
    ]);
    $data = [
        'first_name'  => $request->get('first_name'),
        'last_name'   => $request->get('last_name'),
        'email'       => $request->get('email'),
        'phone'       => $request->get('phone'),
        'password'    => $request->get('password'),
        'password'    => bcrypt($request->get('password')),
        'channel_id'  => core()->getCurrentChannel()->id,
        'is_verified' => 0,
        'customer_group_id' => $this->customerGroupRepository->findOneWhere(['code' => 'general'])->id
    ];

    //Event::dispatch('customer.registration.before');

    $customer = $this->customerRepository->create($data);

   // Event::dispatch('customer.registration.after', $customer);

  /*  $jwtToken = null;

        if (! $jwtToken = auth()->guard($this->guard)->attempt($request->only(['email', 'password']))) {
            return response()->json([
                'error' => 'Invalid Email or Password',
            ], 401);
        }

        Event::dispatch('customer.after.login', $request->get('email'));

       
        return response()->json([
            'token'   => $jwtToken,
            'message' => 'Logged in successfully.',
            'data'    => $customer,
        ]);
    
    */

    $otp = rand(100000, 999999);
    Session::put('OTP', $otp);
    Session::put('phone',$customer->phone);
    Session::put('password',$request->get('password'));

    $response['error'] = 0;
    $response['message'] = 'Your OTP is created.';
    $response['OTP'] = $otp;
    $response['phone'] = $customer->phone;
   

    return response()->json([
        'message' => 'Your account has been created successfully.',
        'data'    => $response,
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