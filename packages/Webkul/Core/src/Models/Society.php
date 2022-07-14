<?php

namespace Webkul\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Core\Contracts\Society as SocietyContract;

class Society extends Model implements SocietyContract
{
    protected $table = 'societies';

    protected $fillable = [
        'name',
        'sector',
        'city',
        'district',
        'state',
        'postcode',
        'description',
        'status',
    ];
}