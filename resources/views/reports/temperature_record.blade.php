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
            margin-bottom: 10px;
        }

        .col-1 {
            text-align: center;
        }

        .col-2>div {
            text-align: center;
            width: 60%;
        }

        .col-2>div>* {
            margin: 5px 0px;
        }

        #slogan {
            font-weight: bold;
            font-style: italic;
        }

        #rnc {
            font-weight: bolder
        }

        .section {
            margin-bottom: 15px;
            padding: 5px 40px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        .section p {
            margin: 5px 0;
        }

        .bold {
            font-weight: bold;
        }

        .temperature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .temperature-table th,
        .temperature-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }

        .temperature-table th {
            background-color: #f3f3f3;
        }

        .chart {
            display: flex;
            align-items: flex-end;
            height: 200px;
            width: 400px;
            border-left: 2px solid #000;
            border-bottom: 2px solid #000;
            margin: 20px;
        }

        .bar {
            flex: 1;
            /* background-color: #007bff; */
            margin: 0 2px;
            position: relative;
        }

        .bar::after {
            content: attr(data-value);
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="header">

        <table width="100%">
            <tr>
                <td class="col-1" width="10%">
                    {{-- <img src="{{ public_path('images/clinicLogo.jpg') }}" width="60" height="80"> --}}
                    <img src="{{ asset('images/clinicLogo.jpg') }}" width="60" height="80">
                    <h3 id="clinic-ab">IEMMN</h3>

                </td>
                <td width="90%" class="col-2">
                    <div>
                        <h1>{{ $clinic->name }}</h1>
                        <p>{{ $clinic->address }} • Tel.: {{ $clinic->phone }} • Fax.: {{ $clinic->fax }}</p>
                        <p id="slogan">"{{ $clinic->slogan }}"</p>
                        <p id="rnc">RNC {{ $clinic->rnc }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>


    {{-- <div class="section"> --}}
    <table width="100%">
        <tr>
            <td style="width: 70%; vertical-align: top;">
                <p><span class="bold">Paciente:</span> {{ $temperatureRecord->admission->patient->first_name }}
                    {{ $temperatureRecord->admission->patient->last_name }}
                    {{ $temperatureRecord->admission->patient->first_surname }}
                    {{ $temperatureRecord->admission->patient->second_surname }}</p>
                <p><span class="bold">Dirección:</span> {{ $temperatureRecord->admission->patient->address }}</p>
                <p><span class="bold">Diagnóstico de impresión:</span>
                    {{ $temperatureRecord->impression_diagnosis }}</p>
            </td>
            <td style="width: 30%; vertical-align: top;">
                <p><span class="bold">Sala:</span> {{ $temperatureRecord->admission->bed->room }}</p>
                <p><span class="bold">Cama no.:</span> {{ $temperatureRecord->admission->bed->number }}</p>
                <p><span class="bold">Enfermera:</span> {{ $temperatureRecord->nurse->name }}</p>
            </td>
        </tr>
    </table>


    <h3>Gráfico de Temperatura</h3>
    <img src="{{ $graphPath }}" width="90%">

</body>

</html>
