<?php
header("Content-Type:application/json");
require_once '/vendor/autoload.php';
use \Firebase\JWT\JWT;

require_once("/config/database.php");
$request = $_SERVER['REQUEST_METHOD'];
function customError($errno, $errstr) {
    echo json_encode(array("error" => array($errno, $errstr)));
    // echo "<b>Error:</b> [$errno] $errstr";
  }
  //set error handler
set_error_handler("customError");
if ($request === "POST") {
    $body = json_decode(file_get_contents("php://input"));
    $email = $body->email;
    $password = md5($body->password);

    $chkUser = $conn->query("SELECT * FROM users WHERE `email` = '$email' AND `password` = '$password'");
    if($chkUser->num_rows > 0) {

        $key = "keyCode";
        $token = $chkUser->fetch_assoc();
        unset($token["password"]);
        $token["iat"] = time();
        $token["exp"] = time() + 3600;

        $jwt = json_encode(JWT::encode($token, $key));
        echo $jwt;
    } else {
        echo json_encode("Invalid Email and password");
        http_response_code(401);
    }
}
?>