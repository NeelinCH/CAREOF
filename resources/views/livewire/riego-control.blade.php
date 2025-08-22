<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold mb-4">Control de Riego Arduino</h3>
    
    @if($mensaje)
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700">{{ $mensaje }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Puerto COM</label>
            <select wire:model="puerto" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <option value="COM9">COM9</option>
                <option value="COM10">COM10</option>
                <option value="COM3">COM3</option>
                <option value="COM4">COM4</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700">Cantidad (ml)</label>
            <input type="number" wire:model="cantidadMl" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
    </div>

    <div class="mb-4">
        <p class="text-sm">
            <span class="font-medium">Estado Arduino:</span>
            <span class="{{ $estadoArduino === 'Disponible' ? 'text-green-600' : 'text-red-600' }}">
                {{ $estadoArduino }}
            </span>
        </p>
    </div>

    <button wire:click="activarRiego" 
            class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center justify-center"
            {{ $estadoArduino !== 'Disponible' ? 'disabled' : '' }}>
        <i class="fas fa-tint mr-2"></i> Activar Riego
    </button>

    @if($mostrarConfirmacion)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full mx-4">
                <h4 class="text-lg font-semibold mb-4">Confirmar Riego</h4>
                <p class="mb-4">¿Estás seguro de activar el riego ahora?</p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('mostrarConfirmacion', false)" class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
                    <button wire:click="confirmarRiego" class="px-4 py-2 bg-blue-500 text-white rounded">Confirmar</button>
                </div>
            </div>
        </div>
    @endif
</div>