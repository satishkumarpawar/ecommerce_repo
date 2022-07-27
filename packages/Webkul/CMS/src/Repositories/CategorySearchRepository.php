<?php

namespace Webkul\CMS\Repositories;

use Webkul\Core\Eloquent\Repository;

class CategorySearchRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\CMS\Contracts\CmsPagesCategory';
    }
}