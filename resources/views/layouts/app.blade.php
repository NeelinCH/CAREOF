<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CARE Project') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=Nunito">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <style>
        /* Estilos mínimos para el modal */
        .arduino-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
        }
        
        .arduino-modal-content {
            position: relative;
            background: white;
            margin: 2rem auto;
            padding: 1.5rem;
            border-radius: 0.5rem;
            max-width: 28rem;
            width: 90%;
        }
        
        .arduino-status-connected { background-color: #10B981; border-color: #059669; }
        .arduino-status-disconnected { background-color: #EF4444; border-color: #DC2626; }
        .arduino-status-testing { background-color: #F59E0B; border-color: #D97706; }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- NAVEGACIÓN ORIGINAL DE BREEZE - NO MODIFICAR -->
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- MODAL ARDUINO - FUERA DEL FLUJO PRINCIPAL -->
    <div id="arduinoConfigModal" class="arduino-modal">
        <div class="arduino-modal-content">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-semibold">
                    <i class="fas fa-microchip mr-2 text-blue-500"></i>
                    Configuración Arduino
                </h4>
                <button onclick="closeArduinoConfig()" class="text-gray-500 hover:text-gray-700 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <!-- Estado de Conexión -->
                <div id="arduinoConnectionStatus" class="p-3 rounded-lg border arduino-status-disconnected">
                    <div class="flex items-center">
                        <i class="fas fa-plug mr-2"></i>
                        <span id="arduinoStatusText">Estado: Desconectado</span>
                        <button id="testConnectionBtn" onclick="testArduinoConnection()" 
                                class="ml-auto bg-white text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-100">
                            <i class="fas fa-sync-alt mr-1"></i> Probar
                        </button>
                    </div>
                </div>

                <!-- Puerto COM -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Puerto COM</label>
                    <select id="arduinoPort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="COM10">COM10</option>
                        <option value="COM9">COM9</option>
                        <option value="COM3">COM3</option>
                        <option value="COM4">COM4</option>
                        <option value="COM5">COM5</option>
                    </select>
                </div>

                <!-- Tiempo de Riego -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tiempo de Riego (ms)</label>
                    <input type="number" id="arduinoTime" min="100" max="10000" step="100" value="2000"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <!-- Cantidad de Agua -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cantidad de Agua (ml)</label>
                    <input type="number" id="arduinoWater" min="1" max="5000" value="500"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeArduinoConfig()" 
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Cancelar
                </button>
                <button onclick="saveArduinoConfig()" 
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Guardar
                </button>
            </div>
        </div>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- Scripts adicionales -->
    @stack('scripts')

    <!-- Notificaciones Toast -->
    <div id="notifications" class="fixed top-4 right-4 z-50 space-y-2" style="display: none;"></div>

    <script>
    // ==================== CONFIGURACIÓN ARDUINO ====================
    let arduinoConfig = {
        port: 'COM9',
        time: 2000,
        water: 500,
        connected: false
    };

    // Cargar configuración al iniciar
    document.addEventListener('DOMContentLoaded', function() {
        loadArduinoConfig();
    });

    // Función para abrir el modal
    function openArduinoConfig() {
        document.getElementById('arduinoConfigModal').style.display = 'flex';
        loadArduinoConfigToForm();
    }

    // Función para cerrar el modal
    function closeArduinoConfig() {
        document.getElementById('arduinoConfigModal').style.display = 'none';
    }

    // Cargar configuración en el formulario
    function loadArduinoConfigToForm() {
        document.getElementById('arduinoPort').value = arduinoConfig.port;
        document.getElementById('arduinoTime').value = arduinoConfig.time;
        document.getElementById('arduinoWater').value = arduinoConfig.water;
    }

    // Cargar configuración guardada
    function loadArduinoConfig() {
        const saved = localStorage.getItem('arduinoConfig');
        if (saved) {
            arduinoConfig = JSON.parse(saved);
        }
    }

    // Guardar configuración
    function saveArduinoConfig() {
        arduinoConfig = {
            port: document.getElementById('arduinoPort').value,
            time: parseInt(document.getElementById('arduinoTime').value),
            water: parseInt(document.getElementById('arduinoWater').value),
            connected: arduinoConfig.connected
        };
        
        localStorage.setItem('arduinoConfig', JSON.stringify(arduinoConfig));
        showNotification('Configuración guardada correctamente', 'success');
        
        setTimeout(closeArduinoConfig, 1000);
    }

    // Probar conexión
    async function testArduinoConnection() {
        const btn = document.getElementById('testConnectionBtn');
        const status = document.getElementById('arduinoConnectionStatus');
        const text = document.getElementById('arduinoStatusText');
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Probando...';
        status.className = 'p-3 rounded-lg border arduino-status-testing';
        text.innerHTML = 'Estado: Probando...';

        try {
            await new Promise(resolve => setTimeout(resolve, 2000));
            const success = Math.random() > 0.2;
            
            if (success) {
                status.className = 'p-3 rounded-lg border arduino-status-connected';
                text.innerHTML = 'Estado: Conectado';
                showNotification('Conexión exitosa con Arduino', 'success');
            } else {
                throw new Error('No se pudo conectar');
            }
        } catch (error) {
            status.className = 'p-3 rounded-lg border arduino-status-disconnected';
            text.innerHTML = 'Estado: Error de conexión';
            showNotification('Error al conectar: ' + error.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sync-alt mr-1"></i> Probar';
        }
    }

    // Función de notificaciones
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `p-4 rounded-lg shadow-lg border-l-4 ${
            type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        const container = document.getElementById('notifications');
        container.style.display = 'block';
        container.appendChild(notification);
        
        setTimeout(() => notification.remove(), 5000);
    }

    // Hacer funciones globales
    window.openArduinoConfig = openArduinoConfig;
    window.closeArduinoConfig = closeArduinoConfig;
    window.testArduinoConnection = testArduinoConnection;
    window.saveArduinoConfig = saveArduinoConfig;
    </script>
</body>
</html>