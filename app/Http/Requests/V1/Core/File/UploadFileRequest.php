<?php

namespace App\Http\Requests\V1\Core\File;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;

class UploadFileRequest extends FormRequest
{
    /**
     * @return array
     */
    private function allowedFileExtensions(): array
    {
        return collect(config('mediable.aggregate_types'))
            ->pluck('extensions')
            ->flatten()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                File::types($this->allowedFileExtensions())
                    ->max('100mb'),
            ],
            'alt' => [
                'sometimes',
                'nullable',
                'string',
                'min:3',
                'max:255'
            ]
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'file.required' => 'انتخاب فایل الزامی است.',
            'file.mimes' => 'فرمت فایل انتخاب‌شده مجاز نیست.',
            'file.max' => 'حجم فایل نباید بیشتر از 100 مگابایت باشد.',
            'file.uploaded' => 'آپلود فایل با خطا مواجه شد.',

            'alt.string' => 'متن جایگزین باید از نوع رشته باشد.',
            'alt.min' => 'متن جایگزین باید حداقل ۳ کاراکتر باشد.',
            'alt.max' => 'متن جایگزین نباید بیشتر از ۲۵۵ کاراکتر باشد.',
        ];
    }
}
