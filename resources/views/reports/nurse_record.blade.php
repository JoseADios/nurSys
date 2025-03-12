<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Diario de la Enfermería</title>
    <style>
        @page {
            margin: 20px;
            size: letter;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px 10px;
            font-size: 12px;
        }

        .container {
            border: 1px solid #696969;
            border-radius: 10px;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border: 1px solid #696969;
            border-radius: 10px;
            padding: 5px;
            position: relative;
        }

        .logo {
            position: absolute;
            left: 10px;
            top: 5px;
            width: 80px;
        }

        .img-logo {
            border-radius: 5px;
        }

        .hospital-info {
            font-size: 11px;
            text-align: center;
        }

        .hospital-name {
            font-weight: bold;
            font-size: 20px;
            margin-top: 0px;
            margin-bottom: 5px;
        }

        .title {
            font-size: 16px;
            text-align: center;
            border: 1px solid #696969;
            border-radius: 15px;
            padding: 3px 10px;
            font-weight: 600;
            display: inline-block;
            margin: 10px auto;
            color: #000;
            font-weight: bold;

        }

        .patient-info {
            width: 100%;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            text-align: start;
        }

        .patient-details {
            width: 75%;
        }

        .signature-container-header {
            width: 20%;
            text-align: center;
        }

        /* Contenedor para aplicar el borde redondeado */
        .table-container {
            border: 1px solid #777777;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
            padding: 0;
        }

        /* Estilos para la tabla */
        .rounded-table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }

        /* Encabezados de la tabla sin relleno de color */
        .rounded-table thead th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-right: 1px solid #dddddd;
            border-bottom: 1px solid #777777;
        }

        .rounded-table thead th:last-child {
            border-right: none;
        }

        /* Celdas del cuerpo de la tabla */
        .rounded-table tbody td {
            padding: 8px 10px;
            border-right: 1px solid #dddddd;
            border-bottom: 1px solid #dddddd;
        }

        .rounded-table tbody td:last-child {
            border-right: none;
        }

        /* Filas alternas */
        .rounded-table tbody tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        .rounded-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .rounded-table tbody tr:last-child td {
            border-bottom: none;
        }

        .text-center {
            text-align: center;
        }

        .fecha-col {
            width: 12%;
        }

        .hora-col {
            width: 10%;
        }

        .medicacion-col {
            width: 38%;
        }

        .observaciones-col {
            width: 40%;
        }

        .rnc {
            font-size: 9px;
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        /* Estilos para filas alternadas */
        .records-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img class="img-logo" src="{{ public_path('images/clinicLogo.jpg') }}" width="50" height="70">
                {{-- <img class="img-logo" src="{{ asset('images/clinicLogo.jpg') }}" width="50" height="70"> --}}

                <div style="font-weight: bold; text-align: center; color: #000; font-size: 14px;">IEMMN</div>
            </div>
            <div class="hospital-info">
                <p class="hospital-name">{{ $clinic->name }}</p>
                <p style="margin: 0">{{ $clinic->address }}<br>
                    Tel.: {{ $clinic->phone }} • Fax: {{ $clinic->fax }}<br>
                    <span class="rnc">RNC: {{ $clinic->rnc }}</span>
                </p>
            </div>
            <div class="title">RECORD DIARIO DE LA ENFERMERÍA</div>

            <div class="patient-info">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 75%; vertical-align: top;">
                            <!-- Tabla de información del paciente -->
                            <table style="width: 100%">
                                <tr>
                                    <td><span class="bold">Paciente:</span>
                                        {{ $nurseRecord->admission->patient->first_name }}
                                        {{ $nurseRecord->admission->patient->first_surname }}
                                        {{ $nurseRecord->admission->patient->second_surname }}</td>
                                    <td><span class="bold"> Fecha: </span>
                                        {{ $nurseRecord->created_at->format('d/m/Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><span class="bold">Enfermera:</span> {{ $nurseRecord->nurse->name }}
                                        {{ $nurseRecord->nurse->last_name }}</td>
                                    <td><span class="bold">Sala:</span> {{ $nurseRecord->admission->bed->room }}
                                        <span class="bold">Cama No:</span>
                                        {{ $nurseRecord->admission->bed->number }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 25%; vertical-align: top; text-align: center;">
                            <!-- Firma de la enfermera -->
                            @if ($nurseSignaturePath)
                                <img src="{{ $nurseSignaturePath }}" width="80"
                                    style="display: block; margin: 0 auto 5px auto;">
                            @else
                                <div style="border-bottom: 1px solid #000; height: 30px; margin-bottom: 5px;"></div>
                            @endif
                            {{-- <div style="font-size: 11px;">Firma</div> --}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="table-container">
            <table class="rounded-table">
                <thead>
                    <tr>
                        <th class="fecha-col">Fecha</th>
                        <th class="hora-col">Hora</th>
                        <th class="medicacion-col">Medicación</th>
                        <th class="observaciones-col">Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details as $event)
                        <tr>
                            <td class="text-center">{{ $event->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">{{ $event->created_at->format('h:i A') }}</td>
                            <td>{{ $event->medication }}</td>
                            <td>{{ $event->comment }}</td>
                        </tr>
                    @endforeach

                    <!-- Agregar filas vacías si hay pocos registros para mantener la estructura -->
                    @if (count($details) < 5)
                        @for ($i = 0; $i < 10 - count($details); $i++)
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor
                    @endif
                </tbody>
            </table>

        </div>
    </div>
</body>

</html>
