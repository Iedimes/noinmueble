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

<p style="text-align:right; font-size:17px; font-family:Times New Roman;"><b>NPB {{ $response['cedula'] }}</b></p>

<p style="text-align:center; font-size:19px; font-family:Times New Roman;"><b>CONSTANCIA DE NO SER BENEFICIARIO</b></p>

<p style="text-align:center; font-size:19px; font-family:Times New Roman;"><b>MINISTERIO DE URBANISMO, VIVIENDA Y HABITAT</b></p>
<br>

<p style="text-align:justify; font-size:15px; font-family:Times New Roman;">
    Se hace constar que el/la <b>Sr./Sra. {{ $response['titular'] }}</b>
    con <b>C.I. N°. {{ $response['cedula'] }} </b>, no registra beneficio en el marco de los diferentes Programas y Proyectos Habitacionales del
    Ministerio de Urbanismo, Vivienda y Habitat - MUVH, respaldados documental y digitalmente.
    Se expide la presente constancia a pedido del/la interesado/a, {{ $fechaEnLetras }}
</p>
<br><br><br>

<p style="font-size:13px; font-family:Times New Roman; text-align: center;"><b>Emitido por: La Dirección General Social, Dirección de Registro y Estadísticas de Información Social </b></p>

<br><br><br>

<img src="data:image/png;base64, {{ base64_encode($valor) }}" style="left: 550px; width: 100px; height: 100px;" alt=""><br>
<p style="font-size:11px; font-family:Times New Roman;"><b>Verifique a través del código QR la validez de este documento.</b></p><br>
<p style="font-size:11px; font-family:Times New Roman;"><b>Registro de Sistema de Información del MUVH - Resolución Nro. xxx</b></p>

