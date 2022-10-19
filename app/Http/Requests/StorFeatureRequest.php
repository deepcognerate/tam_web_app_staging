<?php

namespace App\Http\Requests;

use App\Models\Feature;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StorFeatureRequest extends FormRequest
{

    public function rules()
    {
        return [
            'feature_name' => [
                'string',
                'required',
            ],
        ];
    }
}
