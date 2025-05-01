<?php

namespace App\Modules\Tickets\Models;

use App\Bll\Utility;
use App\Models\Users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{
    use SoftDeletes;

    protected $table = 'tickets';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        $user_id = Auth::guard('sanctum')?->user()?->id;
        $school_id =  Utility::school_id();
        static::creating(function (Ticket $ticket) use ($user_id, $school_id) {
            $ticket->user_id = $user_id;
            $ticket->school_id = $school_id;
        });
        static::addGlobalScope('forUser', function ($query) use ($user_id, $school_id) {
            $query->where('user_id', $user_id)->where('school_id', $school_id);
        });
    }

    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(TicketComments::class, 'ticket_id');
    }

}
