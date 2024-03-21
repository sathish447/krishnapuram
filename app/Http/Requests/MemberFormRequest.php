<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;


class MemberFormRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $uid = (int)$this->id;
        //  if($uid == 0) {
            return [
                'id' => 'required|max:20',
                'name' => 'required|max:255',
                'fname' => 'required|max:255',
                'mname' => 'required|max:255',
                'designation' => 'nullable|max:100',
                'farm_note' => 'sometimes|max:5000',
                'user_type' => 'sometimes',
                'phone' => ["required",
                    "max:10",
                    Rule::unique('user', 'phone')->where('is_active', true)->whereNull('deleted_at')->ignore($uid)],
                'role_id' => 'required|max:20',
                'vendor_id' => 'required|max:20',
                'state_id' => 'sometimes',
                'district_id' => 'sometimes',
                'email' => ["required",
                    "email",
                    "max:255",
                    Rule::unique('user', 'email')->where('is_active', true)->whereNull('deleted_at')->ignore($uid)],
                'password' => 'nullable|max:255|regex:' . conf("password_format") . "|confirmed",
                'password_confirmation' => 'nullable|max:255|regex:' . conf("password_format")
            ];
        //  }
    }

    public function messages()
    {
        return [
            'password.regex' => conf("password_message")
        ];
    }
    protected function prepareForValidation()
    {
        $data = cleanRequest($this->rules(), $this);
        $this->replace($data);
    }
}
