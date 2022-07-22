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
    protected $guard;
    protected $_config;
    protected $customer_id;
    protected $user;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->guard = request()->has('token') ? 'api' : 'customer';
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
            if($this->user->hasWallet('default')==false){
                $wallet = $this->user->createWallet([
                    'name' => 'Default Wallet',
                    'slug' => 'default',
                ]);
            } 
            if($this->user->hasWallet('cash-back')==false){
                    $wallet = $this->user->createWallet([
                        'name' => 'Cash Back Wallet',
                        'slug' => 'cash-back',
                    ]);
            } 

        }

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

    public function getBalance()
    {

        if (!is_null($this->customer_id)) {
            
            $cashback_Wallet = $this->user->getWallet('cash-back');
            $cashback_Wallet->refreshBalance();
            $this->user->wallet->refreshBalance();
            
           // $transactions =Transaction::where("order_id",$order_id)->get();
            
            
          
           $balance_result = DB::select("SELECT id , SUM(balance) AS balance,allow_uses_cash_back,allow_uses_cash_back_type 
                FROM ( SELECT DISTINCT id, sum(amount) AS balance, allow_uses_cash_back, allow_uses_cash_back_type FROM `transactions` WHERE `wallet_id`=".$cashback_Wallet->id." and type='deposit' 
                UNION SELECT DISTINCT cash_back_id AS id, sum(amount) AS balance, allow_uses_cash_back, allow_uses_cash_back_type FROM `transactions` WHERE `wallet_id`=".$cashback_Wallet->id." and type='withdraw') t 
                group by id, allow_uses_cash_back, allow_uses_cash_back_type order by id ASC");

            $total_cashback_allowed=0;    
            if(count($balance_result)>0){
                foreach($balance_result as $balance){
                    if($balance->allow_uses_cash_back>$balance->balance)$total_cashback_allowed +=$balance->balance;
                    else $total_cashback_allowed +=$balance->allow_uses_cash_back;
                }
                if($total_cashback_allowed>$cashback_Wallet->balance)$total_cashback_allowed =$cashback_Wallet->balance;
                    
            }    
            return response()->json([
                'data' => ["default_wallet_balance"=>$this->user->wallet->balance,"cash_back_wallet_balance"=>$cashback_Wallet->balance,"cash_back_balance_allowed_to_pay"=>$total_cashback_allowed,"cash_back_wallet_balance_allowed"=>$balance_result],
            ]);
            
        }

        return response()->json([
            'message' => 'Page no found.',
        ]);
       
      
               
    }

    public function getWalletBalance()
    {

        if (!is_null($this->customer_id)) {
            
            $cashback_Wallet = $this->user->getWallet('cash-back');
            $cashback_Wallet->refreshBalance();
            $this->user->wallet->refreshBalance();
            
           // $transactions =Transaction::where("order_id",$order_id)->get();
            
            
          
           $balance_result = DB::select("SELECT id , SUM(balance) AS balance,allow_uses_cash_back,allow_uses_cash_back_type 
                FROM ( SELECT DISTINCT id, sum(amount) AS balance, allow_uses_cash_back, allow_uses_cash_back_type FROM `transactions` WHERE `wallet_id`=".$cashback_Wallet->id." and type='deposit' 
                UNION SELECT DISTINCT cash_back_id AS id, sum(amount) AS balance, allow_uses_cash_back, allow_uses_cash_back_type FROM `transactions` WHERE `wallet_id`=".$cashback_Wallet->id." and type='withdraw') t 
                group by id, allow_uses_cash_back, allow_uses_cash_back_type order by id ASC");

           
                $total_cashback_allowed=0;    
                if(count($balance_result)>0){
                    foreach($balance_result as $balance){
                        if($balance->allow_uses_cash_back>$balance->balance)$total_cashback_allowed +=$balance->balance;
                        else $total_cashback_allowed +=$balance->allow_uses_cash_back;
                    }
                    if($total_cashback_allowed>$cashback_Wallet->balance)$total_cashback_allowed =$cashback_Wallet->balance;
                        
                }

                return  ["default_wallet_balance"=>$this->user->wallet->balance,"cash_back_wallet_balance"=>$cashback_Wallet->balance,"cash_back_balance_allowed_to_pay"=>$total_cashback_allowed,"cash_back_wallet_balance_allowed"=>$balance_result];
            
        }

        return response()->json([
            'message' => 'Page no found.',
        ]);
       
      
               
    }

    public function getList()
    {

        if (!is_null($this->customer_id)) {
            
            $cashback_Wallet = $this->user->getWallet('cash-back');
            $cashback_Wallet->refreshBalance();
            $this->user->wallet->refreshBalance();
            
            return response()->json([
                'data' => ["holder_name"=>$this->user->first_name.' '.$this->user->last_name,"wallet"=>$this->user->wallet,"transactions"=>$this->user->transactions,"cash_back_wallet"=>$cashback_Wallet,"transactions"=>$this->user->transactions],
            ]);
            
        }

        return response()->json([
            'message' => 'Page no found.',
        ]);
       
      
               
    }

   
       
    public function get()
    {
        
        if (!is_null($this->customer_id)) {
           
            if(request()->input('slug')){
                $Wallet = $this->user->getWallet(request()->input('slug'));
            } else {
                $Wallet = $this->user->wallet;
            }
            
            $Wallet->refreshBalance();

            return response()->json([
                'data' => ["holder_name"=>$this->user->first_name.' '.$this->user->last_name,"wallet"=>$Wallet,"transactions"=>$this->user->transactions],
            ]);
            
         
        } else {
           return [];
        }
        
    }

    public function create()
    {
        if (!is_null($this->customer_id)) {
            
            $this->validate(request(), [
                'type' => 'string|required',
                'amount' => 'integer|required',
                'confirmed' => 'string|required',
                
            ]);
            if(request()->input('slug')){
                $Wallet = $this->user->getWallet(request()->input('slug'));
                if(request()->input('type')=='deposit')
                $transaction = $Wallet->deposit(request()->input('amount'),  ['description' => request()->input('description')], false); // not confirm
                if(request()->input('type')=='withdraw')
                $transaction = $Wallet->withdraw(request()->input('amount'), ['description' => request()->input('description')], false); // not confirm
                
                if(request()->input('confirmed')=='true'){
                    if (!is_null(request()->input('action_type'))) $transaction->action_type=request()->input('action_type');
                    if (!is_null(request()->input('order_id'))) $transaction->order_id=request()->input('order_id');
                    if (!is_null(request()->input('cash_back_id'))) $transaction->cash_back_id=request()->input('cash_back_id');
                    if (!is_null(request()->input('allow_uses_cash_back'))) $transaction->allow_uses_cash_back=request()->input('allow_uses_cash_back');
                    if (!is_null(request()->input('allow_uses_cash_back_type'))) $transaction->allow_uses_cash_back_type=request()->input('allow_uses_cash_back_type');
                       
                    $Wallet->confirm($transaction); // bool(true)
                }

                $Wallet->refreshBalance();
                
                return response()->json([
                    'message' => 'Transaction created successfully.',
                    'data' => ["holder_name"=>$this->user->first_name.' '.$this->user->last_name,"wallet"=>$Wallet,"transactions"=>$transaction],
                ]);
            } else {

                if(request()->input('type')=='deposit')
                $transaction = $this->user->deposit(request()->input('amount'),  ['description' => request()->input('description')], false); // not confirm
                if(request()->input('type')=='withdraw')
                $transaction = $this->user->withdraw(request()->input('amount'), ['description' => request()->input('description')], false); // not confirm
                
                if(request()->input('confirmed')=='true'){
                    if (!is_null(request()->input('action_type')))
                        $transaction->action_type=request()->input('action_type');
                    $this->user->confirm($transaction); // bool(true)
                }

                $this->user->wallet->refreshBalance();
                
                return response()->json([
                    'message' => 'Transaction created successfully.',
                    'data' => ["holder_name"=>$this->user->first_name.' '.$this->user->last_name,"wallet"=>$this->user->wallet,"transactions"=>$transaction],
                ]);
                
            }
        }
  
        
    }

    public function recharge()
    {
        if (!is_null($this->customer_id)) {
            
            $this->validate(request(), [
                'type' => 'string|required',
                'amount' => 'integer|required',
                'confirmed' => 'string|required',
                
            ]);
            if(request()->input('slug')){
                $Wallet = $this->user->getWallet(request()->input('slug'));
                if(request()->input('type')=='deposit')
                $transaction = $Wallet->deposit(request()->input('amount'),  ['description' => request()->input('description')], false); // not confirm
                if(request()->input('type')=='withdraw')
                $transaction = $Wallet->withdraw(request()->input('amount'), ['description' => request()->input('description')], false); // not confirm
                
                if(request()->input('confirmed')=='true'){
                    if (!is_null(request()->input('action_type'))) $transaction->action_type=request()->input('action_type');
                    if (!is_null(request()->input('order_id'))) $transaction->order_id=request()->input('order_id');
                    if (!is_null(request()->input('cash_back_id'))) $transaction->cash_back_id=request()->input('cash_back_id');
                    if (!is_null(request()->input('allow_uses_cash_back'))) $transaction->allow_uses_cash_back=request()->input('allow_uses_cash_back');
                    if (!is_null(request()->input('allow_uses_cash_back_type'))) $transaction->allow_uses_cash_back_type=request()->input('allow_uses_cash_back_type');
                       
                    $Wallet->confirm($transaction); // bool(true)
                }

                $Wallet->refreshBalance();
                
                return response()->json([
                    'message' => 'Transaction created successfully.',
                    'data' => ["holder_name"=>$this->user->first_name.' '.$this->user->last_name,"wallet"=>$Wallet,"transactions"=>$transaction],
                ]);
            } else {

                if(request()->input('type')=='deposit')
                $transaction = $this->user->deposit(request()->input('amount'),  ['description' => request()->input('description')], false); // not confirm
                if(request()->input('type')=='withdraw')
                $transaction = $this->user->withdraw(request()->input('amount'), ['description' => request()->input('description')], false); // not confirm
                
                if(request()->input('confirmed')=='true'){
                    if (!is_null(request()->input('action_type')))
                        $transaction->action_type=request()->input('action_type');
                    $this->user->confirm($transaction); // bool(true)
                }

                $this->user->wallet->refreshBalance();
                
                return response()->json([
                    'message' => 'Transaction created successfully.',
                    'data' => ["holder_name"=>$this->user->first_name.' '.$this->user->last_name,"wallet"=>$this->user->wallet,"transactions"=>$transaction],
                ]);
                
            }
        }

      
       
        
    }


    public function update()
    {
       
        if (!is_null($this->customer_id)) {
            
            $this->validate(request(), [
                'type' => 'string|required',
                'amount' => 'integer|required',
                'confirmed' => 'string|required',
                
            ]);
           /* if(request()->input('type')=='deposit')
            $transaction = $this->user->deposit(request()->input('amount'),  ['description' => request()->input('description')], false); // not confirm
            if(request()->input('type')=='withdraw')
            $transaction = $this->user->withdraw(request()->input('amount'), ['description' => request()->input('description')], false); // not confirm
            */

            $transaction = $this->user->transactions->find(request()->input('id'));
            $transaction->amount=request()->input('amount');
            $transaction->type=request()->input('type');
            $transaction->meta=['description' => request()->input('description')];
          

            if(request()->input('confirmed')=='true')
            $this->user->confirm($transaction); // bool(true)

            if(request()->input('slug')){
                $Wallet = $this->user->getWallet(request()->input('slug'));
                $Wallet->refreshBalance();
                return response()->json([
                    'message' => 'Transaction updated successfully.',
                    'data' => ["holder_name"=>$this->user->first_name.' '.$this->user->last_name,"wallet"=>$Wallet,"transactions"=>$transaction],
                ]);
                
            } else {
                $this->user->wallet->refreshBalance();
                return response()->json([
                    'message' => 'Transaction updated successfully.',
                    'data' => ["holder_name"=>$this->user->first_name.' '.$this->user->last_name,"wallet"=>$this->user->wallet,"transactions"=>$transaction],
                ]);
                
            }
            
            
           
        }
            
        
    }

    public function refund($order_id,$customer_id,$order_total)
    {
        if (!is_null($order_id) && !is_null($customer_id)) {
                $user = Customer::where('id',$customer_id)->first(); 

                
                //$Wallet = $user->wallet;
                $cashback_Wallet = $user->getWallet('cash-back');

                $transactions = $user->transactions->find("order_id",$order_id);
               // $transactions =Transaction::where("order_id",$order_id)->get();
                $total_cachboack_refund=0;
                foreach($transactions as $transaction){
                    if($transaction->action_type=='cash_back'){
                        $total_cachboack_refund +=$transaction->amount;
                        $t = $cashback_Wallet->deposit($transaction->amount,  ['description' => 'Refund'],false); 
                        $t->order_id=$transaction->order_id;
                        $t->action_type='refund';
                        //$t->cash_back_id=$transaction->cash_back_id;
                        $t_prev = $user->transactions->find($transaction->cash_back_id);
                        $t->allow_uses_cash_back=$t_prev->allow_uses_cash_back;
                        $t->allow_uses_cash_back_type=$t_prev->allow_uses_cash_back_type;

                        $cashback_Wallet->confirm($t);
                    }
                    
                }
                
                $cashback_Wallet->refreshBalance();

                $balance_refund =$order_total-$total_cachboack_refund;
                $t = $user->deposit($balance_refund,  ['description' => 'Refund'],false); 
                        $t->order_id=$order_id;
                        $t->action_type='refund';
                        $user->confirm($t);       
                $user->wallet->refreshBalance();

                
                
                return response()->json([
                    'data' => true,
                    'message' => 'Transaction created successfully.',
                    ]);
       }
            
    }

    public function payment($order=Array(),$data=Array())
    {
        //return $order;
        $order_id=$order->id;
        $order_total=floatval($order->grand_total);
        if (!is_null($this->customer_id)) {
            $wallet_pay_order_total=$order_total;
            $WalletBalance=$this->getWalletBalance();
            if(intval($data["razorpay_amount"])>0)$wallet_pay_order_total -=$data["razorpay_amount"];

            if($data["cash_back_wallet_pay"]==true || $data["cash_back_wallet_pay"]=="true"){
                
                if(count($WalletBalance["cash_back_wallet_balance_allowed"])>0){
                    $cashback_Wallet=$this->user->getWallet('cash-back');
                    $wallet_cashback_allowed=$order_total*50/100;
                    if($wallet_pay_order_total<$wallet_cashback_allowed)$wallet_cashback_allowed=$wallet_pay_order_total;
                   
                    foreach($WalletBalance["cash_back_wallet_balance_allowed"] as $balance){
                        
                        if($wallet_pay_order_total==0)break;
                        if($wallet_cashback_allowed==0)break;

                        $balance->balance=floatval($balance->balance);

                        

                        if($wallet_cashback_allowed>$balance->balance){
                            if($balance->balance>0){
                                $t=$cashback_Wallet->withdraw($balance->balance, ['title:'=>'Order No:'.$order_id,'description' => 'Order No:'.$order_id],false); 
                                $t->order_id=$order_id;
                                $t->action_type='payment';
                                $t->cash_back_id=$balance->id;
                                $cashback_Wallet->confirm($t);     
                            }
                            $wallet_pay_order_total -=$balance->balance;
                            $wallet_cashback_allowed -=$balance->balance;
                        } else {
                            return $wallet_cashback_allowed.">".$balance->balance;
                            if($wallet_cashback_allowed>0){
                                $t=$cashback_Wallet->withdraw($wallet_cashback_allowed, ['title:'=>'Order No:'.$order_id,'description' => 'Order No:'.$order_id],false); 
                                $t->order_id=$order_id;
                                $t->action_type='payment';
                                $t->cash_back_id=$balance->id;
                                $cashback_Wallet->confirm($t);    
                            }
                            $wallet_pay_order_total -=$wallet_cashback_allowed;
                            $wallet_cashback_allowed=0;
                        }

                        /*if($balance->allow_uses_cash_back>$wallet_pay_order_total){
                            if($wallet_pay_order_total>0)$t=$cashback_Wallet->withdraw($wallet_pay_total, ['title:'=>'Order No:'.$order_id,'description' => 'Order No:'.$order_id],false); 
                            $wallet_pay_order_total=0;
                        } elseif($balance->allow_uses_cash_back>$balance->balance){
                            if($balance->balance>0)$t=$cashback_Wallet->withdraw($balance->balance, ['title:'=>'Order No:'.$order_id,'description' => 'Order No:'.$order_id],false); 
                            $wallet_pay_order_total -=$balance->balance;
                        } elseif($balance->allow_uses_cash_back>$WalletBalance->cash_back_balance_allowed_to_pay){
                            if($WalletBalance->cash_back_balance_allowed_to_pay>0)$t=$cashback_Wallet->withdraw($balance->balance, ['title:'=>'Order No:'.$order_id,'description' => 'Order No:'.$order_id],false); 
                            $wallet_pay_order_total -=$WalletBalance->cash_back_balance_allowed_to_pay;
                        } else {
                            if($balance->allow_uses_cash_back>0)$t=$cashback_Wallet->withdraw($balance->allow_uses_cash_back, ['title:'=>'Order No:'.$order_id,'description' => 'Order No:'.$order_id],false); 
                            $wallet_pay_order_total -=$balance->allow_uses_cash_back;
                        }*/

                        
                       
                    }
                        
                }

                /*if($order_total>500){
                    $cashback_amount=($order_total*5/100);
                    $t = $user->deposit($cashback_amount,  ['description' => 'Cash Back'],false); 
                    $t->order_id=$order_id;
                    $t->allow_uses_cash_back=$cashback_amount;
                    $user->confirm($t);  
                }*/


                /* at once
                if($wallet_pay_order_total>0){
                    if($WalletBalance->cash_back_wallet_balance>0){
                        $wallet_cashback_allowed=$order_total*50/100;
                        if($wallet_cashback_allowed>$WalletBalance->cash_back_wallet_balance)$wallet_cashback_allowed=$WalletBalance->cash_back_wallet_balance;
                        
                        $t=$cashback_Wallet->withdraw($wallet_cashback_allowed, ['title:'=>'Order No:'.$order_id,'description' => 'Order No:'.$order_id],false); 
                        $t->order_id=$order_id;
                        $t->action_type='payment';
                        $this->user->confirm($t);       
                        $this->user->wallet->refreshBalance();
                        $wallet_pay_order_total -=$wallet_cashback_allowed;
                        
                    }
                }*/
                
                $cashback_Wallet->refreshBalance();
                
            }

            if($data["default_wallet_pay"]==true || $data["default_wallet_pay"]=="true"){
                if($wallet_pay_order_total>0){
                    $t=$this->user->withdraw($wallet_pay_order_total, ['title:'=>'Order No:'.$order_id,'description' => 'Order No:'.$order_id],false); 
                    $t->order_id=$order_id;
                    $t->action_type='payment';
                    $this->user->confirm($t);       
                    $this->user->wallet->refreshBalance();
                }
            }

        
            

            return true;
       }
            
       return false;
    }

    public function delete()
    {
        //$transaction = $this->user->transactions->find(request()->input('id'));
            
       
      try {
           
           // $this->user->transactions->delete('id',request()->input('id'));
            Transaction::where('id',request()->input('id'))->delete();
            
            return response()->json(['message' => true], 200);
       } catch (\Exception $e) {
            //return response()->json(['error' => $e], 200);
            report($e);
        }

        
        return response()->json(['message' => false], 200);

    }
}