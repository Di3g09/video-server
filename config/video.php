<?php

return [

    // Carpeta donde Laravel guarda los videos subidos
    'library_path' => env('VIDEO_LIBRARY_PATH', storage_path('app/video_library')),

    // Carpeta que simula /opt/transmision en local
    'transmission_path' => env(
        'VIDEO_TRANSMISSION_PATH',
        storage_path('app/transmision_test') // valor por defecto
    ),

];
