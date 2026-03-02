<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_color' => $this->status->color(),
            'cover_letter' => $this->cover_letter,
            'resume_url' => $this->resume_path ? asset('storage/'.$this->resume_path) : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'candidate' => new CandidateResource($this->whenLoaded('candidate')),
            'job_posting' => new JobResource($this->whenLoaded('jobPosting')),
            'notes' => ApplicationNoteResource::collection($this->whenLoaded('notes')),
        ];
    }
}
