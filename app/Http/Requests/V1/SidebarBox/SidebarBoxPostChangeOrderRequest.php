<?php

namespace App\Http\Requests\V1\SidebarBox;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SidebarBoxPostChangeOrderRequest extends FormRequest
{
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
            'orders' => [
                'required',
                'array',
                'min:1',
            ],

            'orders.*' => [
                'required',
                'array:id,order',
            ],

            'orders.*.id' => [
                'required',
                'integer',
                'distinct',
                'exists:sidebar_box_post,post_id',
            ],

            'orders.*.order' => [
                'required',
                'integer',
                'min:1',
                'max:20',
                'distinct',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'orders.required' => 'ارسال فهرست ترتیب پست‌ها الزامی است.',
            'orders.array' => 'فهرست ترتیب پست‌ها باید به صورت آرایه ارسال شود.',
            'orders.min' => 'حداقل یک پست باید برای تغییر ترتیب ارسال شود.',

            'orders.*.required' => 'اطلاعات هر پست الزامی است.',
            'orders.*.array' => 'اطلاعات هر پست باید شامل شناسه و ترتیب آن باشد.',

            'orders.*.id.required' => 'شناسه پست الزامی است.',
            'orders.*.id.integer' => 'شناسه پست باید عدد صحیح باشد.',
            'orders.*.id.distinct' => 'شناسه یک پست نباید بیش از یک بار ارسال شود.',
            'orders.*.id.exists' => 'پست موردنظر در سیستم وجود ندارد.',

            'orders.*.order.required' => 'مقدار ترتیب پست الزامی است.',
            'orders.*.order.integer' => 'مقدار ترتیب پست باید عدد صحیح باشد.',
            'orders.*.order.min' => 'مقدار ترتیب پست باید حداقل ۱ باشد.',
            'orders.*.order.max' => 'مقدار ترتیب پست باید حداکثر 20 باشد.',
            'orders.*.order.distinct' => 'مقادیر ترتیب پست‌ها نباید تکراری باشند.',
        ];
    }
}
