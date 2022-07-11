<?php

namespace ACME\UserSurvey\Repositories;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use ACME\UserSurvey\Models\UserSurveyCategory;
use ACME\UserSurvey\Models\UserSurveyQuestion;
use ACME\UserSurvey\Repositories\UserSurveyCategorySearchRepository;
use ACME\UserSurvey\Repositories\UserSurveyQuestionRepository;
use Illuminate\Pagination\Paginator;
use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Container\Container as App;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserSurveyCategoryRepository extends Repository
{

    protected $UserSurveyCategorySearchRepository;
   
    public function __construct(
        UserSurveyCategorySearchRepository $UserSurveyCategorySearchRepository,
        App $app
    )
    {
        $this->UserSurveyCategorySearchRepository = $UserSurveyCategorySearchRepository;

        parent::__construct($app);
       
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'ACME\UserSurvey\Contracts\UserSurveyCategory';
    }


    public function getAll($categoryId = null)
    {
        $params = request()->input();

        
        $perPage = isset($params['limit']) && ! empty($params['limit']) ? $params['limit'] : 10;
       

        $page = Paginator::resolveCurrentPage('page');

        $repository = app(UserSurveyCategorySearchRepository::class)->scopeQuery(function ($query) use ($params) {
            
            $qb = $query->distinct()
                ->select('user_survey_categories.*')
                ->orderby('id','desc');
                
           
              return $qb->groupBy('id');

        });

        # apply scope query so we can fetch the raw sql and perform a count
        $repository->applyScope();
        $countQuery = "select count(*) as aggregate from ({$repository->model->toSql()}) c";
        $count = collect(DB::select($countQuery, $repository->model->getBindings()))->pluck('aggregate')->first();


        if ($count > 0) {
            # apply a new scope query to limit results to one page
            $repository->scopeQuery(function ($query) use ($page, $perPage) {
                return $query->forPage($page, $perPage);
            });

            # manually build the paginator
            $items = $repository->get();
           

        } else {
            $items = [];
        }

        $results = new LengthAwarePaginator($items, $count, $perPage, $page, [
            'path'  => request()->url(),
            'query' => request()->query(),
        ]);

        return $results;
    }

    


    public function get($id = null)
    {
       
        if ($id) {
            $qb = $this->model
            ->distinct()
            ->addSelect('user_survey_categories.*');
            $qb->where('user_survey_categories.id', $id);
        } else {
            $qb = $this->model
            ->distinct()
            ->addSelect('user_survey_categories.*');
            $qb->inRandomOrder();
            $qb->limit(1);
            
           
        }
        
        $result = $qb->get();

        if (count($result)==0) {
            $result = [];
        }  
            
       

        return $result;
    }
   

    public function create($request)
    {
        
    
        $SurveyCategory = $this->UserSurveyCategorySearchRepository->create([
            'cate_name'        =>  $request->cate_name,
            'cate_desc'      => $request->cate_desc,
            'cate_order'  =>  $request->cate_order,
            'status'     => $request->status
        ]);

    
        return $SurveyCategory;
        
    }

    public function update($request,$id=null)
    {
       return  $SurveyCategory = $this->UserSurveyCategorySearchRepository->update([
            'cate_name'        =>  $request->cate_name,
            'cate_desc'      => $request->cate_desc,
            'cate_order'  =>  $request->cate_order,
            'status'     => $request->status
        ],$request->id);

       
        
    }

   /* public function delete($id)
    {
        $this->UserSurveyCategorySearchRepository->delete($id);
        
    }*/

}