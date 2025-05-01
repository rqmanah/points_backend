<?php

namespace App\Modules\TeacherTickets\Services;

use App\Bll\Paths;
use App\Services\Store;
use Illuminate\Support\Facades\DB;
use App\Modules\TeacherTickets\Models\Ticket;
use App\Modules\TeacherTickets\Models\TicketComments;
use App\Modules\TeacherTickets\Resources\TicketResource;
use App\Modules\TeacherTickets\Resources\TicketShowResource;

class TicketService extends Store
{
    protected $error;
    protected $success;
    protected $saved;
    protected $filters = ["status"];

    public function __construct()
    {
        $this->resource = TicketResource::class;
        $this->error = __('api.There is no tickets available');
        $this->success = __('api.All tickets retrieved successfully');
        $this->saved = __('api.All tickets retrieved successfully');

        parent::__construct(new Ticket());
    }

    public function GetAll()
    {
        return $this->Get(['tickets.id', 'user_id', 'status', "image", 'closed_date', 'subject', 'tickets.created_at'], false);
    }

    public function storeData()
    {
        try {
            DB::beginTransaction();
            $this->public_path = Paths::get_public_path('comments');
            $this->store(["subject"], [], null, "image");

            if ($this->GetCreated()) {
                TicketComments::create([
                    'ticket_id' => $this->GetCreated()->id,
                    'message' => request()->message
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
        return $this->saved;
    }

    public function showData(int $id)
    {
        if (isset($id) && is_int($id)) {
            $data = Ticket::where('id', $id)->first();
            if ($data != null) {
                $this->data = TicketShowResource::make($data);
            }
        }
        return $this->saved;
    }
}
