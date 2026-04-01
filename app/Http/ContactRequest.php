<?php
// app/Http/Requests/ContactRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $contactId = $this->route('contact')?->id;

        return [
            'type'              => 'required|in:supplier,client,both',
            'business_name'     => 'nullable|string|max:255',
            'first_name'        => 'nullable|string|max:255',
            'last_name'         => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255|unique:contacts,email,' . $contactId,
            'tax_number'        => 'nullable|string|max:100',
            'pay_term_number'   => 'nullable|integer|min:1',
            'pay_term_type'     => 'nullable|in:days,months',
            'opening_balance'   => 'nullable|numeric|min:0',
            'advance_balance'   => 'nullable|numeric|min:0',
            'address'           => 'nullable|string|max:1000',
            'city'              => 'nullable|string|max:255',
            'state'             => 'nullable|string|max:255',
            'country'           => 'nullable|string|max:255',
            'zip_code'          => 'nullable|string|max:20',
            'mobile'            => 'nullable|string|max:20',
            'alternate_number'  => 'nullable|string|max:20',
            'landline'          => 'nullable|string|max:20',
        ];
    }
}