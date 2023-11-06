<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CountWord1000 implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $arrValue = explode(" ",$value);
        return (count($arrValue) >= 1 && count($arrValue) <= 1000);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O :attribute deve conter entre 1 e 1000 palavras.';
        //return 'The validation error message.';
    }
}
