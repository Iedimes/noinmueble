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
         if (!empty($cedula)) {
             $persona = Bamper::where('PerCod', $cedula)->select('PerNom', 'PerCod')->first();

             if (!empty($persona)) {
                 $certificados = SHMCER::where('CerPosCod', $cedula)->get();
                 $certificadosconyuge = SHMCER::where('CerCoCI', $cedula)->get();
                 $cartera = PRMCLI::where('PerCod', $cedula)
                     ->where('PylCod', '!=', 'P.F.')
                     ->get();
                $solicitantetitular = IVMSOL::where('SolPerCod', $cedula)
                                                            ->where('SolEtapa', 'B')
                                                            ->first();
                 $solicitanteconyuge = IVMSOL::where('SolPerCge', $cedula)->first();
                 $cepratitular = IVMSAS::where('SASCI', $cedula)->first();
                 $cepraconyuge = IVMSAS::where('CICONY', $cedula)->first();

                 $headers = [
                     'Content-Type' => 'application/json',
                     'Accept' => 'application/json'
                 ];

                 $GetOrder = [
                     'username' => 'muvhConsulta',
                     'password' => '*Sipp*2025**'
                 ];

                 $client = new Client();
                 $res = $client->post('https://sii.paraguay.gov.py/security', [
                     'headers' => $headers,
                     'json' => $GetOrder,
                     'decode_content' => false
                 ]);

                 $contents = $res->getBody()->getContents();
                 $book = json_decode($contents);

                 if ($book->success == true) {
                     $headerscedula = [
                         'Authorization' => 'Bearer ' . $book->token,
                         'Accept' => 'application/json',
                         'decode_content' => false
                     ];

                     $cedulaResponse = $client->get('https://sii.paraguay.gov.py/frontend-identificaciones/api/persona/obtenerPersonaPorCedula/' . $cedula, [
                         'headers' => $headerscedula,
                     ]);

                     $datos = $cedulaResponse->getBody()->getContents();
                     $datospersona = json_decode($datos);

                     if (isset($datospersona->obtenerPersonaPorNroCedulaResponse->return->error)) {
                         return redirect()->back()->with('status', $datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
                     } else {
                         $nombre = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nombres;
                         $apellido = $datospersona->obtenerPersonaPorNroCedulaResponse->return->apellido;
                         $cedula = $datospersona->obtenerPersonaPorNroCedulaResponse->return->cedula;
                         $sexo = $datospersona->obtenerPersonaPorNroCedulaResponse->return->sexo;
                         $fecha = date('Y-m-d H:i:s.v', strtotime($datospersona->obtenerPersonaPorNroCedulaResponse->return->fechNacim));
                         $nac = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nacionalidadBean;
                         $est = $datospersona->obtenerPersonaPorNroCedulaResponse->return->estadoCivil;
                         $nroexp = $cedula;

                         $response = [
                             'cedula' => $cedula,
                             'titular' => $nombre . ' ' . $apellido,
                             'mensaje' => '',
                         ];

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
                         }

                         return $response;
                     }
                 }
             } else {
                //return "si esta vacio o no existe";
                $certificados = SHMCER::where('CerPosCod', $cedula)->get();
                $certificadosconyuge = SHMCER::where('CerCoCI', $cedula)->get();
                $cartera = PRMCLI::where('PerCod', $cedula)
                    ->where('PylCod', '!=', 'P.F.')
                    ->get();
                $solicitantetitular = IVMSOL::where('SolPerCod', $cedula)
                    ->where('SolEtapa', 'B')
                    ->first();
                $solicitanteconyuge = IVMSOL::where('SolPerCge', $cedula)->first();
                $cepratitular = IVMSAS::where('SASCI', $cedula)->first();
                $cepraconyuge = IVMSAS::where('CICONY', $cedula)->first();

                $headers = [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ];

                $GetOrder = [
                    'username' => 'muvhConsulta',
                    'password' => '*Sipp*2025**'
                ];

                $client = new Client();
                $res = $client->post('https://sii.paraguay.gov.py/security', [
                    'headers' => $headers,
                    'json' => $GetOrder,
                    'decode_content' => false
                ]);

                $contents = $res->getBody()->getContents();
                $book = json_decode($contents);

                if ($book->success == true) {
                    $headerscedula = [
                        'Authorization' => 'Bearer ' . $book->token,
                        'Accept' => 'application/json',
                        'decode_content' => false
                    ];

                    $cedulaResponse = $client->get('https://sii.paraguay.gov.py/frontend-identificaciones/api/persona/obtenerPersonaPorCedula/' . $cedula, [
                        'headers' => $headerscedula,
                    ]);

                    $datos = $cedulaResponse->getBody()->getContents();
                    $datospersona = json_decode($datos);

                    if (isset($datospersona->obtenerPersonaPorNroCedulaResponse->return->error)) {
                        return redirect()->back()->with('status', $datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
                    } else {
                        $nombre = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nombres;
                        $apellido = $datospersona->obtenerPersonaPorNroCedulaResponse->return->apellido;
                        $cedula = $datospersona->obtenerPersonaPorNroCedulaResponse->return->cedula;
                        $sexo = $datospersona->obtenerPersonaPorNroCedulaResponse->return->sexo;
                        $fecha = date('Y-m-d H:i:s.v', strtotime($datospersona->obtenerPersonaPorNroCedulaResponse->return->fechNacim));
                        $nac = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nacionalidadBean;
                        $est = $datospersona->obtenerPersonaPorNroCedulaResponse->return->estadoCivil;
                        $nroexp = $cedula;

                        $response = [
                            'cedula' => $cedula,
                            'titular' => $nombre . ' ' . $apellido,
                            'mensaje' => '',
                        ];

                     return $response;
                    }
                }
            }

         }
     }



   public function verificacion($cedula)
    {


        $impresion = Impresion::where('ci', $cedula)->latest()->first();
        if(!empty($impresion)){

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            $GetOrder = [
                'username' => 'muvhConsulta',
                'password' => '*Sipp*2025**'
            ];

            $client = new Client();
            $res = $client->post('https://sii.paraguay.gov.py/security', [
                'headers' => $headers,
                'json' => $GetOrder,
                'decode_content' => false
            ]);

            $contents = $res->getBody()->getContents();
            $book = json_decode($contents);

            if ($book->success == true) {
                $headerscedula = [
                    'Authorization' => 'Bearer ' . $book->token,
                    'Accept' => 'application/json',
                    'decode_content' => false
                ];

                $cedulaResponse = $client->get('https://sii.paraguay.gov.py/frontend-identificaciones/api/persona/obtenerPersonaPorCedula/' . $cedula, [
                    'headers' => $headerscedula,
                ]);

                $datos = $cedulaResponse->getBody()->getContents();
                $datospersona = json_decode($datos);

                if (isset($datospersona->obtenerPersonaPorNroCedulaResponse->return->error)) {
                    return redirect()->back()->with('status', $datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
                } else {
                    $nombre = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nombres;
                    $apellido = $datospersona->obtenerPersonaPorNroCedulaResponse->return->apellido;
                    $cedula = $datospersona->obtenerPersonaPorNroCedulaResponse->return->cedula;
                    $sexo = $datospersona->obtenerPersonaPorNroCedulaResponse->return->sexo;
                    $fecha = date('Y-m-d H:i:s.v', strtotime($datospersona->obtenerPersonaPorNroCedulaResponse->return->fechNacim));
                    $nac = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nacionalidadBean;
                    $est = $datospersona->obtenerPersonaPorNroCedulaResponse->return->estadoCivil;
                    $nroexp = $cedula;

                    $response = [
                        'cedula' => $cedula,
                        'titular' => $nombre . ' ' . $apellido,
                        'mensaje' => '',
                    ];

                    return view('verification', compact('response', 'impresion'));


                }
            }

        }else{

            $mensaje = "EL DOCUMENTO AL CUAL SE HACE REFERENCIA NO ES VALIDO";

            return view('verification', compact('mensaje'));
        }
    }




    public function createPDF($PerCod)
    {

        $cedula=$PerCod;
        $impresion = new Impresion;
        $impresion->ci=$PerCod;
        $impresion->fecha_impresion = date('Y-m-d h:i:s');
        $impresion->save();


        $bamper = Bamper::where('PerCod', $cedula)->select('PerNom', 'PerNomPri','PerNomSeg', 'PerApePri', 'PerApeSeg', 'PerApeCas', 'PerCod')->first();

        if(!empty($bamper)){


            // // $codigoQr = QrCode::size(150)->generate(env('APP_URL') . '/' . $PerCod);
            $codigoQr = QrCode::size(150)->generate(config('app.url') . '/' . $PerCod);

            //$codigoQr = QrCode::size(150)->generate($bamper);
            $pdf = PDF::loadView('admin.beneficiario.pdf.constancia',
                    [
                       'bamper' => $bamper,
                       'valor' => $codigoQr,
                       'cedula' => trim($bamper['PerCod']),
                    ]
                );
                return $pdf->download('Constancia.pdf');

        }else{
            //return "buscar en identificaciones";

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            $GetOrder = [
                'username' => 'muvhConsulta',
                'password' => '*Sipp*2025**'
            ];

            $client = new Client();
            $res = $client->post('https://sii.paraguay.gov.py/security', [
                'headers' => $headers,
                'json' => $GetOrder,
                'decode_content' => false
            ]);

            $contents = $res->getBody()->getContents();
            $book = json_decode($contents);

            if ($book->success == true) {
                $headerscedula = [
                    'Authorization' => 'Bearer ' . $book->token,
                    'Accept' => 'application/json',
                    'decode_content' => false
                ];

                $cedulaResponse = $client->get('https://sii.paraguay.gov.py/frontend-identificaciones/api/persona/obtenerPersonaPorCedula/' . $cedula, [
                    'headers' => $headerscedula,
                ]);

                $datos = $cedulaResponse->getBody()->getContents();
                $datospersona = json_decode($datos);

                if (isset($datospersona->obtenerPersonaPorNroCedulaResponse->return->error)) {
                    return redirect()->back()->with('status', $datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
                } else {
                    $nombre = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nombres;
                    $apellido = $datospersona->obtenerPersonaPorNroCedulaResponse->return->apellido;
                    $cedula = $datospersona->obtenerPersonaPorNroCedulaResponse->return->cedula;
                    $sexo = $datospersona->obtenerPersonaPorNroCedulaResponse->return->sexo;
                    $fecha = date('Y-m-d H:i:s.v', strtotime($datospersona->obtenerPersonaPorNroCedulaResponse->return->fechNacim));
                    $nac = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nacionalidadBean;
                    $est = $datospersona->obtenerPersonaPorNroCedulaResponse->return->estadoCivil;
                    $nroexp = $cedula;

                    $response = [
                        'cedula' => $cedula,
                        'titular' => $nombre . ' ' . $apellido,
                        'mensaje' => '',
                    ];

                    // $codigoQr = QrCode::size(150)->generate(env('APP_URL') . '/' . $PerCod);
                    $codigoQr = QrCode::size(150)->generate(config('app.url') . '/' . $PerCod);
                    $pdf = PDF::loadView('admin.beneficiario.pdf.constancia', [
                        'response' => $response,
                        'valor' => $codigoQr,
                    ]);
                    return $pdf->download('Constancia.pdf');
                }

            }


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
