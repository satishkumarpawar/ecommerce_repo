<?php

namespace ACME\UserSurvey\Repositories;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use ACME\UserSurvey\Models\UserSurveySet;
use ACME\UserSurvey\Models\UserSurveySetDetail;
use ACME\UserSurvey\Models\UserSurveyQuestion;
use ACME\UserSurvey\Models\UserSurveyAnswer;
use ACME\UserSurvey\Repositories\UserSurveySetDetailRepository;
use ACME\UserSurvey\Repositories\UserSurveyQuestionRepository;
use ACME\UserSurvey\Repositories\UserSurveyAnswerRepository;
use Illuminate\Pagination\Paginator;
use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Container\Container as App;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ACME\UserSurvey\Repositories\UserSurveySetSearchRepository;




class UserSurveySetRepository extends Repository
{
 
    protected $UserSurveySetSearchRepository;
    protected $UserSurveySetDetailRepository;
    protected $UserSurveyQuestionRepository;
    protected $UserSurveyAnswerRepository;
   
    public function __construct(
        UserSurveySetSearchRepository $UserSurveySetSearchRepository,
        UserSurveySetDetailRepository $UserSurveySetDetailRepository,
        UserSurveyQuestionRepository $UserSurveyQuestionRepository,
        UserSurveyAnswerRepository $UserSurveyAnswerRepository, 
        App $app
    )
    {
        $this->UserSurveySetSearchRepository = $UserSurveySetSearchRepository;

        $this->UserSurveySetDetailRepository = $UserSurveySetDetailRepository;

        $this->UserSurveyQuestionRepository = $UserSurveyQuestionRepository;

        $this->UserSurveyAnswerRepository = $UserSurveyAnswerRepository;

        parent::__construct($app);
       
    }

