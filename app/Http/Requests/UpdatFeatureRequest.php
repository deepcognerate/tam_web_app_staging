<?php

namespace App\Http\Requests;

use App\Models\Feature;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdatFeatureRequest extends FormRequest
{
    public function rules()
    {
        return [
            'feature_name' => [
                'string',
                'min:3',
                'max:50',
                'required',
            ],
        ];
    }
}
