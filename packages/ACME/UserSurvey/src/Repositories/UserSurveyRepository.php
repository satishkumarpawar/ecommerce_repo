<?php
namespace ACME\UserSurvey\Repositories;


use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use ACME\UserSurvey\Models\UserSurveySet;
use ACME\UserSurvey\Models\UserSurveySetDetail;
use ACME\UserSurvey\Models\UserSurveyQuestion;
use ACME\UserSurvey\Models\UserSurveyAnswer;
use ACME\UserSurvey\Models\UserSurvey;
use ACME\UserSurvey\Models\UserSurveyDetail;
use ACME\UserSurvey\Repositories\UserSurveyQuestionRepository;
use ACME\UserSurvey\Repositories\UserSurveyAnswerRepository;
use Illuminate\Pagination\Paginator;
use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Container\Container as App;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use ACME\UserSurvey\Repositories\UserSurveySearchRepository;
use ACME\UserSurvey\Repositories\UserSurveyDetailRepository;
 
class UserSurveyRepository extends Repository
{

    protected $UserSurveySearchRepository;
    protected $UserSurveyDetailRepository;
    protected $UserSurveyQuestionRepository;
    protected $UserSurveyAnswerRepository;
   
    public function __construct(
        UserSurveySearchRepository $UserSurveySearchRepository,
        UserSurveyDetailRepository $UserSurveyDetailRepository,
        UserSurveyQuestionRepository $UserSurveyQuestionRepository,
        UserSurveyAnswerRepository $UserSurveyAnswerRepository, 
        App $app
    )
    {
        $this->UserSurveySearchRepository = $UserSurveySearchRepository;

        $this->UserSurveyDetailRepository = $UserSurveyDetailRepository;

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
        return 'ACME\UserSurvey\Contracts\UserSurvey';
    }


    
    public function getAll($categoryId = null)
    {
        $params = request()->input();

        
        $perPage = isset($params['limit']) && ! empty($params['limit']) ? $params['limit'] : 10;
       

        $page = Paginator::resolveCurrentPage('page');

        $repository = app(UserSurveySearchRepository::class)->scopeQuery(function ($query) use ($params, $categoryId) {
            
            $qb = $query->distinct()
                ->select('user_surveys.*')
                ->addSelect('customers.first_name')
                ->addSelect('customers.last_name')
                ->addSelect('user_survey_sets.survey_name')
                ->leftJoin('customers', 'user_surveys.user_id', '=', 'customers.id')
                ->leftJoin('user_survey_sets', 'user_surveys.survey_set_id', '=', 'user_survey_sets.id');
                
               

            /*if ($categoryId) {
                $qb->whereIn('user_surveys.category_id', explode(',', $categoryId));
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
                $details = $this->UserSurveyDetailRepository->getSurveyDetail($v["id"]);
                $items[$k]["answer_set"]=$details;
                
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
            ->addSelect('user_surveys.*');
            $qb->where('user_surveys.id', $id);
        } else {
            $qb = $this->model
            ->distinct()
            ->addSelect('user_surveys.*');
            $qb->inRandomOrder();
            $qb->limit(1);
            
           
        }
        
        $result = $qb->get();

        if (count($result)>0) {
            foreach ($result as $k=>$v) {
                $details = $this->UserSurveyDetailRepository->getSurveyDetail($v["id"]);
                $result[$k]["answer_set"]=$details;
               
            }
        } else {
            $result = [];
        }  
            
       

        return $result;
    }
   

    public function create($request1)
    {
        $request =$request1["request"];
        $user_id=$request1["user_id"];
    
        $Survey = $this->UserSurveySearchRepository->create([
            'user_id'        =>   $user_id,
            'survey_set_id'      => $request->survey_set_id
        ]);

        if(count((array)$Survey)>0){
            if(is_array($request->answer_set) && count($request->answer_set)>0){
                $SurveyDetail=Array();
                foreach($request->answer_set as $k=>$detail){
                    $SurveyDetail[] = $this->UserSurveyDetailRepository->create([
                        'survey_id'        =>  $Survey->id,
                        'question_id'      => $detail["question_id"],
                        'answer_id'      => $detail["answer_id"],
                        'answer_text'      => $detail["answer_text"] ? $detail["answer_text"] : null
                    ]);
                   
                }
               
                $Survey->answer_set= $SurveyDetail;
            }
           
        }
        
        
        return $Survey;
        
    }

    public function update($request,$id=null)
    {
        $Survey = $this->UserSurveySearchRepository->update([
            'survey_set_id'      => $request->survey_set_id
        ],$request->id);

        if(count((array)$Survey)>0){
            if(is_array($request->answer_set) && count($request->answer_set)>0){
                $SurveyDetail=Array();
                foreach($request->answer_set as $k=>$detail){
                    if(isset($detail["id"])){
                        $SurveyDetail[] = $this->UserSurveyDetailRepository->update([
                            'survey_id'        =>  $Survey->id,
                            'question_id'      => $detail["question_id"],
                            'answer_id'      => $detail["answer_id"],
                            'answer_text'      => $detail["answer_text"] ? $detail["answer_text"] : null
                        ],$detail["id"]);
                    } else {
                        $SurveyDetail[] = $this->UserSurveyDetailRepository->create([
                            'survey_id'        =>  $Survey->survey_id,
                            'question_id'      => $detail["question_id"],
                            'answer_id'      => $detail["answer_id"],
                            'answer_text'      => $detail["answer_text"] ? $detail["answer_text"] : null
                        ]);

                    }
                   
                }
               
                $Survey->answer_set= $SurveyDetail;
            }
           
        }
        
        
        return $Survey;
        
    }

   /* public function delete($id)
    {
       // $this->UserSurveyDetailRepository->delete()->where('survey_id',$id);

        $this->UserSurveySearchRepository->delete($id);
        
    }*/

}