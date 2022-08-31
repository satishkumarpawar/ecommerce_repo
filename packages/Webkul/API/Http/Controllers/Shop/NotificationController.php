<?php

namespace Webkul\API\Http\Controllers\Shop;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

use Illuminate\Support\Facades\DB;


use Webkul\Customer\Models\Customer;
//use Webkul\Notification\Models\NotificationSetting;


use Webkul\Notification\Repositories\NotificationSettingRepository;
use Webkul\API\Http\Resources\Notification\NotificationSetting as NotificationSettingResource;


class NotificationController extends Controller
{
    use DispatchesJobs, ValidatesRequests;

    protected $NotificationSettingRepository;
    protected $guard;
    protected $customer_id;
    protected $user;


    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct( NotificationSettingRepository $NotificationSettingRepository)
    {
        $this->_config = request('_config');

        $this->NotificationSettingRepository = $NotificationSettingRepository;

        /*$this->guard = request()->has('token') ? 'api' : 'customer';
        #SKP Start
        //need to modify
        if (!is_null(request()->input('customer_id'))) {
            $this->customer_id=request()->input('customer_id');
            $this->middleware('admin');
        } else {
            auth()->setDefaultDriver($this->guard);
            $this->middleware('auth:' . $this->guard);

            $customer = auth($this->guard)->user();
            if(isset($customer->id))$this->customer_id=$customer->id;
            else $this->customer_id=request()->input('customer_id');
        }

        if (!is_null($this->customer_id)) {
            $this->user = Customer::where('id',$this->customer_id)->first(); 
        
        }*/

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return response()->json([
            'message' => 'Page no found.',
        ]);
    }

    public function getList()
    {
       
           return NotificationSettingResource::collection($this->NotificationSettingRepository->getAll());
               
    }

    
       
    public function get()
    {
        
        if (!is_null(request()->input('id'))) {
            return NotificationSettingResource::collection($this->NotificationSettingRepository->get(request()->input('id')));
        } else {
           return [];
        }
        
    }

    public function create()
    {
       
       /* $this->validate(request(), [
            'notification_titile' => 'string|required',
            'notification_message' => 'string|required',
            'customer_group_id' => 'number|required',
            'notification_type' => 'number|required',
            'notification_times' => 'number|required',
            'notification_interval' => 'number|required',
            'state' => 'number|required'
         ]);
    */
           
        $NotificationSetting=$this->NotificationSettingRepository->create(request());

        return response()->json([
            'message' => 'Notification Setting created successfully.',
            'data'    => new NotificationSettingResource($NotificationSetting),
        ]);
        
    }

    public function update()
    {
       
       /* $this->validate(request(), [
            'notification_titile' => 'string|required',
            'notification_message' => 'string|required',
            'customer_group_id' => 'number|required',
            'notification_type' => 'number|required',
            'notification_times' => 'number|required',
            'notification_interval' => 'number|required',
            'state' => 'number|required'
            
        ]);*/
    
    
        $NotificationSetting=$this->NotificationSettingRepository->update(request());

        return response()->json([
            'message' => 'Notification Setting updated successfully.',
            'data'    => new NotificationSettingResource($NotificationSetting),
        ]);
        
    }

    public function delete()
    {
        $NotificationSetting = $this->NotificationSettingRepository->findOrFail(request()->id);

        try {
           
            $this->NotificationSettingRepository->delete(request()->id);

            return response()->json(['message' => true], 200);
        } catch (\Exception $e) {
            report($e);
        }

        return response()->json(['message' => false], 200);

    }
}
