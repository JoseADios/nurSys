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
            margin: 20px;
        }

        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .section {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .section p {
            margin: 5px 0;
        }

        .bold {
            font-weight: bold;
        }

        .image-container {
            text-align: center;
            margin-top: 15px;
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
    </style>
</head>

<body>
    <h1>Hoja de Temperatura</h1>

    <div class="section">
        <p><span class="bold">Paciente:</span> {{ $temperatureRecord->admission->patient->first_name }}
            {{ $temperatureRecord->admission->patient->last_name }}
            {{ $temperatureRecord->admission->patient->first_surname }}
            {{ $temperatureRecord->admission->patient->second_surname }}</p>
        <p><span class="bold">Dirección:</span> {{ $temperatureRecord->admission->patient->address }}</p>
        <p><span class="bold">Diagnóstico de impresión:</span> {{ $temperatureRecord->impression_diagnosis }}</p>
        <p><span class="bold">Sala:</span> {{ $temperatureRecord->admission->bed->room }}</p>
        <p><span class="bold">Cama no.:</span> {{ $temperatureRecord->admission->bed->number }}</p>
        <p><span class="bold">Enfermera:</span> {{ $temperatureRecord->nurse->name }}
            {{ $temperatureRecord->nurse->last_name }}</p>
    </div>


    <h2>Detalles de Temperatura</h2>
    <table class="temperature-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Temperatura (°C)</th>
                <th>Evacuaciones</th>
                <th>Micciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($details as $detail)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($detail->created_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $detail->temperature }}</td>
                    <td>{{ $detail->evacuations }}</td>
                    <td>{{ $detail->urinations }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
