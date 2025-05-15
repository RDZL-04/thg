<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Atribut :diterima.',
    'active_url' => 'Atribut :URL tidak valid.',
    'after' => 'Atribut: harus tanggal setelah :date.',
    'after_or_equal' => 'Atribut  :atribut harus berupa tanggal setelah atau sama dengan :date.',
    'alpha' => 'Atribut :atribut hanya boleh berisi huruf.',
    'alpha_dash' => 'Atribut :atribut hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num' => 'Atribut :atribut hanya boleh berisi huruf dan angka.',
    'array' => 'Atribut :atribut harus berupa array.',
    'before' => 'Atribut :atribut harus tanggal sebelumnya :date.',
    'before_or_equal' => 'Atribut :atribut harus tanggal sebelum atau sama dengan :date.',
    'between' => [
        'numeric' => 'atribut harus di antara :min and :max.',
        'file' => 'atribut harus di antara :min and :max kilobytes.',
        'string' => 'atribut harus di antara:min and :max characters.',
        'array' => 'atribut harus memiliki antara :min and :max items.',
    ],
    'boolean' => 'atribut harus benar atau salah.',
    'confirmed' => 'konfirmasi atribut tidak cocok.',
    'date' => 'atribut tanggal tidak valid.',
    'date_equals' => 'attribute harus tanggal yang sama dengan: date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => 'atribut dan: lainnya harus berbeda.',
    'digits' => 'atribut harus angka.',
    'digits_between' => 'atribut harus di antara :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => 'atribut harus alamat email yang valid.',
    'ends_with' => 'atribut harus diakhiri dengan salah satu dari berikut ini.',
    'exists' => 'Atribut yang dipilih tidak valid.',
    'file' => 'Atribut: harus berupa file.',
    'filled' => 'Bidang: atribut harus memiliki nilai.',
    'gt' => [
        'numeric' => 'Atribut: harus lebih besar dari : value.',
        'file' => 'atribut harus lebih besar dari : value kilobytes.',
        'string' => 'Atribut: harus lebih besar dari :value characters.',
        'array' => 'Atribut: harus memiliki lebih dari :value items.',
    ],
    'gte' => [
        'numeric' => 'Atribut :harus lebih besar dari atau sama:value.',
        'file' => 'Atribut :harus lebih besar dari atau sama :value kilobytes.',
        'string' => 'Atribut :harus lebih besar dari atau sama :value characters.',
        'array' => 'Atribut: harus memiliki :value items or more.',
    ],
    'image' => 'Atribut :harus berupa gambar.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => 'The :attribute may not be greater than :max characters.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => 'The :attribute must be a number.',
    'password' => 'The password is incorrect.',
    'present' => 'The :attribute field must be present.',
    'regex' => 'The :attribute format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute format is invalid.',
    'uuid' => 'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
