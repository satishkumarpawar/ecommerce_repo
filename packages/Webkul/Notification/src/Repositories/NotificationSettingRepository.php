<?php

namespace Webkul\Notification\Repositories;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


use Webkul\Core\Eloquent\Repository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Container\Container as App;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Webkul\Notification\Repositories\NotificationSettingSearchRepository;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Webkul\Notification\Models\NotificationSetting;

class NotificationSettingRepository extends Repository
{
    protected $NotificationSettingSearchRepository;
   
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Notification\Contracts\NotificationSetting';
    }

    public function __construct(
        NotificationSettingSearchRepository $NotificationSettingSearchRepository,
        App $app
    )
    {
        $this->NotificationSettingSearchRepository = $NotificationSettingSearchRepository;

        parent::__construct($app);
       
    }


    public function getAll()
    {
        $params = request()->input();

        
        $perPage = isset($params['limit']) && ! empty($params['limit']) ? $params['limit'] : 10;
       

        $page = Paginator::resolveCurrentPage('page');

        $repository = app(NotificationSettingSearchRepository::class)->scopeQuery(function ($query) use ($params) {
            
            $qb = $query->distinct()
                ->select('notification_settings.*')
                ->orderby('notification_titile','asc');
                
               

            /*if (isset($params['notification_titile'])) {
                $qb->whereIn('notification_titile','like',$params['notification_titile']."%");
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


    
    public function get($id = null)
    {
        if ($id) {
            $qb = $this->model
            ->distinct()
            ->addSelect('notification_settings.*');
            
            $qb->where('id', $id);
        } else {
            $qb = $this->model
            ->distinct()
            ->addSelect('notification_settings.*');
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
        $Notification_setting = $this->NotificationSettingSearchRepository->create([
            'notification_titile'      => $request->notification_titile,
            'notification_message'      => $request->notification_message,
            'customer_group_id'      => $request->customer_group_id,
            'notification_type'      => $request->notification_type,
            'notification_times'      => $request->notification_times,
            'notification_interval'      => $request->notification_interval,
            'status'      => $request->status
        ]);

      
        
        return $Notification_setting;
        
    }

    public function update($request,$id=null)
    {

       // return $request;
        $Notification_setting = $this->NotificationSettingSearchRepository->update([
            'notification_titile'      => $request->notification_titile,
            'notification_message'      => $request->notification_message,
            'customer_group_id'      => $request->customer_group_id,
            'notification_type'      => $request->notification_type,
            'notification_times'      => $request->notification_times,
            'notification_interval'      => $request->notification_interval,
            'status'      => $request->status
        ],$request->id);

      
        
        return $Notification_setting;
        
    }

   /* public function delete($id)
    {
       // 
        $this->NotificationSettingSearchRepository->delete($id);
        
    }*/

  
}