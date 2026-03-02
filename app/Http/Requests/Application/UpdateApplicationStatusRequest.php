<?php

namespace App\Http\Requests\Application;

use App\Enums\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateApplicationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller after loading the model.
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(ApplicationStatus::class)],
        ];
    }
}
