<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HospitalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'clientname' => $this->clientname,
            'address' => $this->address,
            'crontask' => $this->crontasks,
            'orgid_prod' => $this->orgid_prod,
            'clientid_prod' => $this->clientid_prod,
            'clientsecret_prod' => $this->clientsecret_prod,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
