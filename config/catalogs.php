<?php

use App\Models\ApplicationMessage;
use App\Models\CompanyReview;
use App\Models\PracticeEvaluation;
use App\Models\PracticeLog;
use Spatie\Permission\Models\Role;

/*
|--------------------------------------------------------------------------
| Mantenedores (CRUD) basados en configuración
|--------------------------------------------------------------------------
| Cada entrada genera las rutas  mantenedores/{clave}  (index, store, update,
| destroy) y se muestra con la vista genérica resources/views/catalogs/index.
| Solo Administrador y Super Administrador pueden usarlos.
|
| Tipos de campo: text, textarea, number, date, select, json
| 'source' (select): practices | companies | users | applications
*/

return [

    // 8. Bitácoras de práctica -------------------------------------------------
    'practice-logs' => [
        'model' => PracticeLog::class,
        'title' => 'Bitácoras de práctica',
        'singular' => 'bitácora',
        'description' => 'Registro quincenal de horas, actividades y aprendizajes de cada práctica.',
        'with' => ['practice.application.student.user', 'practice.application.offer'],
        'search' => ['activities', 'learnings', 'difficulties'],
        'order' => ['id', 'desc'],
        'columns' => [
            ['label' => 'Práctica', 'path' => 'practice', 'format' => 'label'],
            ['label' => 'Desde', 'path' => 'period_start', 'format' => 'date'],
            ['label' => 'Hasta', 'path' => 'period_end', 'format' => 'date'],
            ['label' => 'Horas', 'path' => 'hours'],
            ['label' => 'Actividades', 'path' => 'activities', 'format' => 'limit'],
        ],
        'fields' => [
            ['name' => 'practice_id', 'label' => 'Práctica', 'type' => 'select', 'source' => 'practices', 'rules' => ['required', 'integer', 'exists:practices,id']],
            ['name' => 'period_start', 'label' => 'Inicio del período', 'type' => 'date', 'rules' => ['required', 'date']],
            ['name' => 'period_end', 'label' => 'Fin del período', 'type' => 'date', 'rules' => ['required', 'date', 'after_or_equal:period_start']],
            ['name' => 'hours', 'label' => 'Horas trabajadas', 'type' => 'number', 'min' => 1, 'max' => 120, 'rules' => ['required', 'integer', 'between:1,120']],
            ['name' => 'activities', 'label' => 'Actividades realizadas', 'type' => 'textarea', 'full' => true, 'rules' => ['required', 'string', 'max:5000']],
            ['name' => 'learnings', 'label' => 'Aprendizajes', 'type' => 'textarea', 'full' => true, 'rules' => ['nullable', 'string', 'max:5000']],
            ['name' => 'difficulties', 'label' => 'Dificultades', 'type' => 'textarea', 'full' => true, 'rules' => ['nullable', 'string', 'max:5000']],
        ],
    ],

    // 9. Evaluaciones de práctica ---------------------------------------------
    'practice-evaluations' => [
        'model' => PracticeEvaluation::class,
        'title' => 'Evaluaciones de práctica',
        'singular' => 'evaluación',
        'description' => 'Rúbricas del supervisor y del coordinador. Solo una evaluación por rol en cada práctica.',
        'with' => ['practice.application.student.user', 'practice.application.offer', 'evaluator'],
        'search' => ['evaluator_role', 'comments'],
        'order' => ['id', 'desc'],
        'unique_together' => ['practice_id', 'evaluator_role'],
        'columns' => [
            ['label' => 'Práctica', 'path' => 'practice', 'format' => 'label'],
            ['label' => 'Rol evaluador', 'path' => 'evaluator_role'],
            ['label' => 'Evaluador', 'path' => 'evaluator', 'format' => 'label'],
            ['label' => 'Nota', 'path' => 'grade'],
        ],
        'fields' => [
            ['name' => 'practice_id', 'label' => 'Práctica', 'type' => 'select', 'source' => 'practices', 'rules' => ['required', 'integer', 'exists:practices,id']],
            ['name' => 'evaluator_role', 'label' => 'Rol del evaluador', 'type' => 'select', 'options' => ['supervisor' => 'Supervisor de la empresa', 'coordinador' => 'Coordinador académico'], 'rules' => ['required', 'in:supervisor,coordinador']],
            ['name' => 'evaluator_id', 'label' => 'Evaluador (usuario)', 'type' => 'select', 'source' => 'users', 'rules' => ['nullable', 'integer', 'exists:users,id']],
            ['name' => 'grade', 'label' => 'Nota final (1,0 a 7,0)', 'type' => 'number', 'min' => 1, 'max' => 7, 'step' => '0.1', 'rules' => ['required', 'numeric', 'between:1,7']],
            ['name' => 'scores', 'label' => 'Puntajes por competencia (JSON)', 'type' => 'json', 'full' => true, 'help' => 'Ejemplo: {"responsabilidad": 6.5, "trabajo_en_equipo": 6.0}', 'rules' => ['required', 'json']],
            ['name' => 'comments', 'label' => 'Comentarios', 'type' => 'textarea', 'full' => true, 'rules' => ['nullable', 'string', 'max:5000']],
        ],
    ],

    // 10. Reseñas de empresa ---------------------------------------------------
    'company-reviews' => [
        'model' => CompanyReview::class,
        'title' => 'Reseñas de empresa',
        'singular' => 'reseña',
        'description' => 'Valoración (1 a 5) que deja el estudiante sobre la empresa donde hizo su práctica.',
        'with' => ['practice.application.student.user', 'practice.application.offer', 'company'],
        'search' => ['comment'],
        'order' => ['id', 'desc'],
        'columns' => [
            ['label' => 'Práctica', 'path' => 'practice', 'format' => 'label'],
            ['label' => 'Empresa', 'path' => 'company.razon_social'],
            ['label' => 'Valoración', 'path' => 'rating'],
            ['label' => 'Comentario', 'path' => 'comment', 'format' => 'limit'],
        ],
        'fields' => [
            ['name' => 'practice_id', 'label' => 'Práctica', 'type' => 'select', 'source' => 'practices', 'unique' => ['company_reviews', 'practice_id'], 'rules' => ['required', 'integer', 'exists:practices,id']],
            ['name' => 'company_id', 'label' => 'Empresa', 'type' => 'select', 'source' => 'companies', 'rules' => ['required', 'integer', 'exists:companies,id']],
            ['name' => 'rating', 'label' => 'Valoración (1 a 5)', 'type' => 'number', 'min' => 1, 'max' => 5, 'rules' => ['required', 'integer', 'between:1,5']],
            ['name' => 'comment', 'label' => 'Comentario', 'type' => 'textarea', 'full' => true, 'rules' => ['nullable', 'string', 'max:3000']],
        ],
    ],

    // 11. Mensajes de postulación ---------------------------------------------
    'application-messages' => [
        'model' => ApplicationMessage::class,
        'title' => 'Mensajes de postulación',
        'singular' => 'mensaje',
        'description' => 'Conversaciones entre estudiantes, coordinadores y empresas dentro de una postulación (moderación).',
        'with' => ['application.student.user', 'application.offer', 'user'],
        'search' => ['body', 'application_status'],
        'order' => ['id', 'desc'],
        'columns' => [
            ['label' => 'Postulación', 'path' => 'application', 'format' => 'label'],
            ['label' => 'Autor', 'path' => 'user', 'format' => 'label'],
            ['label' => 'Estado', 'path' => 'application_status'],
            ['label' => 'Mensaje', 'path' => 'body', 'format' => 'limit'],
        ],
        'fields' => [
            ['name' => 'application_id', 'label' => 'Postulación', 'type' => 'select', 'source' => 'applications', 'rules' => ['required', 'integer', 'exists:applications,id']],
            ['name' => 'user_id', 'label' => 'Autor', 'type' => 'select', 'source' => 'users', 'rules' => ['nullable', 'integer', 'exists:users,id']],
            ['name' => 'application_status', 'label' => 'Estado de la postulación', 'type' => 'select', 'options' => ['Postulado' => 'Postulado', 'Asignado' => 'Asignado', 'Rechazado' => 'Rechazado'], 'rules' => ['required', 'string', 'max:40']],
            ['name' => 'body', 'label' => 'Mensaje', 'type' => 'textarea', 'full' => true, 'rules' => ['required', 'string', 'min:2', 'max:2000']],
        ],
    ],

    // 12. Roles ----------------------------------------------------------------
    'roles' => [
        'model' => Role::class,
        'title' => 'Roles',
        'singular' => 'rol',
        'description' => 'Roles del sistema. Los 5 roles base están protegidos y no se pueden editar ni eliminar.',
        'with' => [],
        'count' => ['users'],
        'search' => ['name'],
        'order' => ['name', 'asc'],
        'defaults' => ['guard_name' => 'web'],
        'locked' => ['column' => 'name', 'values' => ['Super Administrador', 'Administrador', 'Coordinador', 'Estudiante', 'Empresa']],
        'restrict_delete' => ['users' => 'usuario(s)'],
        'columns' => [
            ['label' => 'Rol', 'path' => 'name'],
            ['label' => 'Guard', 'path' => 'guard_name'],
            ['label' => 'Usuarios', 'path' => 'users_count'],
        ],
        'fields' => [
            ['name' => 'name', 'label' => 'Nombre del rol', 'type' => 'text', 'maxlength' => 80, 'unique' => ['roles', 'name'], 'rules' => ['required', 'string', 'max:80']],
        ],
    ],
];
