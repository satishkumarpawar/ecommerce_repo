<?php

namespace Webkul\CMS\Repositories;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Container\Container as App;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Webkul\CMS\Models\CmsPageTranslationProxy;

use Webkul\CMS\Repositories\CmsSearchRepository;


class CmsRepository extends Repository
{
    protected $CmsSearchRepository;
   
    public function __construct(
        CmsSearchRepository $CmsSearchRepository,
        App $app
    )
    {
        $this->CmsSearchRepository = $CmsSearchRepository;

        parent::__construct($app);
       
    }
    
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\CMS\Contracts\CmsPage';
    }

#SKP Start
    public function getAll()
    {
        $params = request()->input();

        
        $perPage = isset($params['limit']) && ! empty($params['limit']) ? $params['limit'] : 10;
       

        $page = Paginator::resolveCurrentPage('page');

        $repository = app(CmsSearchRepository::class)->scopeQuery(function ($query) use ($params) {
            
            $qb = $query->distinct()
            ->select('cms_pages.id', 'cate_name','cms_page_translations.page_title', 'cms_page_translations.url_key', 'cms_page_translations.html_content', 'cms_page_translations.meta_title', 'cms_page_translations.meta_description', 'cms_page_translations.meta_keywords', 'cms_page_translations.locale', 'cms_page_translations.cms_page_id', 'cms_page_translations.category_id')
            ->leftJoin('cms_page_translations', function($leftJoin) {
                $leftJoin->on('cms_pages.id', '=', 'cms_page_translations.cms_page_id')
                         ->where('cms_page_translations.locale', app()->getLocale());
            })
            ->leftJoin('cms_pages_categories', function($leftJoin) { 
                $leftJoin->on('cms_page_translations.category_id', '=', 'cms_pages_categories.id');
            });

            if (isset($params['categoryId'])) {
                $qb->where('cms_page_translations.category_id', $params['categoryId']);
            }
                $qb->groupBy('id');
                $qb->orderby('id','desc');
           
              return $qb;

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
            ->select('cms_pages.id', 'cate_name','cms_page_translations.page_title', 'cms_page_translations.url_key', 'cms_page_translations.html_content', 'cms_page_translations.meta_title', 'cms_page_translations.meta_description', 'cms_page_translations.meta_keywords', 'cms_page_translations.locale', 'cms_page_translations.cms_page_id', 'cms_page_translations.category_id')
            ->leftJoin('cms_page_translations', function($leftJoin) {
                $leftJoin->on('cms_pages.id', '=', 'cms_page_translations.cms_page_id')
                         ->where('cms_page_translations.locale', app()->getLocale());
            })
            ->leftJoin('cms_pages_categories', function($leftJoin) { 
                $leftJoin->on('cms_page_translations.category_id', '=', 'cms_pages_categories.id');
            });
            $qb->where('cms_pages.id', $id);

        } else {
            if(!is_null(request()->category_id)) $category_id=request()->category_id;
            else $category_id=1;

            $qb = $this->model
            ->distinct()
            ->select('cms_pages.id', 'cate_name','cms_page_translations.page_title', 'cms_page_translations.url_key', 'cms_page_translations.html_content', 'cms_page_translations.meta_title', 'cms_page_translations.meta_description', 'cms_page_translations.meta_keywords', 'cms_page_translations.locale', 'cms_page_translations.cms_page_id', 'cms_page_translations.category_id')
            ->leftJoin('cms_page_translations', function($leftJoin) {
                $leftJoin->on('cms_pages.id', '=', 'cms_page_translations.cms_page_id')
                         ->where('cms_page_translations.locale', app()->getLocale());
            })
            ->leftJoin('cms_pages_categories', function($leftJoin) { 
                $leftJoin->on('cms_page_translations.category_id', '=', 'cms_pages_categories.id');
            });
            $qb->where('cms_page_translations.category_id',$category_id);
            $qb->inRandomOrder();
            $qb->limit(1);
            
           
        }
        
        $result = $qb->get();

        if (count($result)==0) {
            $result = [];
        }  
            
       

        return $result;
    }
   

    /**
     * @param  array  $data
     * @return \Webkul\CMS\Contracts\CmsPage
     */
    public function create(array $data)
    {
        Event::dispatch('cms.pages.create.before');

        $model = $this->getModel();

        foreach (core()->getAllLocales() as $locale) {
            foreach ($model->translatedAttributes as $attribute) {
                if (isset($data[$attribute])) {
                    $data[$locale->code][$attribute] = $data[$attribute];
                }
            }
        }

        $page = parent::create($data);

        $page->channels()->sync($data['channels']);

        Event::dispatch('cms.pages.create.after', $page);

        return $page;
    }

    /**
     * @param  array  $data
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\CMS\Contracts\CmsPage
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $page = $this->find($id);

        Event::dispatch('cms.pages.update.before', $id);

        parent::update($data, $id, $attribute);

        $page->channels()->sync($data['channels']);

        Event::dispatch('cms.pages.update.after', $id);

        return $page;
    }

    /**
     * Checks slug is unique or not based on locale
     *
     * @param  int  $id
     * @param  string  $urlKey
     * @return bool
     */
    public function isUrlKeyUnique($id, $urlKey)
    {
        $exists = CmsPageTranslationProxy::modelClass()::where('cms_page_id', '<>', $id)
            ->where('url_key', $urlKey)
            ->limit(1)
            ->select(\DB::raw(1))
            ->exists();

        return $exists ? false : true;
    }

    /**
     * Retrive category from slug
     *
     * @param  string  $urlKey
     * @return \Webkul\CMS\Contracts\CmsPage|\Exception
     */
    public function findByUrlKeyOrFail($urlKey)
    {
        $page = $this->model->whereTranslation('url_key', $urlKey)->first();

        if ($page) {
            return $page;
        }

        throw (new ModelNotFoundException)->setModel(
            get_class($this->model), $urlKey
        );
    }
}