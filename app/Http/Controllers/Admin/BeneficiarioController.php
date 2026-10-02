<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Beneficiario\BulkDestroyBeneficiario;
use App\Http\Requests\Admin\Beneficiario\DestroyBeneficiario;
use App\Http\Requests\Admin\Beneficiario\IndexBeneficiario;
use App\Http\Requests\Admin\Beneficiario\StoreBeneficiario;
use App\Http\Requests\Admin\Beneficiario\UpdateBeneficiario;
use App\Models\SIG005;
use App\Models\SIG006;
use App\Models\PRMCLI;
use App\Models\IVMPYL;
use App\Models\SHMCER;
use App\Models\IVMSOL;
use App\Models\IVMSAS;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\Beneficiario;
use App\Models\Bamper;
use App\Models\Proyecto;
use App\Models\Resolucion;
use App\Models\Ctacte;
use App\Models\Programa;
use App\Models\Impresion;
use App\Models\Conyuge;
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;
use Illuminate\Support\Facades\Cache;




class BeneficiarioController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexBeneficiario $request
     * @return array|Factory|View
     */

     public function index($cedula)
    {
        if (empty($cedula)) {
            return response()->json([
                'error' => 'Cédula no proporcionada.'
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
        }

        $persona = $this->obtenerPersonaLocal($cedula);
        $cedulasFamilia = $this->obtenerCedulasFamilia($cedula);

        try {
            $certificados = SHMCER::whereIn('CerPosCod', $cedulasFamilia)->where('CerEst', '!=', '7')->get();
        } catch (\Throwable $e) {
            $certificados = collect();
        }

        try {
            $certificadosconyuge = SHMCER::whereIn('CerCoCI', $cedulasFamilia)->where('CerEst', '!=', '7')->get();
        } catch (\Throwable $e) {
            $certificadosconyuge = collect();
        }

        try {
            $cartera = PRMCLI::whereIn('PerCod', $cedulasFamilia)
                ->where('PylCod', '!=', 'P.F.')
                ->get();
        } catch (\Throwable $e) {
            $cartera = collect();
        }

        try {
            $cepratitular = IVMSAS::whereIn('SASCI', $cedulasFamilia)->first();
        } catch (\Throwable $e) {
            $cepratitular = null;
        }

        try {
            $cepraconyuge = IVMSAS::whereIn('CICONY', $cedulasFamilia)->first();
        } catch (\Throwable $e) {
            $cepraconyuge = null;
        }

        $cedulasAfectadas = [
            '2508554', '2001270', '4135149', '1602089', '3213622', '3628661', '3613336', '4588784', '6301183', '2980252',
            '3560991', '5498464', '1962553', '4818890', '5363337', '5684474', '6158007', '3790675', '5615685', '5776947',
            '5137975', '4688661', '4668661', '5959002', '1439092', '4294621', '1478762', '4700401', '6301040', '5730748',
            '5844678', '4583319', '4983734', '6199401', '5101904', '5004305', '4261449', '2081933', '3552606', '4994592',
            '6091993', '4876998', '6287864', '4047744', '5802656', '4354507', '7446306', '2633800', '6301031', '5395247',
            '6067369', '4048247', '7113995', '5861908', '5212393', '2081586', '6593328', '1776573'
        ];

        $response = [
            'cedula' => $cedula,
            'titular' => $persona ? $persona->PerNom : '',
            'mensaje' => '',
        ];

        try {
            $solTipo20 = IVMSOL::where(function ($q) use ($cedulasFamilia) {
                $q->whereIn('SolPerCod', $cedulasFamilia)
                  ->orWhereIn('SolPerCge', $cedulasFamilia);
            })
            ->where('SolTipo', 20)
            ->where('SolEtapa', 'B')
            ->first();
        } catch (\Throwable $e) {
            $solTipo20 = null;
        }

        // Verificación de beneficios (titular y cónyuge)
        if (
            !empty(array_intersect($cedulasFamilia, $cedulasAfectadas)) ||
            $certificados->isNotEmpty() ||
            $certificadosconyuge->isNotEmpty() ||
            $cartera->isNotEmpty() ||
            $cepratitular ||
            $cepraconyuge ||
            $solTipo20
        ) {
            $response['mensaje'] = 'UD. CUENTA CON BENEFICIO EN EL MINISTERIO DE URBANISMO, VIVIENDA Y HABITAT. NO ES POSIBLE IMPRIMIR LA CONSTANCIA.';
            return response()->json($response)
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }

        $datosTitular = $this->obtenerDatosTitular($cedula);
        if ($datosTitular) {
            $response['titular'] = $this->armarNombreTitular($datosTitular);
            return response()->json($response)
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }

        // Si no se pudo obtener el nombre ni de API ni de cache
        if (empty($response['titular'])) {
            return response()->json([
                'error' => 'No se pudo obtener los datos del titular. Intente nuevamente más tarde.'
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
        }

        // Fallback final
        return response()->json($response)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }




    public function verificacion($cedula)
    {
        $cedula = trim($cedula);
        $impresion = Impresion::where('ci', $cedula)->latest()->first();
        
        if (empty($impresion)) {
            $impresion = Impresion::whereRaw('TRIM(ci) = ?', [$cedula])->latest()->first();
        }

        if(!empty($impresion)){

            $cedulasAfectadas = [
                '2508554', '2001270', '4135149', '1602089', '3213622', '3628661', '3613336', '4588784', '6301183', '2980252',
                '3560991', '5498464', '1962553', '4818890', '5363337', '5684474', '6158007', '3790675', '5615685', '5776947',
                '5137975', '4688661', '4668661', '5959002', '1439092', '4294621', '1478762', '4700401', '6301040', '5730748',
                '5844678', '4583319', '4983734', '6199401', '5101904', '5004305', '4261449', '2081933', '3552606', '4994592',
                '6091993', '4876998', '6287864', '4047744', '5802656', '4354507', '7446306', '2633800', '6301031', '5395247',
                '6067369', '4048247', '7113995', '5861908', '5212393', '2081586', '6593328', '1776573'
            ];

            $datosTitular = $this->obtenerDatosTitular($cedula);

            if (in_array($cedula, $cedulasAfectadas)) {
                $response = [
                    'cedula' => $cedula,
                    'titular' => $datosTitular ? $this->armarNombreTitular($datosTitular) : 'TITULAR CONSULTADO',
                    'estado' => 'requiere_reemision',
                    'mensaje' => 'El presente documento requiere una nueva emisión debido a un proceso de actualización y sincronización de datos institucionales. El documento físico impreso no posee validez para trámites actuales. Se solicita al titular gestionar la re-emisión de la constancia actualizada a través de los canales correspondientes.',
                ];

                return view('verification', compact('response', 'impresion'));
            }

            $titularNombre = $datosTitular ? $this->armarNombreTitular($datosTitular) : '';
            if (empty($titularNombre)) {
                $personaLocal = $this->obtenerPersonaLocal($cedula);
                $titularNombre = $personaLocal ? trim($personaLocal->PerNom) : 'TITULAR REGISTRADO';
            }

            $response = [
                'cedula' => $cedula,
                'titular' => $titularNombre,
                'estado' => 'valido',
                'mensaje' => '',
            ];

            return view('verification', compact('response', 'impresion'));

        }else{

            $mensaje = "EL DOCUMENTO AL CUAL SE HACE REFERENCIA NO ES VALIDO";

            return view('verification', compact('mensaje'));
        }
    }




     public function createPDF($PerCod)
    {
        try {
            $cedula = $PerCod;

            $cedulasAfectadas = [
                '2508554', '2001270', '4135149', '1602089', '3213622', '3628661', '3613336', '4588784', '6301183', '2980252',
                '3560991', '5498464', '1962553', '4818890', '5363337', '5684474', '6158007', '3790675', '5615685', '5776947',
                '5137975', '4688661', '4668661', '5959002', '1439092', '4294621', '1478762', '4700401', '6301040', '5730748',
                '5844678', '4583319', '4983734', '6199401', '5101904', '5004305', '4261449', '2081933', '3552606', '4994592',
                '6091993', '4876998', '6287864', '4047744', '5802656', '4354507', '7446306', '2633800', '6301031', '5395247',
                '6067369', '4048247', '7113995', '5861908', '5212393', '2081586', '6593328', '1776573'
            ];

            $cedulasFamilia = $this->obtenerCedulasFamilia($cedula);

            try {
                $certificados = SHMCER::whereIn('CerPosCod', $cedulasFamilia)->where('CerEst', '!=', '7')->exists();
            } catch (\Throwable $e) {
                $certificados = false;
            }

            try {
                $certificadosconyuge = SHMCER::whereIn('CerCoCI', $cedulasFamilia)->where('CerEst', '!=', '7')->exists();
            } catch (\Throwable $e) {
                $certificadosconyuge = false;
            }

            try {
                $cartera = PRMCLI::whereIn('PerCod', $cedulasFamilia)->where('PylCod', '!=', 'P.F.')->exists();
            } catch (\Throwable $e) {
                $cartera = false;
            }

            try {
                $cepratitular = IVMSAS::whereIn('SASCI', $cedulasFamilia)->exists();
            } catch (\Throwable $e) {
                $cepratitular = false;
            }

            try {
                $cepraconyuge = IVMSAS::whereIn('CICONY', $cedulasFamilia)->exists();
            } catch (\Throwable $e) {
                $cepraconyuge = false;
            }

            try {
                $solTipo20 = IVMSOL::where(function ($q) use ($cedulasFamilia) {
                    $q->whereIn('SolPerCod', $cedulasFamilia)
                      ->orWhereIn('SolPerCge', $cedulasFamilia);
                })
                ->where('SolTipo', 20)
                ->where('SolEtapa', 'B')
                ->exists();
            } catch (\Throwable $e) {
                $solTipo20 = false;
            }

            if (
                !empty(array_intersect($cedulasFamilia, $cedulasAfectadas)) ||
                $certificados ||
                $certificadosconyuge ||
                $cartera ||
                $cepratitular ||
                $cepraconyuge ||
                $solTipo20
            ) {
                return response('UD. CUENTA CON BENEFICIO EN EL MINISTERIO DE URBANISMO, VIVIENDA Y HABITAT. NO ES POSIBLE IMPRIMIR LA CONSTANCIA.', 403);
            }

            $datosTitular = $this->obtenerDatosTitular($cedula);

            if (!$datosTitular) {
                return response('No se pudieron obtener los datos de la API de identificaciones para esta cédula.', 404);
            }

            $bamperApi = (object)[
                'PerNom' => $datosTitular['nombres'],
                'PerApePri' => $datosTitular['apellido'] ?? '',
                'PerCod' => $cedula,
            ];

            // Registrar impresión
            $impresion = new Impresion;
            $impresion->ci = $PerCod;
            $impresion->fecha_impresion = now();
            $impresion->save();

            // Generar QR
            $codigoQr = base64_encode(QrCode::format('svg')->size(150)->generate(
                config('app.url') . '/verificacion/' . $bamperApi->PerCod
            ));

            $pdf = PDF::loadView('admin.beneficiario.pdf.constancia', [
                'bamper' => $bamperApi,
                'valor' => $codigoQr,
                'cedula' => $cedula,
            ]);

            return $pdf->download('Constancia.pdf');

        } catch (\Exception $e) {
            abort(500, 'Ocurrió un error inesperado: ' . $e->getMessage());
        }
    }

    private function obtenerDatosTitular($cedula)
    {
        $cacheKey = 'persona_' . md5($cedula);

        if (Cache::has($cacheKey)) {
            $datosCache = Cache::get($cacheKey);
            if ($datosCache && (!empty($datosCache['nombres']) || !empty($datosCache['apellido']))) {
                return $this->normalizarDatosTitular($datosCache, $cedula);
            }
        }

        $datos = null;
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $datos = $this->buscarPersonaEnApiIdentificaciones($cedula);
            if ($datos) {
                break;
            }

            if ($attempt < 2) {
                usleep(250000);
            }
        }

        if (!$datos) {
            $persona = $this->obtenerPersonaLocal($cedula);
            if ($persona) {
                $datos = [
                    'nombres' => $this->normalizarTexto($persona->PerNom ?? ''),
                    'apellido' => $this->normalizarTexto($persona->PerApePri ?? ''),
                    'cedula' => $cedula,
                ];
            }
        }

        if ($datos) {
            Cache::put($cacheKey, $this->normalizarDatosTitular($datos, $cedula), now()->addMinutes(60));
        }

        return $datos;
    }

    private function normalizarDatosTitular($datos, $cedula)
    {
        return [
            'nombres' => $this->normalizarTexto($datos['nombres'] ?? ''),
            'apellido' => $this->normalizarTexto($datos['apellido'] ?? ''),
            'cedula' => $cedula,
        ];
    }

    private function armarNombreTitular($datos)
    {
        return trim(($datos['nombres'] ?? '') . ' ' . ($datos['apellido'] ?? ''));
    }

    private function normalizarTexto($texto)
    {
        if (!is_string($texto)) {
            return '';
        }

        $texto = mb_convert_encoding($texto, 'UTF-8', 'auto');
        $texto = preg_replace('/[^\P{C}]+/u', '', $texto);

        return trim($texto);
    }

    private function obtenerPersonaLocal($cedula)
    {
        try {
            return Bamper::where('PerCod', $cedula)
                ->select('PerNom', 'PerCod')
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function buscarPersonaEnApiIdentificaciones($cedula)
    {
        $headers = ['Content-Type' => 'application/json', 'Accept' => 'application/json'];
        $credentials = ['username' => 'muvhConsulta', 'password' => '*Sipp*2025**'];
        $client = new Client();

        try {
            $res = $client->post('https://sii.paraguay.gov.py/security', [
                'headers' => $headers,
                'json' => $credentials,
                'decode_content' => false,
                'http_errors' => false,
                'verify' => false,
                'timeout' => 10,
            ]);

            if ($res->getStatusCode() !== 200) {
                return null;
            }

            $content = $res->getBody()->getContents();
            $book = json_decode($content);

            if (empty($book->success) || empty($book->token)) {
                return null;
            }

            $cedulaResponse = $client->get(
                'https://sii.paraguay.gov.py/frontend-identificaciones/api/persona/obtenerPersonaPorCedula/' . $cedula,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $book->token,
                        'Accept' => 'application/json',
                        'Cache-Control' => 'no-cache, no-store, must-revalidate',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                        'Connection' => 'close',
                    ],
                    'query' => ['_t' => uniqid()],
                    'http_errors' => false,
                    'decode_content' => false,
                    'verify' => false,
                    'timeout' => 10,
                ]
            );

            if ($cedulaResponse->getStatusCode() !== 200) {
                return null;
            }

            $datos = $cedulaResponse->getBody()->getContents();
            $datospersona = json_decode($datos);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            $returnData = $datospersona->obtenerPersonaPorNroCedulaResponse->return ?? null;
            if (!$returnData || !empty($returnData->error)) {
                return null;
            }

            $nombre = $this->normalizarTexto($returnData->nombres ?? '');
            $apellido = $this->normalizarTexto($returnData->apellido ?? '');

            if ($nombre === '' && $apellido === '') {
                return null;
            }

            return [
                'nombres' => $nombre,
                'apellido' => $apellido,
                'cedula' => $cedula,
            ];
        } catch (RequestException $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function obtenerCedulasFamilia($cedula)
    {
        $cedulas = [trim($cedula)];

        try {
            $solTitulares = IVMSOL::where('SolPerCge', $cedula)->pluck('SolPerCod')->toArray();
            $solConyuges = IVMSOL::where('SolPerCod', $cedula)->pluck('SolPerCge')->toArray();
            $cedulas = array_merge($cedulas, $solTitulares, $solConyuges);
        } catch (\Throwable $e) {}

        try {
            $cerTitulares = SHMCER::where('CerCoCI', $cedula)->pluck('CerPosCod')->toArray();
            $cerConyuges = SHMCER::where('CerPosCod', $cedula)->pluck('CerCoCI')->toArray();
            $cedulas = array_merge($cedulas, $cerTitulares, $cerConyuges);
        } catch (\Throwable $e) {}

        try {
            $sasTitulares = IVMSAS::where('CICONY', $cedula)->pluck('SASCI')->toArray();
            $sasConyuges = IVMSAS::where('SASCI', $cedula)->pluck('CICONY')->toArray();
            $cedulas = array_merge($cedulas, $sasTitulares, $sasConyuges);
        } catch (\Throwable $e) {}

        $cedulasLimpias = array_map('trim', $cedulas);
        $cedulasLimpias = array_filter($cedulasLimpias, function($val) {
            return !empty($val);
        });

        return array_values(array_unique($cedulasLimpias));
    }






    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        //$this->authorize('admin.beneficiario.create');

        return view('admin.beneficiario.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreBeneficiario $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreBeneficiario $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Beneficiario
        $beneficiario = Beneficiario::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/beneficiarios'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/beneficiarios');
    }

    /**
     * Display the specified resource.
     *
     * @param Beneficiario $beneficiario
     * @throws AuthorizationException
     * @return void
     */
    public function show(Beneficiario $beneficiario)
    {
        //$this->authorize('admin.beneficiario.show', $beneficiario);

        return view('admin.beneficiario.show');

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Beneficiario $beneficiario
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Beneficiario $beneficiario)
    {
        //$this->authorize('admin.beneficiario.edit', $beneficiario);


        return view('admin.beneficiario.edit', [
            'beneficiario' => $beneficiario,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBeneficiario $request
     * @param Beneficiario $beneficiario
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateBeneficiario $request, Beneficiario $beneficiario)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Beneficiario
        $beneficiario->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/beneficiarios'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/beneficiarios');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyBeneficiario $request
     * @param Beneficiario $beneficiario
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyBeneficiario $request, Beneficiario $beneficiario)
    {
        $beneficiario->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyBeneficiario $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyBeneficiario $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Beneficiario::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
