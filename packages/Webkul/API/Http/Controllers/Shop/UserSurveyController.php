<?php

namespace Webkul\API\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use ACME\UserSurvey\Repositories\UserSurveyRepository;
use Webkul\API\Http\Resources\UserSurvey\UserSurvey as UserSurveyResource;
use ACME\UserSurvey\Repositories\UserSurveyDetailRepository;
use Webkul\API\Http\Resources\UserSurvey\UserSurveyDetail as UserSurveyDetailResource;
use ACME\UserSurvey\Repositories\UserSurveySetRepository;
use Webkul\API\Http\Resources\UserSurvey\UserSurveySet as UserSurveySetResource;
use ACME\UserSurvey\Repositories\UserSurveySetDetailRepository;
use Webkul\API\Http\Resources\UserSurvey\UserSurveySetDetail as UserSurveySetDetailResource;
use ACME\UserSurvey\Repositories\UserSurveyCategoryRepository;
use Webkul\API\Http\Resources\UserSurvey\UserSurveyCategory as UserSurveyCategoryResource;
use ACME\UserSurvey\Repositories\UserSurveyQuestionRepository;
use Webkul\API\Http\Resources\UserSurvey\UserSurveyQuestion as UserSurveyQuestionResource;
use Webkul\API\Http\Resources\UserSurvey\UserSurveyAnswer as UserSurveyAnswerResource;

use JWTAuth; #SKP

use Webkul\API\Http\Controllers\Shop\WalletController;
use Bavix\Wallet\Models\Transaction;

use Webkul\Customer\Models\Customer;

use Bavix\Wallet\Objects\Cart as WalletCart;


class UserSurveyController extends Controller
{
    /**
     * Contains current guard
     *
     * @var array
     */
    protected $guard;
    protected $customer_id;
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    protected $UserSurveyRepository;
    protected $UserSurveySetRepository;
    protected $UserSurveyCategoryRepository;
    protected $UserSurveyQuestionRepository;
   
    public function __construct(
        UserSurveyRepository $UserSurveyRepository,
        UserSurveySetRepository $UserSurveySetRepository,
        UserSurveyCategoryRepository $UserSurveyCategoryRepository,
        UserSurveyQuestionRepository $UserSurveyQuestionRepository
    )
    {
        $this->guard = request()->has('token') ? 'api' : 'customer';
        #SKP Start
        //need to modify
        $customer = auth($this->guard)->user();
            
        if(isset($customer)) {
            auth()->setDefaultDriver($this->guard);
            $this->middleware('auth:' . $this->guard);

            if(isset($customer->id))$this->customer_id=$customer->id;
            else $this->customer_id=request()->input('customer_id');
        } else {
            $this->middleware('admin');
        }



        //$this->middleware('auth:' . $this->guard);

        $this->_config = request('_config');
       
        $this->UserSurveyRepository = $UserSurveyRepository;
        $this->UserSurveySetRepository = $UserSurveySetRepository;
        $this->UserSurveyCategoryRepository = $UserSurveyCategoryRepository;
        $this->UserSurveyQuestionRepository = $UserSurveyQuestionRepository;

    }
    
    public function index()
    {
        return response()->json([
            'message' => 'Page no found.',
        ]);
               
    }

    // User Survey  Start

    public function getList()
    {
        if (!is_null(request()->input('id'))) {
            return UserSurveyResource::collection($this->UserSurveyRepository->getAll(request()->input('id')));
        } else {
           return UserSurveyResource::collection($this->UserSurveyRepository->getAll());
        }
               
    }

       
    public function get()
    {
        
        if (!is_null(request()->input('id'))) {
            return UserSurveyResource::collection($this->UserSurveyRepository->get(request()->input('id')));
        } else {
           return UserSurveyResource::collection($this->UserSurveyRepository->get());
        }
        
    }

