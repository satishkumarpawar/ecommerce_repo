<?php

namespace Webkul\CMS\Repositories;

use Webkul\Core\Eloquent\Repository;

class CmsSearchRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\CMS\Contracts\CmsPage';
    }
}