<?php

require_once 'vendor/autoload.php';

use Hms\Controllers\PatientController;
use Hms\Controllers\AppointmentController;
use Hms\Controllers\RecordController;
use Hms\Controllers\HomeController;

session_start();

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($url === "/home") {
    $controller = new HomeController();

    $controller->index();
}

if($url === "/patients") {
    $controller = new PatientController();

    if ($_SERVER['REQUEST_METHOD'] === "GET") {
        $controller->index();
    } elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        $controller->save($data);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents("php://input"), true);
        $controller->update($data);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents("php://input"), true);
        $controller->delete($data['id']);
    }
}

if($url === "/appointments") {
    $controller = new AppointmentController();

    if ($_SERVER['REQUEST_METHOD'] === "GET") {
        $patientId = 0;

        $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        $params = [];
        parse_str($queryString, $params);

        if (array_key_exists("patient", $params)) {
            $patientId = $params["patient"];
        }

        $controller->index($patientId);
    } elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        $controller->save($data);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents("php://input"), true);
        $controller->update($data);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents("php://input"), true);
        $controller->delete($data['id']);
    }
}

if($url === "/records") {
    $controller = new RecordController();

    if ($_SERVER['REQUEST_METHOD'] === "GET") {
        $patientId = 0;

        $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        $params = [];
        parse_str($queryString, $params);

        if (array_key_exists("patient", $params)) {
            $patientId = $params["patient"];
        }

        $controller->index($patientId);
    } elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        $controller->save($data);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents("php://input"), true);
        $controller->update($data);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents("php://input"), true);
        $controller->delete($data['id']);
    }
}