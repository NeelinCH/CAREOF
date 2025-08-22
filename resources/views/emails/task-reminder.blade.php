@component('mail::message')
# Recordatorio de Tarea

Tienes una tarea pendiente para tu planta **{{ $tarea->planta->nombre }}** ({{ $tarea->planta->especie }}).

**Tipo de tarea:** {{ ucfirst($tarea->tipo) }}  
**Fecha programada:** {{ $tarea->proxima_fecha->format('d/m/Y') }}  
**Frecuencia:** Cada {{ $tarea->frecuencia_dias }} días  
**Descripción:** {{ $tarea->descripcion ?? 'Ninguna' }}

@component('mail::button', ['url' => route('plantas.show', $tarea->planta->id)])
Ver Planta
@endcomponent

Gracias,  
{{ config('app.name') }}
@endcomponent