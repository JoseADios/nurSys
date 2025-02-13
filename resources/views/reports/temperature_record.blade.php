<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoja de Temperatura</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Instituto de Especialidades Médicas Monseñor Nouel</h2>
        <p><strong>Paciente:</strong> {{ $temperatureRecord->admission->patient->first_name }} {{ $temperatureRecord->admission->patient->last_name }}</p>
        <p><strong>Diagnóstico de impresión:</strong> {{ $temperatureRecord->impression_diagnosis }}</p>
    </div>

    <h3>Gráfico de Temperatura</h3>
    <img src="{{ $graphPath }}" width="90%">

</body>
</html>