     /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'ACME\UserSurvey\Contracts\UserSurveySet';
    }

    

    public function getAll($categoryId = null)
    {
        $params = request()->input();

        
        $perPage = isset($params['limit']) && ! empty($params['limit']) ? $params['limit'] : 10;
       

        $page = Paginator::resolveCurrentPage('page');

        $repository = app(UserSurveySetSearchRepository::class)->scopeQuery(function ($query) use ($params, $categoryId) {
            
            $qb = $query->distinct()
                ->select('user_survey_sets.*');
                
               

            /*if ($categoryId) {
                $qb->whereIn('user_survey_sets.category_id', explode(',', $categoryId));
            }*/

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
           
            
           foreach ($items as $k=>$v) {
                $details = $this->UserSurveySetDetailRepository->getSurveySetDetail($v["id"]);
                $items[$k]["question_set"]=$details;
                foreach($details as $k2=>$v2) {
                    $question_answers = $this->UserSurveyAnswerRepository->getSurveyAnswers($v2["question_id"]);
                    $items[$k]["question_set"][$k2]["answer_options"]=$question_answers;
                }
            }
            


        } else {
            $items = [];
        }

        $results = new LengthAwarePaginator($items, $count, $perPage, $page, [
            'path'  => request()->url(),
            'query' => request()->query(),
        ]);

        return $results;
    }

    public function getList($categoryId = null)
    {
        $params = request()->input();

        
        $perPage = isset($params['limit']) && ! empty($params['limit']) ? $params['limit'] : 10;
       

        $page = Paginator::resolveCurrentPage('page');

        $repository = app(UserSurveySetSearchRepository::class)->scopeQuery(function ($query) use ($params, $categoryId) {
            
            $qb = $query->distinct()
                ->select('user_survey_sets.id','user_survey_sets.survey_name');
                
               

            /*if ($categoryId) {
                $qb->whereIn('user_survey_sets.category_id', explode(',', $categoryId));
            }*/

              return $qb->groupBy('id');

        });

      
            $items = $repository->get();
           
         

        return $items;
    }

    
    /*
    public function getSurveySetDetail($id = null)
    {
        $qb = $this->model
            ->leftJoin('user_survey_questions', 'user_survey_set_details.question_id', '=', 'user_survey_questions.id');

        if ($id) {
            $qb->where('user_survey_set_details.survey_set_id', $id);
            $qb->where('user_survey_questions.status', 1);
        }

        return $qb->get();
    }
    public function getSurveySetDetail($id = null)
    {
        $qb = $this->UserSurveySetDetailRepository->model
            ->leftJoin('user_survey_questions', 'user_survey_set_details.question_id', '=', 'user_survey_questions.id');

        if ($id) {
            $qb->where('user_survey_set_details.survey_set_id', $id);
            $qb->where('user_survey_questions.status', 1);
        }

        return $qb->get();
    }
    */


    public function get($id = null)
    {
       /* $params = request()->input();

        $repository =  app(UserSurveySetSearchRepository::class)->scopeQuery(function ($query) use ($params, $id) {
            $qb = $query->distinct()
                    ->addSelect('user_survey_sets.*');
                if ($id) {
                    
                    $qb->where('user_survey_sets.id', $id);
                    // $qb->where("(user_survey_sets.start_date>=now() OR user_survey_sets.start_date=0) AND user_survey_sets.end_date=0) OR (user_survey_sets.start_date>=now() AND user_survey_set.end_date<=now())");
                }
            return $qb->groupBy('id');
        });
        
        $result = $repository->get();
        */
       
      //  $id=-1;
        if ($id) {
            $qb = $this->model
            ->distinct()
            ->addSelect('user_survey_sets.*');
            $qb->where('user_survey_sets.id', $id);
            // $qb->where("(user_survey_sets.start_date>=now() OR user_survey_sets.start_date=0) AND user_survey_sets.end_date=0) OR (user_survey_sets.start_date>=now() AND user_survey_set.end_date<=now())");
        } else {
            $qb = $this->model
            ->distinct()
            ->addSelect('user_survey_sets.*');
            $qb->inRandomOrder();
            $qb->limit(1);
            
           
        }
        
        $result = $qb->get();

        if (count($result)>0) {
            foreach ($result as $k=>$v) {
                $details = $this->UserSurveySetDetailRepository->getSurveySetDetail($v["id"]);
                $result[$k]["question_set"]=$details;
                foreach($details as $k2=>$v2) {
                    $question_answers = $this->UserSurveyAnswerRepository->getSurveyAnswers($v2["question_id"]);
                    $result[$k]["question_set"][$k2]["answer_options"]=$question_answers;
                
                }
            }
        } else {
            $result = [];
        }  
            
       

        return $result;
    }
   

    public function create($request)
    {
        
    
        $SurveySet = $this->UserSurveySetSearchRepository->create([
            'survey_name'        =>  $request->survey_name,
            'survey_desc'      => $request->survey_desc,
            'survey_level'  =>  $request->survey_level,
            'start_date'     => $request->start_date,
            'end_date'      => $request->end_date
        ]);

        if(count((array)$SurveySet)>0){
            if(is_array($request->question_set) && count($request->question_set)>0){
                $SurveySetDetail=Array();
                foreach($request->question_set as $k=>$detail){
                    $SurveySetDetail[] = $this->UserSurveySetDetailRepository->create([
                        'question_id'        =>  $detail["question_id"],
                        'survey_set_id'      => $SurveySet->id
                    ]);
                   
                }
               
                $SurveySet->question_set= $SurveySetDetail;
            }
           
        }
        
        
        return $SurveySet;
        
    }

    public function update($request,$id=null)
    {
        $SurveySet = $this->UserSurveySetSearchRepository->update([
            'survey_name'        =>  $request->survey_name,
            'survey_desc'      => $request->survey_desc,
            'survey_level'  =>  $request->survey_level,
            'start_date'     => $request->start_date,
            'end_date'      => $request->end_date
        ],$request->id);

        if(count((array)$SurveySet)>0){
            if(is_array($request->question_set) && count($request->question_set)>0){
                $SurveySetDetail=Array();
                foreach($request->question_set as $k=>$detail){
                    $SurveySetD= $this->UserSurveySetDetailRepository
                        ->where('survey_set_id',$request->id)
                        ->where('question_id',$detail["question_id"])
                        ->get()->first();
                    if(isset($SurveySetD["id"])){
                        $SurveySetDetail[] = $SurveySetD;
                    } else {
                        $SurveySetD = $this->UserSurveySetDetailRepository->create([
                            'question_id'        =>  $detail["question_id"],
                            'survey_set_id'      => $detail["survey_set_id"]
                        ]);
                        $SurveySetD= $this->UserSurveySetDetailRepository
                        ->where('survey_set_id',$request->id)
                        ->where('question_id',$detail["question_id"])
                        ->get()->first();
                        if(isset($SurveySetD["id"])){
                            $SurveySetDetail[] = $SurveySetD;
                        }
                    }
                   
                }
               
                $SurveySet->question_set= $SurveySetDetail;
            }
            
            if(count((array)$request->delete_questions)>0){
                foreach($request->delete_questions as $k=>$detail){
                    if(isset($detail["question_id"])){
                        $this->UserSurveySetDetailRepository
                        ->where('survey_set_id',$request->id)
                        ->where('question_id',$detail["question_id"])
                        ->delete();
                       
                    }
                }
            }
           
        }

        /*$SurveySet->question_set=$this->UserSurveySetDetailRepository
        ->where('survey_set_id',$request->id)
        ->get();
        */
        return $SurveySet;
        
    }

    public function addQuestion($request,$id=null)
    {
       
            if(is_array($request->question_set) && count($request->question_set)>0){
                $SurveySetDetail=Array();
                foreach($request->question_set as $k=>$detail){
                    $SurveySetD= $this->UserSurveySetDetailRepository
                        ->where('survey_set_id',$detail["survey_set_id"])
                        ->where('question_id',$detail["question_id"])
                        ->get()->first();
                    if(isset($SurveySetD["id"])){
                        $SurveySetDetail[] = $SurveySetD;
                    } else {
                        $SurveySetD = $this->UserSurveySetDetailRepository->create([
                            'question_id'        =>  $detail["question_id"],
                            'survey_set_id'      => $detail["survey_set_id"]
                        ]);
                        $SurveySetD= $this->UserSurveySetDetailRepository
                        ->where('survey_set_id',$request->id)
                        ->where('question_id',$detail["question_id"])
                        ->get()->first();
                        if(isset($SurveySetD["id"])){
                            $SurveySetDetail[] = $SurveySetD;
                        }
                    }
                }
            }
            
       
        return $SurveySetDetail;
        
    }

   /* public function delete($id)
    {
       // $this->UserSurveySetDetailRepository->delete()->where('survey_set_id',$id);

        $this->UserSurveySetSearchRepository->delete($id);
        
    }*/


}