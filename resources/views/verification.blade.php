<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MUVH - VALIDACION CONSTANCIA</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f7f7;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="container mx-auto p-4 md:p-8">
        <!-- Logo con el mismo ancho que el contenedor -->
        <div class="w-full max-w-2xl mx-auto mb-8">
            <img src="{{ url('img/logofull.jpg') }}" alt="MUVH Logo" class="w-full h-auto rounded-lg shadow-md">
        </div>

        @if (isset($response))
            <!-- Card de validación exitosa -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden max-w-2xl mx-auto border-t-8 border-red-600">
                <div class="bg-red-600 text-white p-4">
                    <h5 class="text-xl font-bold text-center">CONSTANCIA VÁLIDA</h5>
                </div>
                <div class="p-6">
                    <div class="text-center mb-6">
                        <h4 class="text-2xl font-semibold text-gray-800">DOCUMENTO VERIFICADO CORRECTAMENTE</h4>
                        <p class="text-gray-500 mt-2">Los datos a continuación corresponden a un documento oficial emitido por el MUVH.</p>
                    </div>

                    <div class="bg-gray-100 rounded-lg p-4 mb-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div class="mb-4 md:mb-0 md:w-1/2">
                                <p class="text-sm text-gray-600">SOLICITANTE:</p>
                                <p class="text-lg font-bold text-red-600">{{ $response['titular'] }}</p>
                            </div>
                            <div class="md:w-1/2">
                                <p class="text-sm text-gray-600">CÉDULA DE IDENTIDAD:</p>
                                <p class="text-lg font-bold text-red-600">{{ $response['cedula'] }}</p>
                            </div>
                        </div>
                    </div>

                    <ul class="list-none space-y-2 text-center text-gray-700">
                        <li class="bg-gray-50 rounded-md p-2">
                            <span class="font-semibold text-sm block">FECHA DE IMPRESIÓN:</span>
                            <span class="text-base font-bold">{{ date('d/m/Y', strtotime(trim($impresion->fecha_impresion)))}}</span>
                        </li>
                    </ul>

                    <div class="mt-6 text-center">
                        <p class="text-xl font-bold text-green-600">VÁLIDO POR 90 DÍAS</p>
                    </div>
                </div>
            </div>
        @else
            <!-- Card de validación fallida -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden max-w-2xl mx-auto border-t-8 border-red-600">
                <div class="bg-red-600 text-white p-4">
                    <h5 class="text-xl font-bold text-center">CONSTANCIA INVÁLIDA</h5>
                </div>
                <div class="p-6">
                    <div class="text-center mb-6">
                        <h4 class="text-2xl font-semibold text-red-600">ERROR DE VERIFICACIÓN</h4>
                        <p class="text-gray-500 mt-2">El código escaneado no corresponde a un documento válido del MUVH.</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4">
                        <p class="text-center text-lg font-bold">{{ $mensaje }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
