@extends('errors.layout')

@php
    $espera = (int) (($exception ?? null)?->getHeaders()['Retry-After'] ?? 0);
@endphp

@section('codigo', '429')
@section('titulo', 'Demasiados intentos seguidos')
@section('mensaje', 'Hiciste varias consultas en poco tiempo. Por seguridad, espera un momento antes de volver a intentarlo.')

@if($espera > 0)
    @section('extra')
        Puedes intentarlo de nuevo en <strong>{{ $espera >= 60 ? ceil($espera / 60) . ' ' . ($espera > 60 ? 'minutos' : 'minuto') : $espera . ' segundos' }}</strong>.
    @endsection
@endif
