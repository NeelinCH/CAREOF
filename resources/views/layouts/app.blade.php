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
        .modal-backdrop {
            transition: opacity 0.3s ease;
        }
        .arduino-status-connected {
            background-color: #10B981 !important;
            border-color: #059669 !important;
        }
        .arduino-status-disconnected {
            background-color: #EF4444 !important;
            border-color: #DC2626 !important;
        }
        .arduino-status-testing {
            background-color: #F59E0B !important;
            border-color: #D97706 !important;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
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
        <main class="container mx-auto py-6 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            {{ $slot }}
        </main>
    </div>

    <!-- Modal de Configuración Arduino -->
    <div id="arduinoConfigModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-semibold">
                    <i class="fas fa-microchip mr-2 text-blue-500"></i>
                    Configuración Arduino
                </h4>
                <button onclick="closeArduinoConfig()" class="text-gray-500 hover:text-gray-700">
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
                            <i class="fas fa-sync-alt mr-1"></i> Probar Conexión
                        </button>
                    </div>
                </div>

                <!-- Puerto COM -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Puerto COM *</label>
                    <select id="arduinoPort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="COM9">COM9</option>
                        <option value="COM10">COM10</option>
                        <option value="COM3">COM3</option>
                        <option value="COM4">COM4</option>
                        <option value="COM5">COM5</option>
                        <option value="COM6">COM6</option>
                        <option value="COM7">COM7</option>
                        <option value="COM8">COM8</option>
                        <option value="/dev/ttyUSB0">/dev/ttyUSB0 (Linux)</option>
                        <option value="/dev/ttyACM0">/dev/ttyACM0 (Linux)</option>
                    </select>
                </div>

                <!-- Tiempo de Riego -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tiempo de Riego (ms) *</label>
                    <input type="number" id="arduinoTime" min="100" max="10000" step="100" value="2000"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <p class="text-xs text-gray-500 mt-1">1000 ms = 1 segundo</p>
                </div>

                <!-- Cantidad de Agua -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cantidad de Agua (ml) *</label>
                    <input type="number" id="arduinoWater" min="1" max="5000" value="500"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>

                <!-- Velocidad (Baud Rate) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Velocidad (Baud Rate)</label>
                    <select id="arduinoBaudRate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="9600">9600</option>
                        <option value="19200">19200</option>
                        <option value="38400">38400</option>
                        <option value="57600">57600</option>
                        <option value="115200">115200</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeArduinoConfig()" 
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition duration-200">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </button>
                <button onclick="saveArduinoConfig()" 
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200">
                    <i class="fas fa-save mr-1"></i> Guardar Configuración
                </button>
            </div>
        </div>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- Scripts adicionales -->
    @stack('scripts')

    <!-- Notificaciones Toast -->
    <div id="notifications" class="fixed top-4 right-4 z-50 space-y-2" style="display: none;">
        <!-- Las notificaciones se insertarán aquí dinámicamente -->
    </div>

    <script>
    // ==================== CONFIGURACIÓN ARDUINO ====================
    let arduinoConfig = {
        port: 'COM9',
        time: 2000,
        water: 500,
        baudRate: 9600,
        connected: false
    };

    // Cargar configuración guardada al iniciar
    document.addEventListener('DOMContentLoaded', function() {
        loadArduinoConfig();
        checkArduinoConnection();
    });

    // Función para abrir el modal de configuración
    function openArduinoConfig() {
        document.getElementById('arduinoConfigModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        loadArduinoConfigToForm();
    }

    // Función para cerrar el modal de configuración
    function closeArduinoConfig() {
        document.getElementById('arduinoConfigModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        
        // Redirigir de vuelta a la página de la tarea si estamos en una configuración específica
        const urlParams = new URLSearchParams(window.location.search);
        const returnUrl = urlParams.get('return_url');
        if (returnUrl) {
            window.location.href = returnUrl;
        }
    }

    // Cargar configuración guardada en el formulario
    function loadArduinoConfigToForm() {
        document.getElementById('arduinoPort').value = arduinoConfig.port;
        document.getElementById('arduinoTime').value = arduinoConfig.time;
        document.getElementById('arduinoWater').value = arduinoConfig.water;
        document.getElementById('arduinoBaudRate').value = arduinoConfig.baudRate;
    }

    // Cargar configuración desde localStorage
    function loadArduinoConfig() {
        const savedConfig = localStorage.getItem('arduinoConfig');
        if (savedConfig) {
            arduinoConfig = { ...arduinoConfig, ...JSON.parse(savedConfig) };
        }
        updateConnectionStatus();
    }

    // Guardar configuración
    function saveArduinoConfig() {
        const newConfig = {
            port: document.getElementById('arduinoPort').value,
            time: parseInt(document.getElementById('arduinoTime').value),
            water: parseInt(document.getElementById('arduinoWater').value),
            baudRate: parseInt(document.getElementById('arduinoBaudRate').value)
        };

        // Validaciones
        if (newConfig.time < 100 || newConfig.time > 10000) {
            showNotification('El tiempo de riego debe estar entre 100 y 10000 ms', 'error');
            return;
        }

        if (newConfig.water < 1 || newConfig.water > 5000) {
            showNotification('La cantidad de agua debe estar entre 1 y 5000 ml', 'error');
            return;
        }

        arduinoConfig = { ...arduinoConfig, ...newConfig };
        localStorage.setItem('arduinoConfig', JSON.stringify(arduinoConfig));
        
        showNotification('Configuración de Arduino guardada correctamente', 'success');
        
        // Cerrar el modal después de guardar
        setTimeout(() => {
            closeArduinoConfig();
        }, 1500);
    }

    // Probar conexión con Arduino
    async function testArduinoConnection() {
        const testBtn = document.getElementById('testConnectionBtn');
        const statusDiv = document.getElementById('arduinoConnectionStatus');
        const statusText = document.getElementById('arduinoStatusText');
        
        testBtn.disabled = true;
        testBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Probando...';
        statusDiv.className = 'p-3 rounded-lg border arduino-status-testing';
        statusText.innerHTML = 'Estado: Probando conexión...';

        try {
            // Simular prueba de conexión (en producción sería una llamada real)
            const success = await simulateConnectionTest();
            
            if (success) {
                arduinoConfig.connected = true;
                statusDiv.className = 'p-3 rounded-lg border arduino-status-connected';
                statusText.innerHTML = 'Estado: Conectado correctamente';
                showNotification('✅ Conexión con Arduino establecida correctamente', 'success');
            } else {
                throw new Error('No se pudo establecer conexión');
            }
        } catch (error) {
            arduinoConfig.connected = false;
            statusDiv.className = 'p-3 rounded-lg border arduino-status-disconnected';
            statusText.innerHTML = 'Estado: Error de conexión';
            showNotification('❌ Error al conectar con Arduino: ' + error.message, 'error');
        } finally {
            testBtn.disabled = false;
            testBtn.innerHTML = '<i class="fas fa-sync-alt mr-1"></i> Probar Conexión';
            localStorage.setItem('arduinoConfig', JSON.stringify(arduinoConfig));
        }
    }

    // Simular prueba de conexión (reemplazar con llamada real a tu API)
    async function simulateConnectionTest() {
        return new Promise((resolve) => {
            setTimeout(() => {
                // Simular éxito en el 80% de los casos para pruebas
                const success = Math.random() > 0.2;
                resolve(success);
            }, 2000);
        });
    }

    // Verificar estado de conexión al cargar
    function checkArduinoConnection() {
        if (arduinoConfig.connected) {
            updateConnectionStatus();
        }
    }

    // Actualizar visualización del estado de conexión
    function updateConnectionStatus() {
        const statusDiv = document.getElementById('arduinoConnectionStatus');
        const statusText = document.getElementById('arduinoStatusText');
        
        if (statusDiv && statusText) {
            if (arduinoConfig.connected) {
                statusDiv.className = 'p-3 rounded-lg border arduino-status-connected';
                statusText.innerHTML = `Estado: Conectado (${arduinoConfig.port})`;
            } else {
                statusDiv.className = 'p-3 rounded-lg border arduino-status-disconnected';
                statusText.innerHTML = 'Estado: Desconectado';
            }
        }
    }

    // ==================== NOTIFICACIONES ====================
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `p-4 rounded-lg shadow-lg border-l-4 transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-100 border-green-400 text-green-700' :
            type === 'error' ? 'bg-red-100 border-red-400 text-red-700' :
            'bg-blue-100 border-blue-400 text-blue-700'
        } animate-slide-in`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${
                    type === 'success' ? 'fa-check-circle' :
                    type === 'error' ? 'fa-exclamation-triangle' :
                    'fa-info-circle'
                } mr-2"></i>
                <span class="flex-1">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" 
                        class="ml-4 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        const container = document.getElementById('notifications');
        container.style.display = 'block';
        container.appendChild(notification);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }
            if (container.children.length === 0) {
                container.style.display = 'none';
            }
        }, 5000);
    }

    // Mostrar notificaciones de sesión de Laravel
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif
    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif
    @if($errors->any())
        @foreach($errors->all() as $error)
            showNotification('{{ $error }}', 'error');
        @endforeach
    @endif

    // ==================== FUNCIONES GLOBALES ====================
    // Hacer funciones disponibles globalmente
    window.openArduinoConfig = openArduinoConfig;
    window.closeArduinoConfig = closeArduinoConfig;
    window.testArduinoConnection = testArduinoConnection;
    window.saveArduinoConfig = saveArduinoConfig;
    window.showNotification = showNotification;
    </script>

    <style>
        @keyframes slide-in {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
        .hidden {
            display: none !important;
        }
    </style>
</body>
</html>