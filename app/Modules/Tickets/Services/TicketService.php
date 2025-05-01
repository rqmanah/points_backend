<?php

namespace App\Modules\Tickets\Services;

use App\Bll\Paths;
use App\Services\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Modules\Tickets\Models\Ticket;
use App\Modules\Tickets\Models\TicketComments;
use App\Modules\Tickets\Resources\TicketResource;
use App\Modules\Tickets\Resources\TicketShowResource;

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
        $this->saved = __('api.Ticket created successfully');

        parent::__construct(new Ticket());
    }

    public function GetAll()
    {
        return $this->Get(['tickets.id', 'user_id', 'status', 'subject', 'closed_date', 'tickets.created_at'], false);
    }

    public function storeData()
    {
        try {
            DB::beginTransaction();
            $this->store(["subject"], [], null, "");

            if ($this->GetCreated()) {

                $image = request()->image;
                if ($image) {
                    $image = $this->moveImage($image, $this->GetCreated()->id);
                }
                $Ticket = Ticket::find($this->GetCreated()->id);
                $Ticket->update([
                    'image' => env('APP_URL') . '/' . $image,
                ]);

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

    public function close($id)
    {
        $ticket = Ticket::find($id);
        $ticket->status = 'closed';
        $ticket->closed_date = now();
        $ticket->save();
        return $this->saved;
    }

    public function moveImage($image_name, $objectId)
    {
        $imagePath = public_path('temp/' . $image_name);

        $uploadsDir = public_path('uploads/tickets-comments/' . $objectId);

        if (!File::exists($uploadsDir)) {
            File::makeDirectory($uploadsDir, 0755, true);
        }

        // Move the image to the new directory
        $newImagePath = $uploadsDir . '/' . $image_name;
        File::move($imagePath, $newImagePath);

        $storedImagePath = 'uploads/tickets-comments/' . $objectId . '/' . $image_name;
        return $storedImagePath;
    }
}
