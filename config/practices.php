<?php

return [

    /*
     | Estados de una práctica, en orden.
     */
    'states' => ['Asignado', 'En Curso', 'Informe Entregado', 'Evaluado', 'Finalizada'],

    /*
     | Estados en que la práctica cuenta como activa.
     */
    'active_states' => ['Asignado', 'En Curso', 'Informe Entregado', 'Evaluado'],

    /*
     | Competencias de la rúbrica (notas de 1.0 a 7.0).
     */
    'competencies' => [
        'responsabilidad' => 'Responsabilidad y puntualidad',
        'conocimientos' => 'Aplicación de conocimientos técnicos',
        'calidad' => 'Calidad del trabajo realizado',
        'trabajo_equipo' => 'Trabajo en equipo',
        'comunicacion' => 'Comunicación efectiva',
        'iniciativa' => 'Iniciativa y proactividad',
    ],

    'min_grade' => 1.0,
    'max_grade' => 7.0,

    /*
     | Nota mínima para aprobar la práctica.
     */
    'passing_grade' => 4.0,

    /*
     | Días que cubre cada registro de bitácora.
     */
    'log_period_days' => 14,

    /*
     | Enviar también por correo las notificaciones.
     */
    'mail_notifications' => env('PRACTICES_MAIL_NOTIFICATIONS', true),
];
