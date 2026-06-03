<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\InquirySubmissionRecord;

class BulkAssignInquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->guard('mcmc')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'inquiry_ids' => [
                'required',
                'array',
                'min:1',
                'max:50' // Limit bulk operations to 50 inquiries at once
            ],
            'inquiry_ids.*' => [
                'required',
                'integer',
                Rule::exists('inquiries', 'inquiry_ID')
            ],
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
            'override_existing' => [
                'sometimes',
                'boolean'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'inquiry_ids.required' => 'Please select at least one inquiry to assign.',
            'inquiry_ids.min' => 'Please select at least one inquiry to assign.',
            'inquiry_ids.max' => 'You can only assign up to 50 inquiries at once.',
            'inquiry_ids.*.exists' => 'One or more selected inquiries are not valid.',
            'agency_id.required' => 'Please select an agency to assign the inquiries to.',
            'agency_id.exists' => 'The selected agency is not valid.',
            'comments.max' => 'Assignment comments cannot exceed 1000 characters.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'inquiry_ids' => 'inquiries',
            'agency_id' => 'agency',
            'comments' => 'assignment comments'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('inquiry_ids') && $this->has('agency_id')) {
                $inquiryIds = $this->input('inquiry_ids');
                $agencyId = $this->input('agency_id');
                $overrideExisting = $this->input('override_existing', false);

                // Load inquiries
                $inquiries = InquirySubmissionRecord::whereIn('inquiry_ID', $inquiryIds)->get();

                $invalidCount = 0;
                $alreadyAssignedCount = 0;

                foreach ($inquiries as $inquiry) {
                    // Check if inquiry can be assigned
                    if (!$inquiry->canBeProcessed()) {
                        $invalidCount++;
                        continue;
                    }

                    // Check for existing assignments
                    $currentAssignment = $inquiry->currentAssignment();
                    if ($currentAssignment && !$overrideExisting) {
                        if ($currentAssignment->agency_ID == $agencyId) {
                            $alreadyAssignedCount++;
                        }
                    }
                }

                // Add validation errors
                if ($invalidCount > 0) {
                    $validator->errors()->add(
                        'inquiry_ids',
                        "{$invalidCount} inquiries cannot be assigned due to their current status."
                    );
                }

                if ($alreadyAssignedCount > 0) {
                    $validator->errors()->add(
                        'inquiry_ids',
                        "{$alreadyAssignedCount} inquiries are already assigned to the selected agency."
                    );
                }

                // Warn if more than 80% of inquiries have issues
                $totalIssues = $invalidCount + $alreadyAssignedCount;
                if ($totalIssues > 0 && ($totalIssues / count($inquiries)) > 0.8) {
                    $validator->errors()->add(
                        'bulk_assignment',
                        'Most selected inquiries have issues. Please review your selection.'
                    );
                }
            }
        });
    }

    /**
     * Get the validated data from the request.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        if ($key === null) {
            // Add default values for optional fields
            $validated['override_existing'] = $validated['override_existing'] ?? false;
            $validated['comments'] = $validated['comments'] ?? '';
        }

        return $validated;
    }
}
