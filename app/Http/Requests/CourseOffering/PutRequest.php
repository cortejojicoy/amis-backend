<?php

namespace App\Http\Requests\CourseOffering;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Auth::user()->hasRole('super_admin')){
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
            'institution' => ['required', 'string', 'max:255', 'min:1'],
            'career' => ['required', 'string', 'max:255','min:1'],
            'course_id' => ['required', 'integer', 'min:1'],
            'acad_org' => ['required', 'string', 'max:255', 'min:1'],
            'acad_group' => ['required', 'string', 'max:255', 'min:1'],
            'subject' => ['required', 'string', 'max:255', 'min:1'],
            'catalog' => ['required', 'string', 'max:255', 'min:1'],
            'descr' => ['required', 'string', 'max:255', 'min:1'],
            'component' => ['required', 'string', 'max:255', 'min:1'],
            'section' => ['required', 'string', 'max:255', 'min:1'],
            'class_nbr' => ['required', 'integer', 'min:1'],
            'times' => ['required', 'string', 'max:255', 'min:1'],
            'days' => ['required', 'string', 'max:255', 'min:1'],
            'id' => ['required', 'integer', 'min:1'],
            'consent' => ['required', 'string', 'max:1', 'min:1'],
            'offer_nbr' => ['required', 'integer', 'min:1'],
            'topic_id' => ['required', 'integer', 'min:1'],
        ];
    }
}
