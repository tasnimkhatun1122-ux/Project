<?php
/**
 * Renders a view file from app/Views/ with the given data made available
 * to it as local variables. Called from controllers as:
 *   view('doctor/dashboard', ['doctor' => $doctor, ...]);
 */
function view($viewName, $data = [])
{
    extract($data);
    require __DIR__ . '/../Views/' . $viewName . '.php';
}
