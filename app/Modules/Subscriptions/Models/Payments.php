<?php

namespace App\Modules\Subscriptions\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{

    protected $table = 'payments';

    protected $guarded = [];

    protected $connection = 'mysql';

}
