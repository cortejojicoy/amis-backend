<?php

namespace App\Http\Requests\MentorAssignment;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\MentorAssignment\PendingMentor;

class SubmitRequest extends FormRequest
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
        

        return [
            '*.faculty_id' => ['required'
                // Rule::exists('mentors')
                //     ->where('uuid', Auth::user()->uuid)
                //     ->where('faculty_id', $this->input('faculty_id')),

                // Rule::exists('mas')
                //     ->where('uuid', Auth::user()->uuid)
                //     ->where('faculty_id', $this->input('faculty_id'))
                //     ->where(function($query) {
                //         $query->where('status', '=', 'Pending');
                // }),
            ],
            '*.mentor_role' => ['required']
        ];
    }
}
