<?php

namespace ACME\UserSurvey\Datagrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class CategoryDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc';

    public function prepareQueryBuilder()
    {
        //queryBuilder = DB::table('table')->addSelect('id');

        //$this->setQueryBuilder($queryBuilder);
        $queryBuilder = DB::table('survey_category as cat')
        ->select(
            'cat.id as id',
            'cat.cate_name',
            'cat.cate_desc',
            'cat.status',
            'cat.cate_order',
            DB::raw('COUNT(DISTINCT ' . DB::getTablePrefix() . 'q.id) as count')
        )
       
        ->leftJoin('survey_questions as q', 'cat.id', '=', 'q.id')
        ->groupBy('cat.id',);


        $this->addFilter('status', 'cat.status');
        $this->addFilter('category_id', 'cat.id');

        $this->setQueryBuilder($queryBuilder);


    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => 'Id',
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'cate_name',
            'label'      => trans('admin::app.datagrid.name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'cate_order',
            'label'      => 'Sort Order',
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => 'Status',
            'type'       => 'boolean',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure'    => function ($value) {
                if ($value->status == 1) {
                    return trans('admin::app.datagrid.active');
                } else {
                    return trans('admin::app.datagrid.inactive');
                }
            },
        ]);

        $this->addColumn([
            'index'      => 'count',
            'label'      => 'No of Q.',
            'type'       => 'number',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => false,
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'title'  => trans('admin::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'usersurvey.admin.categories.edit',
            'icon'   => 'icon pencil-lg-icon',
        ]);

        $this->addAction([
            'title'        => trans('admin::app.datagrid.delete'),
            'method'       => 'POST',
            'route'        => 'usersurvey.admin.categories.delete',
            'icon'         => 'icon trash-icon',
            'function'     => 'deleteCategory(event, "delete")'
        ]);

       
    }
}