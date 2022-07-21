<?php

namespace Webkul\API\Http\Controllers\Shop;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

use JWTAuth; #SKP

use Bavix\Wallet\Models\Transaction;

use Webkul\Customer\Models\Customer;

class WalletController extends Controller
{
    use DispatchesJobs, ValidatesRequests;

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
    public function __construct()
    {
        $this->_config = request('_config');
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

        if (!is_null(request()->input('customer_id'))) {
            $user = Customer::where('id',request()->input('customer_id'))->first(); 
            $user->wallet->refreshBalance();
            return response()->json([
                'data' => ["holder_name"=>$user->first_name.' '.$user->last_name,"wallet"=>$user->wallet,"transactions"=>$user->transactions],
            ]);
            
        }

        return response()->json([
            'message' => 'Page no found.',
        ]);
       
      
               
    }

   
       
    public function get()
    {
        
        if (!is_null(request()->input('customer_id'))) {
            if (!is_null(request()->input('customer_id'))) {
                $user = Customer::where('id',request()->input('customer_id'))->first(); 
                return response()->json([
                    'data' => ["holder_name"=>$user->first_name.' '.$user->last_name,"wallet"=>$user->wallet,"transactions"=>$user->transactions],
                ]);
                
            }
        } else {
           return [];
        }
        
    }

    public function create()
    {
        if (!is_null(request()->input('customer_id'))) {
            $user = Customer::where('id',request()->input('customer_id'))->first(); 
            
            $this->validate(request(), [
                'type' => 'string|required',
                'amount' => 'integer|required',
                'confirmed' => 'string|required',
                
            ]);
            if(request()->input('type')=='deposit')
            $transaction = $user->deposit(request()->input('amount'),  ['description' => request()->input('description')], false); // not confirm
            if(request()->input('type')=='withdraw')
            $transaction = $user->withdraw(request()->input('amount'), ['description' => request()->input('description')], false); // not confirm
            
            if(request()->input('confirmed')=='true')
            $user->confirm($transaction); // bool(true)

            $user->wallet->refreshBalance();
            
            return response()->json([
                'message' => 'Transaction created successfully.',
                'data' => ["holder_name"=>$user->first_name.' '.$user->last_name,"wallet"=>$user->wallet,"transactions"=>$transaction],
            ]);
            
        }
            
       
        
    }

    public function update()
    {
       
        if (!is_null(request()->input('customer_id'))) {
            $user = Customer::where('id',request()->input('customer_id'))->first(); 
            
            $this->validate(request(), [
                'type' => 'string|required',
                'amount' => 'integer|required',
                'confirmed' => 'string|required',
                
            ]);
           /* if(request()->input('type')=='deposit')
            $transaction = $user->deposit(request()->input('amount'),  ['description' => request()->input('description')], false); // not confirm
            if(request()->input('type')=='withdraw')
            $transaction = $user->withdraw(request()->input('amount'), ['description' => request()->input('description')], false); // not confirm
            */

            $transaction = $user->transactions->find(request()->input('id'));
            $transaction->amount=request()->input('amount');
            $transaction->type=request()->input('type');

            if(request()->input('confirmed')=='true')
            $user->confirm($transaction); // bool(true)

            $user->wallet->refreshBalance();
            
            return response()->json([
                'message' => 'Transaction updated successfully.',
                'data' => ["holder_name"=>$user->first_name.' '.$user->last_name,"wallet"=>$user->wallet,"transactions"=>$transaction],
            ]);
            
        }
            
        
    }

    public function delete()
    {
        //$transaction = $user->transactions->find(request()->input('id'));
            
       
      try {
           
           // $user->transactions->delete('id',request()->input('id'));
            Transaction::where('id',request()->input('id'))->delete();
            
            return response()->json(['message' => true], 200);
       } catch (\Exception $e) {
            //return response()->json(['error' => $e], 200);
            report($e);
        }

        
        return response()->json(['message' => false], 200);

    }
}