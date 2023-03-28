<?php

namespace App\Http\Requests;

use App\Services\PlanOfCourseworkService;
use App\Services\TagProcessor;
use Illuminate\Foundation\Http\FormRequest;

class PcwUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(PlanOfCourseworkService $planOfCourseworkService, TagProcessor $tagProcessor)
    {
        if($this->request->access_type == 'unit') {
            $this->request = array_merge(['access_permission' => 'pcw_unit_approve']);
            return $planOfCourseworkService->hasAccess($this->request, $this->route('plan-of-courseworks'), $tagProcessor);
        } else if($this->request->access_type == 'college') {
            $this->request = array_merge(['access_permission' => 'pcw_college_approve']);
            return $planOfCourseworkService->hasAccess($this->request, $this->route('plan-of-courseworks'), $tagProcessor);
        } else if($this->request->access_type == 'faculty') {
            $this->request = array_merge(['access_permission' => 'pcw_faculty_approve']);
            return $planOfCourseworkService->hasAccess($this->request, $this->route('plan-of-courseworks'), $tagProcessor);
        } else {
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
            //
        ];
    }
}
