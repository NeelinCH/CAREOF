@component('mail::message')
# 🌱 Recordatorio de Cuidado de Plantas

Hola {{ $tarea->planta->user->name }},

Tienes una tarea pendiente para tu planta **{{ $tarea->planta->nombre }}** ({{ $tarea->planta->especie }}).

## 📋 Detalles de la tarea:
- **Tipo:** {{ ucfirst($tarea->tipo) }}
- **Fecha programada:** {{ $tarea->proxima_fecha->format('d/m/Y') }}
- **Frecuencia:** Cada {{ $tarea->frecuencia_dias }} días
@if($tarea->descripcion)
- **Descripción:** {{ $tarea->descripcion }}
@endif

@component('mail::button', ['url' => route('plantas.show', $tarea->planta->id), 'color' => 'success'])
📊 Ver Detalles de la Planta
@endcomponent

¡Gracias por cuidar de tus plantas! 🌿

Saludos,  
**Equipo CARE**  
rosinethesis2018@gmail.com

@component('mail::subcopy')
¿Necesitas ayuda? Responde a este email y te asistiremos.
@endcomponent
@endcomponent