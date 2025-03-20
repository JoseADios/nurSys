<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoja de Temperatura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        img {
            border-radius: 10px;
        }

        #clinic-ab {
            text-align: center;
            margin: 0px;
        }

        h1 {
            /* text-align: center; */
            font-size: 20px;
            margin-top: 0px;
            margin-bottom: 10px;
        }

        .col-1 {
            text-align: center;
        }

        .col-1>img {
            margin-top: 5px;
        }

        .col-2>div {
            text-align: center;
            width: 65%;
        }

        .col-2>div>* {
            margin: 2px 0px;
        }

        #address,
        #slogan,
        #rnc {
            font-size: smaller
        }

        #slogan {
            font-weight: bold;
            font-style: italic;
        }

        #rnc {
            font-weight: 900
        }

        .record-info {
            font-size: x-small;
            padding: 0px 40px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        .record-info p {
            margin: 2px 0;
        }

        .bold {
            font-weight: bold;
        }

        .graph-cont {
            margin-top: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="header">

        <table width="100%">
            <tr>
                <td class="col-1" width="10%">
                    <img src="{{ public_path('images/clinicLogo.jpg') }}" width="50" height="60">
                    {{-- <img src="{{ asset('images/clinicLogo.jpg') }}" width="50" height="70"> --}}
                    <h3 id="clinic-ab">IEMMN</h3>

                </td>
                <td width="90%" class="col-2">
                    <div style="margin-left: 10%">
                        <h1>{{ $clinic->name }}</h1>
                        <p id="address">{{ $clinic->address }} • Tel.: {{ $clinic->phone }} • Fax.:
                            {{ $clinic->fax }}</p>
                        <p id="slogan">"{{ $clinic->slogan }}"</p>
                        <p id="rnc">RNC {{ $clinic->rnc }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>


    <table width="100%" class="record-info">
        <tr>
            <td style="width: 70%; vertical-align: top;">
                <p><span class="bold">Paciente:</span> {{ $temperatureRecord->admission->patient->first_name }}
                    {{ $temperatureRecord->admission->patient->first_surname }}
                    {{ $temperatureRecord->admission->patient->second_surname }}</p>
                <p><span class="bold">Dirección:</span> {{ $temperatureRecord->admission->patient->address }}</p>
                <p><span class="bold">Diagnóstico de impresión:</span>
                    {{ $temperatureRecord->admission->admission_dx }}</p>
            </td>
            <td style="width: 30%; vertical-align: top;">
                <p><span class="bold">Sala:</span> {{ $temperatureRecord->admission->bed->room }}</p>
                <p><span class="bold">Cama no.:</span> {{ $temperatureRecord->admission->bed->number }}</p>
                <p><span class="bold">Enfermera:</span> {{ $temperatureRecord->nurse->name }}</p>
            </td>
        </tr>
    </table>


    {{-- <h3>Gráfico de Temperatura</h3> --}}
    <div class="graph-cont">
        {{-- <img src="{{ $graphPath }}" width="100%"> --}}
        <img src="{{ public_path('storage/temp_chart.jpg') }}" width="100%">
    </div>

    <div class="bottom-cont">
        <table width="100%" style="border-collapse: collapse;">
            <tr>
                <td style="width: 25%; vertical-align: middle; text-align: left; padding: 2px 0;">
                    <div><span class="bold">Dr.</span> {{ $temperatureRecord->admission->doctor->name }}
                        {{ $temperatureRecord->admission->doctor->last_name }}</div>
                </td>
                <td style="width: 30%; vertical-align: middle; text-align: left; padding: 2px 0;">
                    <div style="display: flex; align-items: center;">
                        <span class="bold">Firma enf.</span>

                        @if ($nurseSignaturePath)
                            <img src="{{ $nurseSignaturePath }}" width="100" style="margin-left: 10px;">
                        @endif
                    </div>
                </td>
                <td style="width: 45%; vertical-align: middle; padding: 2px 0 2px 100px;">
                    <p>Generado a las {{now()->locale('es')->isoFormat('h:mm A [del] d [de] MMMM [de] YYYY')}} </p>
                </td>
            </tr>
        </table>
    </div>



</body>

</html>
