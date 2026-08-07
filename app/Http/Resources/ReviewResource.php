<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ReviewResource extends JsonResource
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
            'type' => $this->type,
            'description' => $this->description,
            'rating' => $this->rating,
            'reviewable_id'=>$this->reviewable_id,
            'status'=>$this->status,
            'image'=>$this->image ? Storage::url($this->image):null,
            'client_id'=>$this->client_id,
            'created_at' => $this->created_at,
            

        ];
    }
}
