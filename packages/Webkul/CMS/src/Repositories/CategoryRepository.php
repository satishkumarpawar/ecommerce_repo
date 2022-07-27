<?php

namespace Webkul\CMS\Repositories;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Webkul\CMS\Models\CmsPagesCategory;
use Webkul\CMS\Repositories\CategorySearchRepository;
use Illuminate\Pagination\Paginator;
use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Container\Container as App;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryRepository extends Repository
{

    protected $CategorySearchRepository;
   
    public function __construct(
        CategorySearchRepository $CategorySearchRepository,
        App $app
    )
    {
        $this->CategorySearchRepository = $CategorySearchRepository;

        parent::__construct($app);
       
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\CMS\Contracts\CmsPagesCategory';
    }


    public function getAll($categoryId = null)
    {
        $params = request()->input();

        
        $perPage = isset($params['limit']) && ! empty($params['limit']) ? $params['limit'] : 10;
       

        $page = Paginator::resolveCurrentPage('page');

        $repository = app(CategorySearchRepository::class)->scopeQuery(function ($query) use ($params) {
            
            $qb = $query->distinct()
                ->select('cms_pages_categories.*')
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
            ->addSelect('cms_pages_categories.*');
            $qb->where('cms_pages_categories.id', $id);
        } else {
            $qb = $this->model
            ->distinct()
            ->addSelect('cms_pages_categories.*');
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
        
    
        $PagesCategory = $this->CategorySearchRepository->create([
            'cate_name'        =>  $request->cate_name,
            'cate_desc'      => $request->cate_desc,
            'cate_order'  =>  $request->cate_order,
            'status'     => $request->status,
            'restrict'     => $request->restrict
        ]);

    
        return $PagesCategory;
        
    }

    public function update($request,$id=null)
    {
       return  $PagesCategory = $this->CategorySearchRepository->update([
            'cate_name'        =>  $request->cate_name,
            'cate_desc'      => $request->cate_desc,
            'cate_order'  =>  $request->cate_order,
            'status'     => $request->status,
            'restrict'     => $request->restrict
        ],$request->id);

       
        
    }

   /* public function delete($id)
    {
        $this->CategorySearchRepository->delete($id);
        
    }*/

}