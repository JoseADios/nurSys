<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha control de medicamentos</title>
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

        .container-notifications {

            border-radius: 10px;
            padding: 10px;

            page-break-after: always;
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
            <div class="title">FICHA CONTROL DE MEDICAMENTOS</div>

            <div class="patient-info">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 75%; vertical-align: top;">
                            <!-- Tabla de información del paciente -->
                            <table style="width: 100%">
                                <tr>
                                    <td><span class="bold">Nombre:</span>
                                        {{ $medicationRecord->admission->patient->first_name }}
                                        {{ $medicationRecord->admission->patient->first_surname }}
                                        {{ $medicationRecord->admission->patient->second_surname }}</td>
                                        @if ($medicationRecord->admission->bed)
                                                <td><span class="bold"> Hab.: </span>
                                        {{ $medicationRecord->admission->bed->room }}</td>
                                    <td><span class="bold"> Cama: </span>
                                        {{ $medicationRecord->admission->bed->number }}</td>
                                        @endif


                                </tr>
                                <tr>
                                    <td><span class="bold">Diagnostico:</span> {{ $medicationRecord->admission->admission_dx }}
                                        {{ $medicationRecord->admission->doctor->last_name }}</td>
                                    <td><span class="bold">Dieta:</span> {{ $medicationRecord->diet }}
                                    </td>
                                </tr>
                            </table>
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
                        <th class="medicacion-col">Medicamentos</th>
                        <th class="hora-col">Dosis</th>
                        <th class="hora-col">Via</th>
                        <th class="hora-col">FC</th>
                        <th class="hora-col">Horas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details as $event)
                        <tr>
                            <td class="text-center">{{ $event->created_at->format('d/m/Y') }}</td>
                            <td>{{ $event->drug }}</td>
                            <td> {{ $event->dose }}</td>
                            <td>{{ $event->route }}</td>
                            <td>{{ $event->fc }} </td>
                            <td class="text-center">{{ $event->interval_in_hours }}</td>
                        </tr>
                    @endforeach

                    <!-- Agregar filas vacías si hay pocos registros para mantener la estructura -->
                    @if (count($details) < 5)
                        @for ($i = 0; $i < 15 - count($details); $i++)
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
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

@if ($hasnotifications)


    <div class="container-notifications"></div>
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
        <div class="title">NOTIFICACION DE MEDICAMENTOS SUMINISTRADO A PACIENTE</div>

        <div class="patient-info">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 75%; vertical-align: top;">
                        <!-- Tabla de información del paciente -->
                        <table style="width: 100%">
                            <tr>
                                <td><span class="bold">Nombre:</span>
                                    {{ $medicationRecord->admission->patient->first_name }}
                                    {{ $medicationRecord->admission->patient->first_surname }}
                                    {{ $medicationRecord->admission->patient->second_surname }}</td>



                            </tr>
                            <tr>
                                 @if ($medicationRecord->admission->bed)
                                <td><span class="bold"> Hab.: </span>
                                    {{ $medicationRecord->admission->bed->room }}</td>
                                    @endif
                            </tr>
                        </table>
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
                    <th class="medicacion-col">Medicamentos</th>
                    <th class="hora-col">Hora</th>
                    <th class="observaciones-col">Enfermero/a</th>
                    <th class="hora-col">Firma</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details as $event)
                    @foreach ($event->medicationNotification as $notifications)
                        @if ($notifications->applied == true)
                            <tr>
                                <td>{{ $notifications->created_at->format('d/m/Y') }}</td>
                                <td>{{ $event->drug }}</td>
                                <td>{{ date('h:i:s', strtotime($notifications->administered_time)) }}</td>
                                <td>{{ $notifications->nurse->name }} </td>
                                <td>
                                    @if (!empty($notifications->nurse_sign))
                                        <img src="{{ public_path('storage/signatures/' . basename($notifications->nurse_sign)) }}"
                                            width="100">
                                    @else
                                        <p>No hay firmas disponibles.</p>
                                    @endif
                                </td>

                            </tr>
                        @endif
                    @endforeach
                @endforeach
                <!-- Agregar filas vacías si hay pocos registros para mantener la estructura -->
                @if (count($details) < 5)
                    @for ($i = 0; $i < 15 - count($details); $i++)
                        <tr>
                            <td>&nbsp;</td>
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
@endif
    </div>









</body>

</html>
