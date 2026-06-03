<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignInquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user is authenticated as MCMC staff
        return auth()->guard('mcmc')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'agency_id' => [
                'required',
                'integer',
                Rule::exists('agencies', 'agency_ID')
            ],
            'comments' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'reassignment_reason' => [
                'required_if:is_reassignment,true',
                'nullable',
                'string',
                'max:1000'
            ],
            'priority' => [
                'nullable',
                'string',
                Rule::in(['low', 'medium', 'high'])
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'agency_id.required' => 'Please select an agency to assign this inquiry to.',
            'agency_id.exists' => 'The selected agency is not valid.',
            'comments.max' => 'Assignment comments cannot exceed 1000 characters.',
            'reassignment_reason.required_if' => 'Please provide a reason for reassignment.',
            'reassignment_reason.max' => 'Reassignment reason cannot exceed 1000 characters.',
            'priority.in' => 'Priority must be one of: low, medium, high.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'agency_id' => 'agency',
            'comments' => 'assignment comments',
            'reassignment_reason' => 'reassignment reason',
            'priority' => 'priority level'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Check if this is a reassignment by looking at the inquiry's current assignment
        $inquiry = $this->route('inquiry');

        if ($inquiry && $inquiry->currentAssignment()) {
            $this->merge([
                'is_reassignment' => true
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $inquiry = $this->route('inquiry');

            // Additional business logic validation
            if ($inquiry) {
                // Check if inquiry can be assigned
                if (!$inquiry->canBeProcessed()) {
                    $validator->errors()->add('inquiry', 'This inquiry cannot be assigned in its current state.');
                }

                // Check if trying to assign to the same agency
                $currentAssignment = $inquiry->currentAssignment();
                if ($currentAssignment &&
                    $currentAssignment->agency_ID == $this->input('agency_id') &&
                    $currentAssignment->assignment_Status !== 'rejected') {
                    $validator->errors()->add('agency_id', 'This inquiry is already assigned to the selected agency.');
                }
            }
        });
    }
}
