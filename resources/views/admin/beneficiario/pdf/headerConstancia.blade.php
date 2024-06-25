<?php
// Establecer el idioma y la configuración regional en español
setlocale(LC_ALL, 'es_ES.UTF-8', 'es_ES', 'esp');

// Función para convertir números en letras (solo para el día)
function convertirNumeroALetras($numero)
{
    $unidades = ['', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve', 'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve', 'veinte', 'veintiuno', 'veintidós', 'veintitrés', 'veinticuatro', 'veinticinco', 'veintiséis', 'veintisiete', 'veintiocho', 'veintinueve', 'treinta', 'treinta y uno'];

    if ($numero <= 31) {
        return $unidades[$numero];
    }

    return '';
}

// Función para convertir el año en letras
function convertirAnioALetras($anio)
{
    if ($anio == '2024') {
        return 'dos mil veinticuatro';
    } else {
        // Lógica para convertir el año a letras en otros casos
    }
}

// Obtener el día actual
$diaActual = date('j');

// Obtener el mes actual en letras
$mesActual = strftime('%B');

// Obtener el año actual en letras
$anioActual = convertirAnioALetras(date('Y'));

// Obtener la fecha en letras
$fechaEnLetras = 'a los ' . convertirNumeroALetras($diaActual) . ' (' . $diaActual . ') días del mes de ' . $mesActual . ' del año ' . $anioActual;

?>

<br>
<p style="text-align:right;font-size:18px;"><b>NPB {{ $cedula }}</b></p>
<p style="text-align:center;font-size:30px; font-family:Arial, Helvetica, sans-serif;">CONSTANCIA</p>
<p style="text-align: justify;">Se hace constar que el/la <b>Sr./Sra. {{ $bamper->PerNomPri }} {{ $bamper->PerNomSeg }} {{ $bamper->PerApePri }} {{ $bamper->PerApeSeg }}
</b>con <b>C.I. N°. {{ $cedula }} </b>, no registra beneficio a su nombre en los Programas respaldados documental y digitalmente de
 <b>FONAVIS, CREDITOS HIPOTECARIOS, VIVIENDAS ECONOMICAS, FOCEM, SEMBRANDO OPORTUNIDADES Y CHE TAPYI</b> de la institución, por tanto, se expide la presente constancia
 a pedido del/la interesado/a para lo que hubiere lugar, {{ $fechaEnLetras }}
 <br><br>
 <p style="text-align:center;font-size:15px;"><b>LIC. SARA IRENE RAFAR GAONA</b></p>
 <p style="text-align:center;font-size:12px;"><b>Dirección de Registro y Estadística de Información Social</b></p>

 <br><br>

 <p style="text-align:center;font-size:15px;"><b>ARQ. HECTOR VILLAGRA SANCHEZ</b></p>
 <p style="text-align:center;font-size:12px;"><b>Dirección General Social</b></p>

<br><br>
<img src="data:image/png;base64, {{ base64_encode($valor) }}" style="left: 550px; width: 100px; height: 100px;" alt="">
<br>
