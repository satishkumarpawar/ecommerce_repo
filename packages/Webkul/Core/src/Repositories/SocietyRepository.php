<?php

namespace Webkul\Core\Repositories;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use Webkul\Core\Models\Society;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Container\Container as App;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Webkul\Core\Repositories\SocietySearchRepository;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SocietyRepository extends Repository
{

    protected $SocietySearchRepository;
   
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Core\Contracts\Society';
    }

    public function __construct(
        SocietySearchRepository $SocietySearchRepository,
        App $app
    )
    {
        $this->SocietySearchRepository = $SocietySearchRepository;

        parent::__construct($app);
       
    }


    public function getAll()
    {
        $params = request()->input();

        
        $perPage = isset($params['limit']) && ! empty($params['limit']) ? $params['limit'] : 10;
       

        $page = Paginator::resolveCurrentPage('page');

        $repository = app(SocietySearchRepository::class)->scopeQuery(function ($query) use ($params) {
            
            $qb = $query->distinct()
                ->select('societies.*')
                ->orderby('name','asc');
                
               

            /*if (isset($params['name'])) {
                $qb->whereIn('name','like',$params['name']."%");
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
            


        } else {
            $items = [];
        }

        $results = new LengthAwarePaginator($items, $count, $perPage, $page, [
            'path'  => request()->url(),
            'query' => request()->query(),
        ]);

        return $results;
    }


    public function search()
    {
        $params = request()->input();

        
        $params['limit'] = isset($params['limit']) && ! empty($params['limit']) ? $params['limit'] : 20;
       
        $results =[];

        if (isset($params['keyword'])) {
            $repository = app(SocietySearchRepository::class)->scopeQuery(function ($query) use ($params) {
                
                $qb = $query->distinct()
                    ->select('societies.name')
                    ->addSelect('societies.id')
                    ->addSelect('societies.sector')
                    ->addSelect('societies.city')
                    ->addSelect('societies.district')
                    ->addSelect('societies.state')
                    ->addSelect('societies.postcode')
                    ->orderby('name','asc');
                    
                

                if(isset($params['field']) && $params['field']=='all') {
                    
                    $qb->where('sector','like',"%".$params['keyword']."%");
                    $qb->orWhere('city','like',"%".$params['keyword']."%");
                    $qb->orWhere('district','like',"%".$params['keyword']."%");
                    $qb->orWhere('state','like',"%".$params['keyword']."%");
                    $qb->orWhere('postcode',$params['keyword']);
                    $qb->orWhere('name','like',"%".$params['keyword']."%");
         

                } elseif(isset($params['field'])){

                    if($params['field']=='sector')$qb->where('sector','like',"%".$params['keyword']."%");
                    elseif($params['field']=='city')$qb->where('city','like',"%".$params['keyword']."%");
                    elseif($params['field']=='district')$qb->where('district','like',"%".$params['keyword']."%");
                    elseif($params['field']=='state')$qb->where('state','like',"%".$params['keyword']."%");
                    elseif($params['field']=='postcode')$qb->where('postcode',$params['keyword']);
                    else $qb->where('name','like',"%".$params['keyword']."%");
               } else {
                    $qb->where('name','like',"%".$params['keyword']."%");
                }

                $qb->limit($params['limit']);

                return $qb->groupBy('id');

            });
            $results = $repository->get();
        }

       
            
            


        return $results;
    }


    public function get($id = null)
    {
        if ($id) {
            $qb = $this->model
            ->distinct()
            ->addSelect('societies.*');
            
            $qb->where('id', $id);
        } else {
            $qb = $this->model
            ->distinct()
            ->addSelect('societies.*');
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
        $Society = $this->SocietySearchRepository->create([
            'name'      => $request->name,
            'sector'      => $request->sector,
            'city'      => $request->city,
            'district'      => $request->district,
            'state'      => $request->state,
            'description'      => $request->description,
            'postcode'      => $request->postcode,
            'status'      => $request->status
        ]);

      
        
        return $Society;
        
    }

    public function update($request,$id=null)
    {

       // return $request;
        $Society = $this->SocietySearchRepository->update([
            'name'      => $request->name,
            'sector'      => $request->sector,
            'city'      => $request->city,
            'district'      => $request->district,
            'state'      => $request->state,
            'description'      => $request->description,
            'postcode'      => $request->postcode,
            'status'      => $request->status
        ],$request->id);

      
        
        return $Society;
        
    }

   /* public function delete($id)
    {
       // 
        $this->SocietySearchRepository->delete($id);
        
    }*/

}