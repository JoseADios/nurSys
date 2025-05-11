@extends('errors::minimal')

@section('title', __('Acceso prohibido'))
@section('code', '403')
@section('message')
    @php
        $message = __($exception->getMessage() ?: 'Forbidden');

        // Traducciones personalizadas para mensajes comunes en inglés
        $translations = [
            'Forbidden' => 'Acceso prohibido',
            'User does not have the right roles.' => 'El usuario no tiene los roles necesarios.',
            'User does not have the right permissions.' => 'El usuario no tiene los permisos necesarios.',
            'This action is unauthorized.' => 'Esta acción no está autorizada.',
            // Añade aquí más traducciones según necesites
        ];

        // Si el mensaje está en nuestro array de traducciones, lo sustituimos
        if (array_key_exists($message, $translations)) {
            $message = $translations[$message];
        }
    @endphp
    {{ $message }}
@endsection
