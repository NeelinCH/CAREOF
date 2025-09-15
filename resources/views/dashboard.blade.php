<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard CARE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .care-gradient {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }} CARE 
            </h2>
        </x-slot>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Banner de Versión de Prueba -->
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                    <p class="font-bold">Versión de Prueba</p>
                    <p>Estás utilizando la versión de prueba de CARE. Algunas funcionalidades pueden estar limitadas.</p>
                </div>
                
                <!-- Introducción al Sistema CARE -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 fade-in">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-9 h-9 bg-green-500 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-leaf text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">CARE</h2>
                        </div>
                        <h3 class="text-xl text-green-600 font-semibold mb-2">Control y Agenda de Registro Ecológico</h3>
                        
                        <div class="border-l-4 border-green-500 pl-4 my-6">
                            <p class="text-gray-700 mb-3">
                                CARE es una herramienta digital diseñada para facilitar la gestión del riego y cuidado de plantas, 
                                dirigida especialmente a huertas escolares y usuarios domésticos.
                            </p>
                            <p class="text-gray-700 mb-3">
                                Nuestro sistema combina una plataforma web intuitiva con un dispositivo de riego manual asistido, 
                                promoviendo el compromiso constante con el cuidado de las plantas y la educación ambiental.
                            </p>
                            <p class="text-gray-700">
                                Esta versión de prueba te permite explorar las funcionalidades básicas del sistema y familiarizarte 
                                con la interfaz antes de la implementación completa.
                            </p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg mt-4">
                            <h4 class="font-semibold text-gray-700 mb-2">Funcionalidades disponibles en esta versión:</h4>
                            <ul class="list-disc list-inside text-gray-600 space-y-1">
                                <li>Registro y gestión de plantas</li>
                                <li>Programación de tareas de cuidado (riego, fertilización, poda)</li>
                                <li>Registro de actividades realizadas</li>
                                <li>Visualización de estadísticas básicas</li>
                                <li>Sistema de recordatorios visuales</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Información del Equipo -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 fade-in">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">INTEGRANTES, ROL E INFORMACIÓN DE CONTACTO</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-green-600">Rosineth Chiquinquirá Esis Colina</h4>
                                <p class="text-sm text-gray-600">Líder, Técnico Principal y Comunicadora</p>
                                <p class="text-sm mt-1"><i class="far fa-envelope mr-2"></i>rosinethesis2018@gmail.com</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-green-600">Simón Medina Núñez</h4>
                                <p class="text-sm text-gray-600">Técnico y Encargado Financiero</p>
                                <p class="text-sm mt-1"><i class="far fa-envelope mr-2"></i>simonmedinanunez2@gmail.com</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-green-600">Klainer Eduardo Lamon Reyes</h4>
                                <p class="text-sm text-gray-600">Diseñador y Comunicador</p>
                                <p class="text-sm mt-1"><i class="far fa-envelope mr-2"></i>klainerlamon.28@gmail.com</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-green-600">Información del Proyecto</h4>
                                <p class="text-sm text-gray-600">Fecha de Iniciación: 22/04/2024</p>
                                <p class="text-sm text-gray-600">Grupo: 10° (2024) - 11° (2025) Media Técnica</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mensaje de bienvenida original -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        {{ __("¡Bienvenid@!") }}
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
</body>
</html>