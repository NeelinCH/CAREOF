<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Tarea') }} - {{ $planta->nombre }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('plantas.tareas.update', [$planta->id, $tarea->id]) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tipo de Tarea -->
                            <div>
                                <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo de tarea *</label>
                                <select id="tipo" name="tipo" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    <option value="riego" {{ old('tipo', $tarea->tipo) == 'riego' ? 'selected' : '' }}>Riego</option>
                                    <option value="fertilizacion" {{ old('tipo', $tarea->tipo) == 'fertilizacion' ? 'selected' : '' }}>Fertilización</option>
                                    <option value="poda" {{ old('tipo', $tarea->tipo) == 'poda' ? 'selected' : '' }}>Poda</option>
                                    <option value="trasplante" {{ old('tipo', $tarea->tipo) == 'trasplante' ? 'selected' : '' }}>Trasplante</option>
                                    <option value="otro" {{ old('tipo', $tarea->tipo) == 'otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('tipo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Frecuencia -->
                            <div>
                                <label for="frecuencia_dias" class="block text-sm font-medium text-gray-700">Frecuencia (días) *</label>
                                <input type="number" id="frecuencia_dias" name="frecuencia_dias" min="1" value="{{ old('frecuencia_dias', $tarea->frecuencia_dias) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                @error('frecuencia_dias')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Próxima Fecha -->
                            <div>
                                <label for="proxima_fecha" class="block text-sm font-medium text-gray-700">Próxima fecha *</label>
                                <input type="date" id="proxima_fecha" name="proxima_fecha" value="{{ old('proxima_fecha', $tarea->proxima_fecha->format('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                @error('proxima_fecha')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Estado -->
                            <div>
                                <label for="activa" class="block text-sm font-medium text-gray-700">Estado *</label>
                                <select id="activa" name="activa" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    <option value="1" {{ old('activa', $tarea->activa) ? 'selected' : '' }}>Activa</option>
                                    <option value="0" {{ !old('activa', $tarea->activa) ? 'selected' : '' }}>Inactiva</option>
                                </select>
                                @error('activa')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Descripción -->
                            <div class="md:col-span-2">
                                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción (opcional)</label>
                                <textarea id="descripcion" name="descripcion" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50">{{ old('descripcion', $tarea->descripcion) }}</textarea>
                                @error('descripcion')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <a href="{{ route('plantas.tareas.index', $planta->id) }}" class="mr-4 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i> Actualizar Tarea
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>