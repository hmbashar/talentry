<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'note' => $this->note,
            'created_at' => $this->created_at->toDateTimeString(),
            'user' => [
                'name' => $this->user?->name,
            ],
        ];
    }
}
