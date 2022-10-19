<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCustomerRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('customer_create');
    }

    public function rules()
    {
        return [
            'customer_name'       => [
                'string',
                'required',
            ],
            'firm_name'           => [
                'string',
                'nullable',
            ],
            'mobile_no'           => [
                'required',
                'integer',
                'min:6666666666',
                'max:9999999999',
                'unique:customers,mobile_no',
            ],
            'district'            => [
                'string',
                'nullable',
            ],
            'city'                => [
                'string',
                'nullable',
            ],
            'date_of_birth'       => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'date_of_anniversary' => [
                'string',
                'nullable',
            ],
            
        ];
    }
}
