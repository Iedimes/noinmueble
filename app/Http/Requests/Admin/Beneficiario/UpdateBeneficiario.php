<?php

namespace App\Http\Requests\Admin\Beneficiario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateBeneficiario extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    // public function authorize(): bool
    // {
    //      return Gate::allows('admin.beneficiario.edit', $this->beneficiario);
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'PerCod' => ['sometimes', 'string'],
            'PylCod' => ['sometimes', 'string'],
            'CliNop' => ['sometimes', 'string'],
            'CliSec' => ['sometimes', 'string'],
            'CliEsv' => ['sometimes', 'string'],
            'ManCod' => ['sometimes', 'string'],
            'VivLote' => ['sometimes', 'string'],
            'VivBlo' => ['sometimes', 'string'],
            'CliCMor' => ['sometimes', 'string'],

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
