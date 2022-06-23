<?php

namespace Webkul\API\Http\Controllers\Shop;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Webkul\Customer\Http\Requests\CustomerRegistrationRequest;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use JWTAuth; #SKP


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
            $this->middleware('auth:' . $this->guard)->except('refresh'); #SKP
        }

        $this->customerRepository = $customerRepository;

        $this->customerGroupRepository = $customerGroupRepository;
    }

    /**
     * Method to store user's sign up form data to DB.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CustomerRegistrationRequest $request)
    {
        $request->validated();

        $data = [
            'first_name'  => $request->get('first_name'),
            'last_name'   => $request->get('last_name'),
            'email'       => $request->get('email'),
            'password'    => $request->get('password'),
            'password'    => bcrypt($request->get('password')),
            'channel_id'  => core()->getCurrentChannel()->id,
            'is_verified' => 1,
            'customer_group_id' => $this->customerGroupRepository->findOneWhere(['code' => 'general'])->id
        ];

        Event::dispatch('customer.registration.before');

        $customer = $this->customerRepository->create($data);

        Event::dispatch('customer.registration.after', $customer);

        return response()->json([
            'message' => 'Your account has been created successfully.',
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