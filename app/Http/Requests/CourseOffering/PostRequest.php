<?php

namespace App\Http\Requests\CourseOffering;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PostRequest extends FormRequest
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
            'institution' => ['required', 'string', 'max:255'],
            'career' => ['required', 'string', 'max:255'],
            'course_id' => ['required', 'integer'],
            'acad_org' => ['required', 'string', 'max:255'],
            'acad_group' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'catalog' => ['required', 'string', 'max:255'],
            'descr' => ['required', 'string', 'max:255'],
            'component' => ['required', 'string', 'max:255'],
            'assoc' => ['required', 'integer'],
            'section' => ['required', 'string', 'max:255'],
            'class_nbr' => ['required', 'integer'],
            'times' => ['required', 'string', 'max:255'],
            'days' => ['required', 'string', 'max:255'],
            'id' => ['required', 'integer'],
            'consent' => ['required', 'string', 'max:1'],
            'offer_nbr' => ['required', 'integer', 'min:0'],
            'topic_id' => ['required', 'integer', 'min:0'],
            'class_type' => ['required', 'string', 'max:1'],
        ];
    }
}
