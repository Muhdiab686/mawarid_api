<?php

namespace App\Http\Requests\SubAgency;

use App\Models\SubAgency;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubAgencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * When only moving to another agency, validate the current name against the target agency.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('agency_id') && ! $this->has('name')) {
            /** @var SubAgency $subAgency */
            $subAgency = $this->route('sub_agency');

            $this->merge(['name' => $subAgency->name]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var SubAgency|null $subAgency */
        $subAgency = $this->route('sub_agency');

        $targetAgencyId = $this->integer('agency_id') ?: $subAgency?->agency_id;

        return [
            /** نقل الجهة الفرعية إلى جهة رئيسية أخرى (تنتقل عناصرها معها) */
            'agency_id' => ['sometimes', 'required', 'integer', Rule::exists('agencies', 'id')->withoutTrashed()],
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('sub_agencies', 'name')
                    ->where('agency_id', $targetAgencyId)
                    ->withoutTrashed()
                    ->ignore($subAgency),
            ],
        ];
    }
}
