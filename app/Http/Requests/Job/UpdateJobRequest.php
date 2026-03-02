<?php

namespace App\Http\Requests\Job;

use App\Enums\EmploymentType;
use App\Models\JobPosting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        $job = JobPosting::where('uuid', $this->route('uuid'))->first();

        return $job && $this->user()->can('update', $job);
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'location' => ['sometimes', 'required', 'string', 'max:255'],
            'employment_type' => ['sometimes', 'required', new Enum(EmploymentType::class)],
            'deadline' => ['nullable', 'date', 'after:today'],
        ];
    }
}