    public function create()
    {
       
         $request["request"]=request();
         $request["user_id"]=($this->customer_id?$this->customer_id:null);
        
        $this->validate(request(), [
            'survey_set_id' => 'required',
        ]);
       
        $Survey=$this->UserSurveyRepository->create($request);
        $survey_set=$this->getSurveySet(request()->survey_set_id);
        if($survey_set->cash_back>0){
            $cash_back_amount=$survey_set->cash_back;
            //$wallet= new WalletController();
            $customer = auth($this->guard)->user();
            $cashback_Wallet = $customer->getWallet('cash-back');
            $transaction1 = $cashback_Wallet->deposit($cash_back_amount,  ['description' => "Cash back on survey #".$Survey->id], false); // not confirm
            $transaction1->action_type="cash_back";
            
            $cashback_Wallet->confirm($transaction1); 
        }

        return response()->json([
            'message' => 'Survey created successfully.',
            'data'    => new UserSurveyResource($Survey),
        ]);
        
    }

    public function update()
    {
       
       
        $this->validate(request(), [
            'survey_set_id' => 'required'
        ]);

        $Survey=$this->UserSurveyRepository->update(request());

        return response()->json([
            'message' => 'Survey updated successfully.',
            'data'    => new UserSurveyResource($Survey),
        ]);
        
    }

    public function delete()
    {
        $Survey = $this->UserSurveyRepository->findOrFail(request()->id);

        try {
           
            $this->UserSurveyRepository->delete(request()->id);

            return response()->json(['message' => true], 200);
        } catch (\Exception $e) {
            report($e);
        }

        return response()->json(['message' => false], 400);

    }

// User Survey End

    // User Survey Set Start

    public function getSurveySetList()
    {
        if (!is_null(request()->input('id'))) {
            return UserSurveySetResource::collection($this->UserSurveySetRepository->getAll(request()->input('id')));
        } else {
            if (!is_null(request()->input('short_info'))) {
                return $this->UserSurveySetRepository->getList();
            } else {
                return UserSurveySetResource::collection($this->UserSurveySetRepository->getAll());
            }
        }
               
    }

       
    public function getSurveySet($id=null)
    {
        
        if (!is_null(request()->input('id'))) {
            $data= UserSurveySetResource::collection($this->UserSurveySetRepository->get(request()->input('id')));
        } elseif (!is_null($id)) {
            $data= UserSurveySetResource::collection($this->UserSurveySetRepository->get($id));
        } else {
            $data= UserSurveySetResource::collection($this->UserSurveySetRepository->get());
        }
        $data = (count($data)>0?$data[0]:$data);
        return $data;
    }

    public function createSurveySet()
    {
        
       
        $this->validate(request(), [
            'survey_name' => 'required'
        ]);

        $SurveySet=$this->UserSurveySetRepository->create(request());

        return response()->json([
            'message' => 'Survey Set created successfully.',
            'data'    => new UserSurveySetResource($SurveySet),
        ]);
        
    }
    public function addQuestonSurveySet()
    {
        
       
       /* $this->validate(request(), [
            'question_id' => 'required',
            'surey_set_id' => 'required'
        ]);*/

        $SurveySet=$this->UserSurveySetRepository->addQuestion(request());

        return response()->json([
            'message' => 'Selected questions are added to Survey Set successfully.',
            'data'    => $SurveySet,
        ]);
        
    }
    public function updateSurveySet()
    {
        
       
        $this->validate(request(), [
            'survey_name' => 'required'
        ]);

        $SurveySet=$this->UserSurveySetRepository->update(request());
        $SurveySet= UserSurveySetResource::collection($this->UserSurveySetRepository->get(request()->input('id')));
        return response()->json([
            'message' => 'Survey set updated successfully.',
            'data'    => $SurveySet->first() //new UserSurveySetResource($SurveySet),
        ]);
        
    }

