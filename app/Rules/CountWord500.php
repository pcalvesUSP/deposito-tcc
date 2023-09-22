<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CountWord500 implements Rule
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
        return (count($arrValue) >= 1 && count($arrValue) <= 500);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O :attribute deve conter entre 1 e 500 palavras.';
        //return 'The :attribute must contain at least 500 words.';
    }
    
}
