<?php

namespace Imojie\Http\Requests;

use Imojie\Http\Requests\Request;

class RegisterRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'code' => 'required',
            'username' => 'required',
            'password' => 'required|confirmed',
        ];
    }
}
