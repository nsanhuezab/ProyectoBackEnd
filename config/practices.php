<?php

return [

    /*
     | Flujo de estados de una práctica (enunciado, punto 3).
     | Cada estado solo puede avanzar al siguiente.
     */
    'states' => ['Asignado', 'En Curso', 'Informe Entregado', 'Evaluado', 'Finalizada'],

    /*
     | Estados en los que la práctica se considera "activa" (estudiante en práctica).
     */
    'active_states' => ['Asignado', 'En Curso', 'Informe Entregado', 'Evaluado'],

    /*
     | Rúbrica de evaluación (enunciado, punto 4). La completan el supervisor de la empresa
     | y el coordinador académico. Cada competencia se califica de 1.0 a 7.0.
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
     | Días que cubre cada registro de bitácora (avance quincenal).
     */
    'log_period_days' => 14,

    /*
     | Enviar también por correo las notificaciones de la plataforma (RF-10).
     | Con MAIL_MAILER=log los correos quedan en storage/logs/laravel.log.
     */
    'mail_notifications' => env('PRACTICES_MAIL_NOTIFICATIONS', true),
];
