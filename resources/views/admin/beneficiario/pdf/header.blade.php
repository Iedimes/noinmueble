<br>
<p style="text-align:center;font-size:18px;"><b>Dirección General Social</b></p>
<hr>
<p style="text-align:center;font-size:30px; font-family:Arial, Helvetica, sans-serif;"><b>CONSTANCIA</b></p>
<p style="text-align: justify;"><b>El Ministerio de Urbanismo, Vivienda y Hábitat</b> certifica que el <b>Sr./Sra. {{ $bamper->PerNomPri }} {{ $bamper->PerNomSeg }} {{ $bamper->PerApePri }} {{ $bamper->PerApeSeg }}
@if (empty($cas))
    {{ $bamper->PerApeCas }}
@else
 DE {{ $bamper->PerApeCas }}
@endif</b>con <b>C.I. N°. {{ trim($beneficiario->PerCod) }}</b>
@if (empty($conyuge))
es
@else
y <b>Sr./Sra. {{ $nomconyuge->PerNomPri }} {{ $nomconyuge->PerNomSeg }} {{ $nomconyuge->PerApePri }} {{ $nomconyuge->PerApeSeg }} </b>con <b>C.I. N°. {{ $conyuge }} </b> son
@endif
 adjudicatario/a de la
 vivienda individualizada como manzana <b>{{ trim($cuenta->ManCod) }}</b>, <b>lote {{ trim($cuenta->VivLote) }}</b> del proyecto <b> {{ trim($proyecto->PylNom) }}</b>, con
 <b>Cuenta Corriente Catastral N°. {{ trim($cuenta->VivCtaVer) }}</b>, de conformidad a la <b>Resolución N°. {{ $resolucion->CliNrs ? trim($resolucion->CliNrs):'' }}</b>, <b>Acta {{ $resolucion->CliNac ? trim($resolucion->CliNac): '' }}</b> de fecha <b>{{ date('d/m/Y', strtotime( trim($resolucion->CliFRes)))}} </b>,
  contrato de compra venta de fecha <b>{{ date('d/m/Y', strtotime(trim($contrato->CliFchCon)))}}</b> y Estado de cuenta de la Dirección de Administración y Recuperación de Cartera de fecha <b>{{ date('d/m/Y') }}</b></p>

<p style="text-align: justify;">Esta constancia es gratuita, válida por 90 días, para su presentación exclusiva ante <strong>ANDE/ESSAP</strong></p>



<br><br>
<p style="text-align: justify;">Fecha de Impresión: {{ date('d/m/Y') }}</p>
<img src="data:image/png;base64, {{ base64_encode($valor) }}" style="position: relative; left:550px;" alt="">
<br><br><br><br><br><br><br><br><br><br>
