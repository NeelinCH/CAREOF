<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CARE Project - Configuración Arduino</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=Nunito">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8fafc;
        }
        .connection-status {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .connected {
            background-color: #10B981;
        }
        .disconnected {
            background-color: #EF4444;
        }
        .checking {
            background-color: #F59E0B;
        }
        .arduino-config-card {
            transition: all 0.3s ease;
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .config-section {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background-color: #3B82F6;
            color: white;
        }
        .btn-primary:hover {
            background-color: #2563EB;
        }
        .btn-success {
            background-color: #10B981;
            color: white;
        }
        .btn-success:hover {
            background-color: #059669;
        }
        .btn-gray {
            background-color: #6B7280;
            color: white;
        }
        .btn-gray:hover {
            background-color: #4B5563;
        }
        .form-select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid #D1D5DB;
            background-color: white;
        }
        .notification {
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 1rem;
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 50;
            display: flex;
            align-items: center;
        }
        .notification-success {
            background-color: #D1FAE5;
            color: #065F46;
            border-left: 4px solid #10B981;
        }
        .notification-error {
            background-color: #FEE2E2;
            color: #991B1B;
            border-left: 4px solid #EF4444;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Simulación de barra de navegación -->
        <nav style="background-color: #1E40AF; color: white; padding: 1rem;">
            <div style="max-width: 7xl; margin: 0 auto; display: flex; justify-content: space-between;">
                <span style="font-weight: bold;">CARE Project</span>
                <a href="{{ url()->previous() }}" style="color: white; text-decoration: none;">
                    <i class="fas fa-arrow-left mr-2"></i> Volver
                </a>
            </div>
        </nav>

        <!-- Page Heading -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Configuración de Arduino
                </h2>
                <a href="{{ url()->previous() }}" class="btn btn-gray">
                    <i class="fas fa-arrow-left mr-2"></i> Volver
                </a>
            </div>
        </header>

        <!-- Page Content -->
        <main class="container mx-auto py-6 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div style="margin-bottom: 1rem; padding: 1rem; background-color: #D1FAE5; border: 1px solid #10B981; color: #065F46; border-radius: 0.375rem;">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="arduino-config-card">
                <div style="padding: 1.5rem; background-color: white; border-bottom: 1px solid #E5E7EB;">
                    <form id="arduinoConfigForm" action="{{ route('arduino.config.save') }}" method="POST">
                        @csrf
                        <input type="hidden" name="previous_url" value="{{ url()->previous() }}">
                        
                        <div class="config-section">
                            <h3 style="font-size: 1.125rem; font-weight: 500; color: #111827; margin-bottom: 1rem; display: flex; align-items: center;">
                                <i class="fas fa-microchip mr-2" style="color: #3B82F6;"></i> Configuración de Puerto Serial
                            </h3>
                            
                            <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                                <div>
                                    <label for="port" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Puerto COM</label>
                                    <select id="port" name="port" class="form-select">
                                        <option value="">Seleccione un puerto</option>
                                        <option value="COM9" {{ old('port', $currentPort ?? '') == 'COM9' ? 'selected' : '' }}>COM9</option>
                                        <option value="COM10" {{ old('port', $currentPort ?? '') == 'COM10' ? 'selected' : '' }}>COM10</option>
                                        <option value="COM1" {{ old('port', $currentPort ?? '') == 'COM1' ? 'selected' : '' }}>COM1</option>
                                        <option value="COM2" {{ old('port', $currentPort ?? '') == 'COM2' ? 'selected' : '' }}>COM2</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="baud_rate" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Baud Rate</label>
                                    <select id="baud_rate" name="baud_rate" class="form-select">
                                        <option value="9600" {{ old('baud_rate', $currentBaudRate ?? '9600') == '9600' ? 'selected' : '' }}>9600</option>
                                        <option value="115200" {{ old('baud_rate', $currentBaudRate ?? '') == '115200' ? 'selected' : '' }}>115200</option>
                                        <option value="57600" {{ old('baud_rate', $currentBaudRate ?? '') == '57600' ? 'selected' : '' }}>57600</option>
                                        <option value="38400" {{ old('baud_rate', $currentBaudRate ?? '') == '38400' ? 'selected' : '' }}>38400</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #E5E7EB; padding-top: 1.5rem;">
                            <div style="display: flex; align-items: center;">
                                <span id="connectionStatus">
                                    <span class="connection-status disconnected"></span> Desconectado
                                </span>
                                <button type="button" id="testConnectionBtn" class="btn btn-primary" style="margin-left: 0.5rem;">
                                    <i class="fas fa-plug mr-2"></i> Probar Conexión
                                </button>
                            </div>
                            
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ url()->previous() }}" class="btn btn-gray">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save mr-2"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const testConnectionBtn = document.getElementById('testConnectionBtn');
        const connectionStatus = document.getElementById('connectionStatus');
        const portSelect = document.getElementById('port');
        const baudRateSelect = document.getElementById('baud_rate');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        testConnectionBtn.addEventListener('click', function() {
            const port = portSelect.value;
            const baudRate = baudRateSelect.value;
            
            if (!port) {
                showNotification('Por favor, seleccione un puerto COM', 'error');
                return;
            }
            
            // Cambiar estado a "comprobando"
            const statusIndicator = connectionStatus.querySelector('.connection-status');
            statusIndicator.className = 'connection-status checking';
            connectionStatus.innerHTML = statusIndicator.outerHTML + ' Comprobando...';
            
            // Hacer la petición AJAX al servidor
            fetch("{{ route('arduino.config.test') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    port: port,
                    baud_rate: baudRate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusIndicator.className = 'connection-status connected';
                    connectionStatus.innerHTML = statusIndicator.outerHTML + ' Conectado';
                    showNotification(data.message, 'success');
                } else {
                    statusIndicator.className = 'connection-status disconnected';
                    connectionStatus.innerHTML = statusIndicator.outerHTML + ' Error';
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                statusIndicator.className = 'connection-status disconnected';
                connectionStatus.innerHTML = statusIndicator.outerHTML + ' Error';
                showNotification('Error al probar la conexión: ' + error.message, 'error');
            });
        });
        
        // Pre-seleccionar los puertos principales si existen
        if (!portSelect.value) {
            const com9Option = portSelect.querySelector('option[value="COM9"]');
            const com10Option = portSelect.querySelector('option[value="COM10"]');
            
            // Priorizar COM10, luego COM9
            if (com10Option) {
                com10Option.selected = true;
            } else if (com9Option) {
                com9Option.selected = true;
            }
        }
    });
    
    // Función para mostrar notificaciones
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type === 'success' ? 'notification-success' : 'notification-error'}`;
        
        notification.innerHTML = `
            <div style="display: flex; align-items: center;">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" 
                        style="margin-left: 1rem; color: #6B7280; background: none; border: none; cursor: pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }
    </script>
</body>
</html>