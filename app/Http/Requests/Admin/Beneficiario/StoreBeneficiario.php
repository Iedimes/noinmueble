<?php

namespace App\Http\Requests\Admin\Beneficiario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreBeneficiario extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    // public function authorize(): bool
    // {
    //      return Gate::allows('admin.beneficiario.create');
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'PerCod' => ['required', 'string'],
            'PylCod' => ['required', 'string'],
            'CliNop' => ['required', 'string'],
            'CliSec' => ['required', 'string'],
            'CliEsv' => ['required', 'string'],
            'ManCod' => ['required', 'string'],
            'VivLote' => ['required', 'string'],
            'VivBlo' => ['required', 'string'],
            'CliCMor' => ['required', 'string'],

        ];
    }

    /**
    * Modify input data
    *
    * @return array
    */
    public function getSanitized(): array
    {
        $sanitized = $this->validated();

        //Add your code for manipulation with request data here

        return $sanitized;
    }
}
