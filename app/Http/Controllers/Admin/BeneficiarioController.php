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

        try {
            $certificados = SHMCER::where('CerPosCod', $cedula)->where('CerEst', '!=', '7')->get();
        } catch (\Throwable $e) {
            $certificados = collect();
        }

        try {
            $certificadosconyuge = SHMCER::where('CerCoCI', $cedula)->where('CerEst', '!=', '7')->get();
        } catch (\Throwable $e) {
            $certificadosconyuge = collect();
        }

        try {
            $cartera = PRMCLI::where('PerCod', $cedula)
                ->where('PylCod', '!=', 'P.F.')
                ->get();
        } catch (\Throwable $e) {
            $cartera = collect();
        }

        try {
            $solicitantetitular = IVMSOL::where('SolPerCod', $cedula)->where('SolEtapa', 'B')->first();
        } catch (\Throwable $e) {
            $solicitantetitular = null;
        }

        try {
            $solicitanteconyuge = IVMSOL::where('SolPerCge', $cedula)->where('SolEtapa', 'B')->first();
        } catch (\Throwable $e) {
            $solicitanteconyuge = null;
        }

        try {
            $cepratitular = IVMSAS::where('SASCI', $cedula)->first();
        } catch (\Throwable $e) {
            $cepratitular = null;
        }

        try {
            $cepraconyuge = IVMSAS::where('CICONY', $cedula)->first();
        } catch (\Throwable $e) {
            $cepraconyuge = null;
        }

        $response = [
            'cedula' => $cedula,
            'titular' => $persona ? $persona->PerNom : '',
            'mensaje' => '',
        ];

        // Verificación de beneficios
        if (
            $certificados->isNotEmpty() ||
            $certificadosconyuge->isNotEmpty() ||
            $cartera->isNotEmpty() ||
            $solicitantetitular ||
            $solicitanteconyuge ||
            $cepratitular ||
            $cepraconyuge
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


        $impresion = Impresion::where('ci', $cedula)->latest()->first();
        if(!empty($impresion)){

            $datosTitular = $this->obtenerDatosTitular($cedula);

            if ($datosTitular) {
                $nombre = $datosTitular['nombres'];
                $apellido = $datosTitular['apellido'];

                $response = [
                    'cedula' => $cedula,
                    'titular' => $this->armarNombreTitular($datosTitular),
                    'mensaje' => '',
                ];

                return view('verification', compact('response', 'impresion'));
            }

        }else{

            $mensaje = "EL DOCUMENTO AL CUAL SE HACE REFERENCIA NO ES VALIDO";

            return view('verification', compact('mensaje'));
        }
    }




     public function createPDF($PerCod)
    {
        try {
            $cedula = $PerCod;
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
