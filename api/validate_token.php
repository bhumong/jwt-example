<?php
header("Access-Control-Allow-Origin: http://localhost/WEB/rest-api-authentication-example/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

$data = (object)$_POST;

$jwt = isset($data->jwt) ? $data->jwt : '';

if ($jwt) {
    try {
        $decode = JWT::decode($jwt, $key, ['HS256']);
        http_response_code(200);

        echo json_encode(['message' => 'access granted', 'data' => $decode->data]);
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['message' => 'access denied', 'error' => $e->getMessage()]);
    }
} else {
    http_response_code(401);
    echo json_encode(['message' => 'access denied']);
}