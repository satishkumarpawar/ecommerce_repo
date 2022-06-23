<?php

namespace Webkul\API\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use ACME\UserSurvey\Repositories\UserSurveySetRepository;
use Webkul\API\Http\Resources\UserSurvey\UserSurveySet as UserSurveySetResource;
use ACME\UserSurvey\Repositories\UserSurveySetDetailRepository;
use Webkul\API\Http\Resources\UserSurvey\UserSurveySetDetail as UserSurveySetDetailResource;
use ACME\UserSurvey\Repositories\UserSurveyCategoryRepository;
use Webkul\API\Http\Resources\UserSurvey\UserSurveyCategory as UserSurveyCategoryResource;



class UserSurveyController extends Controller
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

    protected $UserSurveySetRepository;
    protected $UserSurveyCategoryRepository;
   
    public function __construct(
        UserSurveySetRepository $UserSurveySetRepository,
        UserSurveyCategoryRepository $UserSurveyCategoryRepository
    )
    {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        //$this->middleware('auth:' . $this->guard);

        $this->_config = request('_config');

        $this->UserSurveySetRepository = $UserSurveySetRepository;
        $this->UserSurveyCategoryRepository = $UserSurveyCategoryRepository;

    }
    
    public function index()
    {
        return response()->json(["message" => "Page Not Found!"], 401);
               
    }

    // User Survey Set Start

    public function getSurveySetList()
    {
        if (!is_null(request()->input('id'))) {
            return UserSurveySetResource::collection($this->UserSurveySetRepository->getAll(request()->input('id')));
        } else {
           return UserSurveySetResource::collection($this->UserSurveySetRepository->getAll());
        }
               
    }

       
    public function getSurveySet()
    {
        
        if (!is_null(request()->input('id'))) {
            return UserSurveySetResource::collection($this->UserSurveySetRepository->get(request()->input('id')));
        } else {
           return UserSurveySetResource::collection($this->UserSurveySetRepository->get());
        }
        
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

    public function updateSurveySet()
    {
        
       
        $this->validate(request(), [
            'survey_name' => 'required'
        ]);

        $SurveySet=$this->UserSurveySetRepository->update(request());

        return response()->json([
            'message' => 'Survey set updated successfully.',
            'data'    => new UserSurveySetResource($SurveySet),
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


}