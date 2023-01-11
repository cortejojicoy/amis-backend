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
            'institution' => ['required', 'max:255'],
            'career' => ['required', 'max:255'],
            'course_id' => ['required', 'integer'],
            'acad_org' => ['required', 'max:255'],
            'acad_group' => ['required', 'max:255'],
            'subject' => ['required', 'max:255'],
            'catalog' => ['required', 'max:255'],
            'descr' => ['required', 'max:255'],
            'component' => ['required', 'max:255'],
            'section' => ['required', 'max:255'],
            'class_nbr' => ['required', 'integer'],
            'times' => ['required', 'max:255'],
            'days' => ['required', 'max:255'],
            'id' => ['required', 'integer'],
            'consent' => ['required', 'max:1'],
            'offer_nbr' => ['required', 'integer'],
            'topic_id' => ['required', 'integer'],
        ];
    }
}
