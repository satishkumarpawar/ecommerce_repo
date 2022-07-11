<?php

namespace ACME\UserSurvey\Repositories;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use ACME\UserSurvey\Models\UserSurveyQuestion;
use ACME\UserSurvey\Models\UserSurveyAnswer;
use ACME\UserSurvey\Repositories\UserSurveyQuestionSearchRepository;
use ACME\UserSurvey\Repositories\UserSurveyAnswerRepository;
use Illuminate\Pagination\Paginator;
use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Container\Container as App;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserSurveyQuestionRepository extends Repository
{

    protected $UserSurveyQuestionSearchRepository;
    protected $UserSurveyAnswerRepository;
   
    public function __construct(
        UserSurveyQuestionSearchRepository $UserSurveyQuestionSearchRepository,
        UserSurveyAnswerRepository $UserSurveyAnswerRepository, 
        App $app
    )
    {
        $this->UserSurveyQuestionSearchRepository = $UserSurveyQuestionSearchRepository;

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
        return 'ACME\UserSurvey\Contracts\UserSurveyQuestion';
    }

    public function getAll($categoryId = null)
    {
        $params = request()->input();

        
        $perPage = isset($params['limit']) && ! empty($params['limit']) ? $params['limit'] : 10;
       

        $page = Paginator::resolveCurrentPage('page');

        $repository = app(UserSurveyQuestionSearchRepository::class)->scopeQuery(function ($query) use ($params, $categoryId) {
            
            $qb = $query->distinct()
                ->select('user_survey_questions.*')
                ->addSelect('user_survey_categories.cate_name')
                ->leftJoin('user_survey_categories', 'user_survey_questions.cate_id', '=', 'user_survey_categories.id')
                ->orderby('id','desc');
               

            if ($categoryId) {
                $qb->whereIn('user_survey_questions.cate_id', explode(',', $categoryId));
            }

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
                $details = $this->UserSurveyAnswerRepository->getSurveyAnswers($v["id"]);
                $items[$k]["answer_options"]=$details;
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

    


    public function get($id = null)
    {
      
        if ($id) {
            $qb = $this->model
            ->distinct()
            ->addSelect('user_survey_questions.*')
            ->addSelect('user_survey_categories.cate_name')
                ->leftJoin('user_survey_categories', 'user_survey_questions.cate_id', '=', 'user_survey_categories.id')
                ;
            $qb->where('user_survey_questions.id', $id);
        } else {
            $qb = $this->model
            ->distinct()
            ->addSelect('user_survey_questions.*');
            $qb->inRandomOrder();
            $qb->limit(1);
            
           
        }
        
        $result = $qb->get();

        if (count($result)>0) {
            foreach ($result as $k=>$v) {
                $details = $this->UserSurveyAnswerRepository->getSurveyAnswers($v["id"]);
                $result[$k]["answer_options"]=$details;
            }
        } else {
            $result = [];
        }  
            
       

        return $result;
    }
   


    public function create($request)
    {
        
    
        $SurveyQuestion = $this->UserSurveyQuestionSearchRepository->create([
            'cate_id'        =>  $request->cate_id,
            'question_text'      => $request->question_text,
            'question_order'  =>  $request->question_order,
            'question_lock'     => $request->question_lock,
            'status'      => $request->status
        ]);

        if(count((array)$SurveyQuestion)>0){
            if(is_array($request->answer_options) && count($request->answer_options)>0){
                $SurveyQuestionDetail=Array();
                foreach($request->answer_options as $k=>$detail){
                    $SurveyQuestionDetail[] = $this->UserSurveyAnswerRepository->create([
                        'question_id'        =>  $SurveyQuestion->id,
                        'answer_text'      => $detail["answer_text"],
                        'answer_order'      => $detail["answer_order"],
                        'default_ans_flag'      => $detail["default_ans_flag"]
                    ]);
                   
                }
               
                $SurveyQuestion->answer_options= $SurveyQuestionDetail;
            }
           
        }
        
        
        return $SurveyQuestion;
        
    }

    public function update($request,$id=null)
    {
        $SurveyQuestion = $this->UserSurveyQuestionSearchRepository->update([
            'cate_id'        =>  $request->cate_id,
            'question_text'      => $request->question_text,
            'question_order'  =>  $request->question_order,
            'question_lock'     => $request->question_lock,
            'status'      => $request->status
        ],$request->id);

        if(count((array)$SurveyQuestion)>0){
            if(is_array($request->answer_options) && count($request->answer_options)>0){
                $SurveyQuestionDetail=Array();
                foreach($request->answer_options as $k=>$detail){
                    if(isset($detail["id"])){
                        $SurveyQuestionDetail[] = $this->UserSurveyAnswerRepository->update([
                            'question_id'        =>  $detail["question_id"],
                            'answer_text'      => $detail["answer_text"],
                            'answer_order'      => $detail["answer_order"],
                            'default_ans_flag'      => $detail["default_ans_flag"]
                        ],$detail["id"]);
                    } else {
                        $SurveyQuestionDetail[] = $this->UserSurveyAnswerRepository->create([
                            'question_id'        =>  $detail["question_id"],
                            'answer_text'      => $detail["answer_text"],
                            'answer_order'      => $detail["answer_order"],
                            'default_ans_flag'      => $detail["default_ans_flag"]
                        ]);

                    }
                   
                }
               
                $SurveyQuestion->answer_options= $SurveyQuestionDetail;
            }
           
        }
        
        
        return $SurveyQuestion;
        
    }
/*
   public function delete($id)
    {
        $this->UserSurveyAnswerRepository->delete()->where('question_id',$id);

        $this->UserSurveyQuestionSearchRepository->delete($id);
        
    }*/
}