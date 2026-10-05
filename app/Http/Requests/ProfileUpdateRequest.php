<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'regex:/^(0|\+84)[3|5|7|8|9][0-9]{8}$/'],
            'address' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
            'birthday' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'in:nam,nu,khac'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.unique' => 'Địa chỉ email này đã được sử dụng.',
            'phone.regex' => 'Số điện thoại không hợp lệ! Vui lòng nhập số điện thoại Việt Nam gồm 10 chữ số (ví dụ: 0988 123 456).',
            'address.max' => 'Địa chỉ không được vượt quá 500 ký tự.',
            'avatar.image' => 'Tệp tải lên phải là một hình ảnh.',
            'avatar.mimes' => 'Hình ảnh đại diện chỉ chấp nhận định dạng: jpeg, png, jpg, webp, gif.',
            'avatar.max' => 'Kích thước ảnh đại diện không được vượt quá 2MB.',
            'birthday.date' => 'Ngày sinh không đúng định dạng ngày tháng.',
            'gender.in' => 'Giới tính không hợp lệ.',
        ];
    }
}
