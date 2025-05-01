<?php

namespace App\Modules\Tickets\Services;

use App\Bll\Paths;
use App\Services\Store;
use Illuminate\Support\Facades\File;
use App\Modules\Tickets\Models\TicketComments;
use App\Modules\Tickets\Resources\TicketCommentResource;
use Illuminate\Support\Facades\DB;

class TicketCommentService extends Store
{
    protected $saved;

    public function __construct()
    {
        $this->resource = TicketCommentResource::class;
        $this->saved = __('api.Ticket comment created successfully');
        parent::__construct(new TicketComments());
    }

    public function storeData()
    {
        try {
            DB::beginTransaction();
            $this->store(["message", "ticket_id"], [], "", "");
            if ($this->GetCreated()) {
                $image = request()->image;
                if ($image) {
                    $image = $this->moveImage($image, $this->GetCreated()->id);
                }
                $ticketComments = TicketComments::find($this->GetCreated()->id);
                $ticketComments->update([
                    'image' => env('APP_URL') . '/' . $image,
                ]);
            }
            DB::commit();
            return $this->saved;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
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