    public function deleteSurveySet()
    {
        $SurveySet = $this->UserSurveySetRepository->findOrFail(request()->id);

        try {
           
            $this->UserSurveySetRepository->delete(request()->id);

            return response()->json(['message' => true], 200);
        } catch (\Exception $e) {
            report($e);
        }

        return response()->json(['message' => false], 400);

    }

// User Survey Set End


// User Survey Category Start

public function getCategoryList()
{
    if (!is_null(request()->input('id'))) {
        return UserSurveyCategoryResource::collection($this->UserSurveyCategoryRepository->getAll(request()->input('id')));
    } else {
       return UserSurveyCategoryResource::collection($this->UserSurveyCategoryRepository->getAll());
    }
           
}

   
public function getCategory()
{
    
    if (!is_null(request()->input('id'))) {
        return UserSurveyCategoryResource::collection($this->UserSurveyCategoryRepository->get(request()->input('id')));
    } else {
       return UserSurveyCategoryResource::collection($this->UserSurveyCategoryRepository->get());
    }
    
}

public function createCategory()
{
    
   
    $this->validate(request(), [
        'cate_name' => 'required'
    ]);

    $SurveyCategory=$this->UserSurveyCategoryRepository->create(request());

    return response()->json([
        'message' => 'Survey Category created successfully.',
        'data'    => new UserSurveyCategoryResource($SurveyCategory),
    ]);
    
}

public function updateCategory()
{
    
   
    $this->validate(request(), [
        'cate_name' => 'required'
    ]);

    $SurveyCategory=$this->UserSurveyCategoryRepository->update(request());

    return response()->json([
        'message' => 'Survey Category updated successfully.',
        'data'    => new UserSurveyCategoryResource($SurveyCategory),
    ]);
    
}

public function deleteCategory()
{
    $SurveyCategory = $this->UserSurveyCategoryRepository->findOrFail(request()->id);

    try {
       
        $this->UserSurveyCategoryRepository->delete(request()->id);

        return response()->json(['message' => true], 200);
    } catch (\Exception $e) {
        report($e);
    }

    return response()->json(['message' => false], 400);

}

// User Survey Category End


// User Survey Questions Start

public function getQuestionList()
{
    if (!is_null(request()->input('id'))) {
        return UserSurveyQuestionResource::collection($this->UserSurveyQuestionRepository->getAll(request()->input('id')));
    } else {
       return UserSurveyQuestionResource::collection($this->UserSurveyQuestionRepository->getAll());
    }
           
}

   
public function getQuestion()
{
    
    if (!is_null(request()->input('id'))) {
        return UserSurveyQuestionResource::collection($this->UserSurveyQuestionRepository->get(request()->input('id')));
    } else {
       return UserSurveyQuestionResource::collection($this->UserSurveyQuestionRepository->get());
    }
    
}

public function createQuestion()
{
    
   
    $this->validate(request(), [
        'question_text' => 'required'
    ]);

    $SurveyQuestion=$this->UserSurveyQuestionRepository->create(request());

    return response()->json([
        'message' => 'Survey question created successfully.',
        'data'    => new UserSurveyQuestionResource($SurveyQuestion),
    ]);
    
}

public function updateQuestion()
{
    
   
    $this->validate(request(), [
        'question_text' => 'required'
    ]);

    $SurveyQuestion=$this->UserSurveyQuestionRepository->update(request());

    return response()->json([
        'message' => 'Survey question updated successfully.',
        'data'    => new UserSurveyQuestionResource($SurveyQuestion),
    ]);
    
}

public function deleteQuestion()
{
    $SurveyQuestion = $this->UserSurveyQuestionRepository->findOrFail(request()->id);

    try {
       
        $this->UserSurveyQuestionRepository->delete(request()->id);

        return response()->json(['message' => true], 200);
    } catch (\Exception $e) {
        report($e);
    }

    return response()->json(['message' => false], 400);

}

// User Survey Questions End


}