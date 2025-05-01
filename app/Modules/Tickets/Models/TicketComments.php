<?php

namespace App\Modules\Tickets\Models;

use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class TicketComments extends Model
{
    protected $table = 'ticket_comments';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (TicketComments $ticketComments) {
            $ticketComments->manager_id =  Auth::guard('sanctum')?->user()?->id;
        });
    }
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(Users::class, 'admin_id', 'id');
    }
    public function manager()
    {
        return $this->belongsTo(Users::class, 'manager_id', 'id');
    }




}
