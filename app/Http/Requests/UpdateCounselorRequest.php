<?php

namespace App\Http\Requests;

use App\Models\User;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCounselorRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('counselor_update');
    }

    public function rules()
    {
        return [
            'name'     => [
                'min:3',
                'max:50',
                'required',
            ],
            'category_id'    => [
                'required',              
            ],
            'feature_id'    => [
                'required',              
            ],
            'email'    => [
                'required',
            ],
            'phone_no'    => [
                'required',
                'integer',
                'min:6000000000',
                'max:9999999999',
            ],
            'password' => [
                'required',
            ],
            
        ];
    }
}
