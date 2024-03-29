<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HospitalSharedResource extends JsonResource
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
            'clientid_prod' => $this->clientid_prod,
            'crontasks' => CronResource::collection($this->whenLoaded('crontasks'))
        ];
    }
}
