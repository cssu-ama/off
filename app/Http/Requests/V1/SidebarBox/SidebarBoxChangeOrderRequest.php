<?php

namespace App\Http\Requests\V1\SidebarBox;

use App\Models\V1\SidebarBox\SidebarBox;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SidebarBoxChangeOrderRequest extends FormRequest
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
                'exists:sidebar_boxes,id',
            ],

            'orders.*.order' => [
                'required',
                'integer',
                'min:1',
                'max:' . (SidebarBox::query()->count()),
                'distinct',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'orders.required' => 'ارسال فهرست ترتیب باکس ها الزامی است.',
            'orders.array' => 'فهرست ترتیب باکس ها باید به صورت آرایه ارسال شود.',
            'orders.min' => 'حداقل یک باکس باید برای تغییر ترتیب ارسال شود.',

            'orders.*.required' => 'اطلاعات هر باکس الزامی است.',
            'orders.*.array' => 'اطلاعات هر باکس باید شامل شناسه و ترتیب آن باشد.',

            'orders.*.id.required' => 'شناسه باکس الزامی است.',
            'orders.*.id.integer' => 'شناسه باکس باید عدد صحیح باشد.',
            'orders.*.id.distinct' => 'شناسه یک باکس نباید بیش از یک بار ارسال شود.',
            'orders.*.id.exists' => 'باکس موردنظر در سیستم وجود ندارد.',

            'orders.*.order.required' => 'مقدار ترتیب باکس الزامی است.',
            'orders.*.order.integer' => 'مقدار ترتیب باکس باید عدد صحیح باشد.',
            'orders.*.order.min' => 'مقدار ترتیب باکس باید حداقل ۱ باشد.',
            'orders.*.order.max' => 'ترتیب نمایش نمی‌تواند بیشتر از کل تعداد باکس ها باشد.',
            'orders.*.order.distinct' => 'مقادیر ترتیب باکس ها نباید تکراری باشند.',
        ];
    }
}
