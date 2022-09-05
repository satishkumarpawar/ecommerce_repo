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

//use Wontonee\Razorpay\Http\Controllers\RazorpayPaymentController;

//use Razorpay\Api\Api;
//use Razorpay\Api\Errors\SignatureVerificationError;
use App\Http\Controllers\RazorpayPaymentController;


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

    public function getRazorPayOrderID(){
       $data = request()->all();
        
 		      //$api = new Api('rzp_test_5Bqs3Vq0tHFEEm','yc3rA790jWwPcOXXSy04v7wS');//lavkush ki key
             // $api = new Api(core()->getConfigData('sales.paymentmethods.razorpay.key_id'), core()->getConfigData('sales.paymentmethods.razorpay.secret'));

             // $api = new Api(env('RAZOR_KEY'), env('RAZOR_SECRET'));
             //include 'E:\Laravel\bagisto\vendor\wontonee\razorpay\src\razorpay-php\Razorpay.php';
             $rzpay_order=RazorpayPaymentController::createOrder($data);
		      
	
        /*return response()->json([
            'success' => true,
            'data'   => $rzpay_order,
        ]);*/
        return response()->json($rzpay_order);
        
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
                $description="";
                if (!is_null(request()->input('description')))$description=request()->input('description');
                if (!is_null(request()->input('razorpay_transaction_id'))) $description="Razorpay Transaction#: ".request()->input('razorpay_transaction_id')."<br>".$description;
                if (!is_null(request()->input('order_id')) && intval(request()->input('order_id'))>0) $description="Order No:".request()->input('order_id')."<br>".$description;
                
                
                if(request()->input('type')=='deposit')
                $transaction = $this->user->deposit(request()->input('amount'),  ['description' => $description], false); // not confirm
                if(request()->input('type')=='withdraw')
                $transaction = $this->user->withdraw(request()->input('amount'), ['description' => $description], false); // not confirm
                
                if(request()->input('confirmed')=='true'){
                    if (!is_null(request()->input('action_type')))
                        $transaction->action_type=request()->input('action_type');
                        if (!is_null(request()->input('order_id')) && intval(request()->input('order_id'))>0) $transaction->order_id=request()->input('order_id');
                        if (!is_null(request()->input('razorpay_transaction_id'))) $transaction->razorpay_transaction_id=request()->input('razorpay_transaction_id');
                    
                    $this->user->confirm($transaction); // bool(true)
                    //Offer Bonus
                    if(request()->input('type')=='deposit' && (is_null(request()->input('order_id')) || intval(request()->input('order_id'))==0) && (is_null(request()->input('cart_id')) || intval(request()->input('cart_id'))==0)){
                        $bonus_arr=$this->getRechargeBonusCalculate(request()->input('amount'));
                        $percentage="";
                        if(is_array($bonus_arr)){
                            $bonus=$bonus_arr["amount"];
                            $percentage=$bonus_arr["percentage"];
                        } else $bonus=$bonus_arr;
                
                        if($bonus>0){
                            $transaction1 = $this->user->deposit($bonus,  ['description' => "Bonus ".$percentage." on <br>".$description], false); // not confirm
                            $transaction1->action_type="recharge_bonus";
                            if (!is_null(request()->input('order_id'))) $transaction1->order_id=request()->input('order_id');
                            if (!is_null(request()->input('razorpay_transaction_id'))) $transaction->razorpay_transaction_id=request()->input('razorpay_transaction_id');
                    
                            $this->user->confirm($transaction1); 
                        }
                    }
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

    /*
    public function payment($order=Array(),$data=Array())
    {
        //return $order;
        $order_id=$order->id;
        $order_total=floatval($order->grand_total);
        $data["walletpay_amount"]=0;
        $data["cashback_walletpay_amount"]=0;
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
                                $data["cashback_walletpay_amount"] +=$balance->balance;
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
                                $data["cashback_walletpay_amount"] +=$balance->balance;
                                $cashback_Wallet->confirm($t);    
                            }
                            $wallet_pay_order_total -=$wallet_cashback_allowed;
                            $wallet_cashback_allowed=0;
                        }
*/
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

 /*                       
                       
                    }
                        
                }
*/
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
  /*              
                $cashback_Wallet->refreshBalance();
                
            }

            $wallet=$this->user->getWallet('default');
                
            if($data["default_wallet_pay"]==true || $data["default_wallet_pay"]=="true"){
                if($wallet_pay_order_total>0){
                    $t=$wallet->withdraw($wallet_pay_order_total, ['title:'=>'Order No:'.$order_id,'description' => 'Order No:'.$order_id],false); 
                    $t->order_id=$order_id;
                    $t->action_type='payment';
                    $wallet->confirm($t);  
                    $data["walletpay_amount"] +=$wallet_pay_order_total;     
                    $wallet->refreshBalance();
                }
            }

        
            if($data["walletpay_amount"]==0)unset($data["walletpay_amount"]);
            if($data["cashback_walletpay_amount"]==0)unset($data["cashback_walletpay_amount"]);
            if(intval($data["razorpay_amount"])>0)$data["payment_type"]='Online';
            else $data["payment_type"]='Wallet';
            if(!isset($data["razorpay_transaction_id"]) || $data["razorpay_transaction_id"]=='' || $data["razorpay_transaction_id"]==null)$data["razorpay_transaction_id"]='O: '.$order_id.', w: '.$wallet->id;
            if(isset($data["default_wallet_pay"])) unset($data["default_wallet_pay"]);
            if(isset($data["cash_back_wallet_pay"])) unset($data["cash_back_wallet_pay"]);


            return $data;

            //return true;
       }
            
       return [];
    }
*/


    public function payment($order=Array(),$data=Array())
    {
        //return $order;
        $order_id=$order->id;
        $order_total=floatval($order->grand_total);
        $data["walletpay_amount"]=0;
        $data["cashback_walletpay_amount"]=0;
        if (!is_null($this->customer_id)) {
           
            $wallet_pay_order_total=$order_total;
            $WalletBalance=$this->getWalletBalance();
            //if(intval($data["razorpay_amount"])>0)$wallet_pay_order_total -=$data["razorpay_amount"];

            if($data["cash_back_wallet_pay"]==true || $data["cash_back_wallet_pay"]=="true"){
                
                if(count($WalletBalance["cash_back_wallet_balance_allowed"])>0){
                    $cashback_Wallet=$this->user->getWallet('cash-back');
                    $custom_cash_back_wallet_balance_allowed =  core()->getConfigData('general.general.cash_back_wallet_balance_allowed.custom_cash_back_wallet_balance_allowed') ?? '0';
                    $custom_cash_back_wallet_balance_allowed = intval($custom_cash_back_wallet_balance_allowed);
                    $wallet_cashback_allowed=$order_total*$custom_cash_back_wallet_balance_allowed/100;
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
                                $data["cashback_walletpay_amount"] +=$balance->balance;
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
                                $data["cashback_walletpay_amount"] +=$balance->balance;
                                $cashback_Wallet->confirm($t);    
                            }
                            $wallet_pay_order_total -=$wallet_cashback_allowed;
                            $wallet_cashback_allowed=0;
                        }

                        
                       
                    }
                        
                }

                
                $cashback_Wallet->refreshBalance();
                
            }

            $wallet=$this->user->getWallet('default');
             //update previous recharge
             if(isset($data['razorpay_transaction_id'])){
                $transactions = $wallet->transactions->find($data['razorpay_transaction_id']);
                if(is_array($transactions)){
                    foreach($transactions as $t){
                        $t->order_id=$order_id;
                        $description="Order No:".$order_id."<br>".$t->meta["description"];
                        $t->meta=['description' => $description];
                        $wallet->confirm($t); // bool(true)
                    
                    }
                }
            }
            if(!isset($data["default_wallet_pay"]))$data["default_wallet_pay"]=true;    
            if($data["default_wallet_pay"]==true || $data["default_wallet_pay"]=="true"){
                if($wallet_pay_order_total>0){
                    $t=$wallet->withdraw($wallet_pay_order_total, ['title:'=>'Order No:'.$order_id,'description' => 'Order No:'.$order_id],false); 
                    $t->order_id=$order_id;
                    $t->action_type='payment';
                    $wallet->confirm($t);  
                    $data["walletpay_amount"] +=$wallet_pay_order_total;     
                    $wallet->refreshBalance();
                }
            }

        
            if($data["walletpay_amount"]==0)unset($data["walletpay_amount"]);
            if($data["cashback_walletpay_amount"]==0)unset($data["cashback_walletpay_amount"]);
            if(intval($data["razorpay_amount"])>0)$data["payment_type"]='Online';
            else $data["payment_type"]='Wallet';
            if(!isset($data["razorpay_transaction_id"]) || $data["razorpay_transaction_id"]=='' || $data["razorpay_transaction_id"]==null)$data["razorpay_transaction_id"]='O: '.$order_id.', w: '.$wallet->id;
            if(isset($data["default_wallet_pay"])) unset($data["default_wallet_pay"]);
            if(isset($data["cash_back_wallet_pay"])) unset($data["cash_back_wallet_pay"]);


            return $data;

            //return true;
       }
            
       return [];
    }


    public function getWalletRechargeOffers($return_arr=false)
    {
        $custom_wallet_recharge_offers=Array();
        $custom_wallet_recharge_offers_str =  core()->getConfigData('general.general.wallet_recharge_offer.custom_wallet_recharge_offers') ?? '';
        if($custom_wallet_recharge_offers_str!='') {
            $custom_wallet_recharge_offers_arr=explode("\n",$custom_wallet_recharge_offers_str); 
            if(count($custom_wallet_recharge_offers_arr)>0){
                foreach($custom_wallet_recharge_offers_arr as $key=>$val){
                    $val_arr=explode(",",$val); 
                    if(count($val_arr)>=3){
                            if(isset($val_arr[0])){
                               if(intval(trim($val_arr[0])>0)) {
                                $custom_wallet_recharge_offers[$key]["amount"]=intval(trim($val_arr[0]));
                                    if(isset($val_arr[1])){
                                        if(strpos($val_arr[1],'%')!==false) {
                                            $custom_wallet_recharge_offers[$key]["bonus"]=intval(str_replace("%","",trim($val_arr[1])));
                                            $custom_wallet_recharge_offers[$key]["bonus_type"]="percentage";
                                        } else {
                                            $custom_wallet_recharge_offers[$key]["bonus"]=intval(trim($val_arr[1]));
                                            $custom_wallet_recharge_offers[$key]["bonus_type"]="fixed";
                                        }
                                    }
                                    if(isset($val_arr[2])){
                                       $custom_wallet_recharge_offers[$key]["description"]=trim($val_arr[2]); 
                                    }
                               }
                               
                            }
                        
                    }

                    
                }
            }
        }
        
        if($return_arr){
            return $custom_wallet_recharge_offers;
        } else {
            return response()->json([
                'data' => [
                    'wallet_recharge_offers' => $custom_wallet_recharge_offers,
                ]
            ]);
        }
        
    }

    

    public function getRechargeBonusCalculate($amount=0){
        $RechargeOffersRule=Array();
        $RechargeOffers=$this->getWalletRechargeOffers(true);
        if(count($RechargeOffers)>0){
            $offer_amount = array();
            foreach($RechargeOffers as $key => $row){
                $offer_amount[$key] = $row['amount'];
            }
            array_multisort($offer_amount, SORT_DESC, $RechargeOffers);

            foreach($RechargeOffers as $key => $row){
                if($amount>=$row['amount']){
                    $RechargeOffersRule=$row;
                    break;
                }
            }

            if(count($RechargeOffersRule)>0){
                if($RechargeOffersRule["bonus_type"]=='fixed') return $RechargeOffersRule["bonus"];
                else  return ["amount"=>($amount*$RechargeOffersRule["bonus"]/100),"percentage"=>$RechargeOffersRule["bonus"]."%"];
            } else {
                return 0;
            }

        } else {
            return 0;
        }

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