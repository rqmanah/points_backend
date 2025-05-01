<?php

namespace App\Modules\Subscriptions\Resources;

use App\Modules\Subscriptions\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $expired = $this->package_ended_at < now();

        $package_id = $this->package_id;
        $payment = Payments::where('package_id', $package_id)->where('school_id', $request->user()->school_id)->first();

        return [
            'id'                        => $this->id,
            'method'                    => $this->free ? 'free' : 'online',
            'package'                   => $this->package ? $this->package->Data->first()->title : 'الباقة المجانية',
            'created_at'                => $this->created_at->format('Y-m-d H:i:s'),
            'package_plan_price'        => $this->package_plan_price,
            'package_price_after_tax'   => $this->package_plan_price * 15 / 100 + $this->package_plan_price,
            'coupon'                    => $this->coupon,
            'free'                      => $this->free,
            'tax'                       => 15,
            'package_id'                => $this->package_id,
            'package_started_at'        => $this->package_started_at,
            'package_ended_at'          => $this->package_ended_at,
            'expired'                   => $this->package_ended_at < now(),
            'permissions'               => !$expired ? $this?->permissionX() : null,
            'invoiceId'                 => $payment ? $payment?->invoiceId : null,
            'invoiceURL'                => $payment ? $payment?->invoiceURL : null,
        ];
    }
}
