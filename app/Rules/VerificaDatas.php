<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class VerificaDatas implements Rule
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
        $arrData = explode("/",$value);
        $objDate = date_create($arrData[2]."/".$arrData[1]."/".$arrData[0]);
        $dataAtual = date_create('now');

        return ( checkdate($arrData[1], $arrData[0], $arrData[2]) && $objDate >= $dataAtual->format('d/m/Y') );
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O :attribute deve conter uma data válida (formato dd/mm/aaaa).';
        //return 'The :attribute must contain a valid date (format dd/mm/yyyy).';
    }
}
