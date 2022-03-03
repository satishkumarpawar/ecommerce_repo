<?php

namespace ACME\UserSurvey\Repositories;

use Illuminate\Support\Facades\DB;
use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model; 

class CategoryRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'ACME\UserSurvey\Contracts\Category';
    }

 /**
     * Create category.
     *
     * @param  array  $data
     * @return \Webkul\Category\Contracts\Category
     */
    public function create(array $data)
    {
        Event::dispatch('catalog.category.create.before');

        $category = $this->model->create($data);

      
        Event::dispatch('catalog.category.create.after', $category);

        return $category;
    }

    
    /**
     * Specify category tree.
     *
     * @param  int  $id
     * @return \Webkul\Category\Contracts\Category
     */
    public function getCategoryTree($id = null)
    {
        return $id
            ? $this->model::orderBy('cate_order', 'ASC')->where('id', '!=', $id)->get()->toTree()
            : $this->model::orderBy('cate_order', 'ASC')->get()->toTree();
    }

    /**
     * Specify category tree.
     *
     * @param  int  $id
     * @return \Illuminate\Support\Collection
     */
    public function getCategoryTreeWithoutDescendant($id = null)
    {
        return $id
               ? $this->model::orderBy('cate_order', 'ASC')->where('id', '!=', $id)->whereNotDescendantOf($id)->get()->toTree()
               : $this->model::orderBy('cate_order', 'ASC')->get()->toTree();
    }

    /**
     * Get root categories.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRootCategories()
    {
        return $this->getModel()->get();
    }

    /**
     * get visible category tree.
     *
     * @param  int  $id
     * @return \Illuminate\Support\Collection
     */
    public function getVisibleCategoryTree($id = null)
    {
        static $categories = [];

        if (array_key_exists($id, $categories)) {
            return $categories[$id];
        }

        return $categories[$id] = $id
            ? $this->model::orderBy('cate_order', 'ASC')->where('status', 1)->descendantsAndSelf($id)->toTree($id)
            : $this->model::orderBy('cate_order', 'ASC')->where('status', 1)->get()->toTree();
    }

   
    /**
     * Update category.
     *
     * @param  array  $data
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\Category\Contracts\Category
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $category = $this->find($id);

       // Event::dispatch('catalog.category.update.before', $id);

   
        $category->update($data);

       // Event::dispatch('catalog.category.update.after', $id);

        return $category;
    }

    /**
     * Delete category.
     *
     * @param  int  $id
     * @return void
     */
    public function delete($id)
    {
      //  Event::dispatch('catalog.category.delete.before', $id);

        parent::delete($id);

       // Event::dispatch('catalog.category.delete.after', $id);
    }

}
