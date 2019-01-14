<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once 'config/database.php';
include_once 'objects/user.php';

// files for jwt will be here
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

$database = new Database();
$db = $database->getContection();

$user = new User($db);

$data = (object)$_POST;

$jwt = isset($data) ? $data->jwt : '';

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        
        // set user property values here
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->email = $data->email;
        $user->password = $data->password;
        $user->id = $decoded->data->id;

        if ($user->update()) {
            $token = [
                "iss" => $iss,
                "aud" => $aud,
                "iat" => $iat,
                "nbf" => $nbf,
                "data" => [
                    "id" => $user->id,
                    "firstname" => $user->firstname,
                    "lastname" => $user->lastname,
                    "email" => $user->email
                ]
            ];
            $jwt = JWT::encode($token, $key);
            http_response_code(200);
            echo json_encode(['message' => 'success update user', 'jwt' => $jwt]);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'unable to update user',]);
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            'message' => 'access denied',
            'error' => $e->getMessage(),
        ]);
    }
 
    // catch failed decoding will be here
} else {
    http_response_code(401);
        echo json_encode([
            'message' => 'access denied.',
        ]);
}
 
// error message if jwt is empty will be here
?>