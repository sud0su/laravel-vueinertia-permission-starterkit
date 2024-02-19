<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CronResource extends JsonResource
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
            'rsklinik' => $this->rsklinik ? $this->rsklinik : null,
            'clientname' => $this->rsklinik ? $this->rsklinik->clientname : null,
            'crontitle' => $this->crontitle,
            'croncat' => $this->croncat,
            'endpoint' => $this->endpoint,
            'day' => $this->day,
            'hour' => $this->hour,
            'minute' => $this->minute,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
