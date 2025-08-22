<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Planta') }}: {{ $planta->nombre }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('plantas.update', $planta->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre -->
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la planta</label>
                                <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $planta->nombre) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                @error('nombre')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Especie -->
                            <div>
                                <label for="especie" class="block text-sm font-medium text-gray-700">Especie</label>
                                <input type="text" id="especie" name="especie" value="{{ old('especie', $planta->especie) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                @error('especie')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Fecha Adquisición -->
                            <div>
                                <label for="fecha_adquisicion" class="block text-sm font-medium text-gray-700">Fecha de adquisición</label>
                                <input type="date" id="fecha_adquisicion" name="fecha_adquisicion" value="{{ old('fecha_adquisicion', $planta->fecha_adquisicion->format('Y-m-d')) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                @error('fecha_adquisicion')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Ubicación -->
                            <div>
                                <label for="ubicacion" class="block text-sm font-medium text-gray-700">Ubicación</label>
                                <input type="text" id="ubicacion" name="ubicacion" value="{{ old('ubicacion', $planta->ubicacion) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                @error('ubicacion')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Imagen Actual -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Imagen Actual</label>
                                @if($planta->imagen)
                                    <div class="mt-1">
                                        <img src="{{ asset('storage/' . $planta->imagen) }}" alt="{{ $planta->nombre }}" class="h-32 w-auto rounded-md">
                                        <div class="mt-2 flex items-center">
                                            <input type="checkbox" id="eliminar_imagen" name="eliminar_imagen" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                            <label for="eliminar_imagen" class="ml-2 text-sm text-gray-600">Eliminar imagen actual</label>
                                        </div>
                                    </div>
                                @else
                                    <p class="mt-1 text-sm text-gray-500">No hay imagen cargada</p>
                                @endif
                            </div>

                            <!-- Nueva Imagen -->
                            <div class="md:col-span-2">
                                <label for="imagen" class="block text-sm font-medium text-gray-700">Nueva Imagen (opcional)</label>
                                <input type="file" id="imagen" name="imagen" accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                                @error('imagen')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <a href="{{ route('plantas.index') }}" class="mr-4 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i> Actualizar Planta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>