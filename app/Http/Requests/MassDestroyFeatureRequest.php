<?php

namespace App\Http\Requests;

use App\Models\Feature;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyFeatureRequest extends FormRequest
{

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:feature,id',
        ];
    }
}
