<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
class BulkUpdateSaveMentorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Auth::user()->sais_id == $this->route('sais_id')){
            return true;
        }else{
            return false;
        }
    }    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "*.actions" => 'required',
            "*.sais_id" => 'required',
            "*.mentor_id" => [
                            'required',
                            'exists:faculties,sais_id'
                          ],
        ];
    }
}
