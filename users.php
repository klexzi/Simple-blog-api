<?php
header("Content-Type:application/json");
require_once("/config/database.php");
$request = $_SERVER['REQUEST_METHOD'];
function customError($errno, $errstr) {
    echo json_encode(array("error" => array($errno, $errstr)));
    // echo "<b>Error:</b> [$errno] $errstr";
  }
  //set error handler
set_error_handler("customError");
if ($request === "GET") {
    if(isset($_GET['id']) && is_numeric($_GET['id'])) {
       $id = $_GET['id'];
       $user = $conn->query("SELECT * FROM users WHERE `id` = $id");
       if($user->num_rows > 0) {
           $userData = $user->fetch_assoc();
           unset($userData["password"]);
           echo json_encode($userData);
       }
       else {
           echo "User not Found";
       }
    } else {
           $return_arr = [];
    $users = $conn->query("SELECT * FROM users");
    if($users->num_rows > 0) {  
        if(!$users) http_response_code(500);

        while($row = $users->fetch_assoc()) {
            unset($row["password"]);
            array_push($return_arr, $row);
        }
        echo json_encode($return_arr);
        http_response_code(200);
    } else {
        echo "No User found";
        http_response_code(404);
    } 
    }


} elseif ($request === "POST") {
    $body = json_decode(file_get_contents("php://input"));
    $name = $body->name;
    $email = $body->email;
    $password = md5($body->password);
    $addUser = $conn->query("INSERT into users (`name`, `email`, `password`) VALUES ('$name', '$email', '$password');"); 
    if($addUser) {
        $lastId = $conn->insert_id;
        $newUser = $conn->query("SELECT * FROM users WHERE id = $lastId");
        echo json_encode($newUser->fetch_assoc());
    }
} elseif ($request === "PUT") {
    $id = $_GET['id'];
    if(isset($id)) {
        $body = json_decode(file_get_contents("php://input"));
        $name = $body->name;
        $email = $body->email;
        $password = md5($body->password);
        $checkUsers = $conn->query("SELECT * FROM users WHERE `id` = $id");
        if($checkUsers->num_rows < 1) {
            echo "No user with the given id";
            http_response_code(404);
        } else {
        $updateUser = $conn->query("UPDATE users set `name`='$name', `email`='$email', `password`='$password' WHERE `id` = $id");
        if($updateUser) {
            $user = $conn->query("SELECT * FROM users WHERE `id` = $id");
            echo json_encode($user->fetch_assoc());
        }
        }

    } else {
        echo "Id not sent";
    }
} elseif ($request === "DELETE") {
    if(isset($_GET['id'])) {
        $id = $_GET['id'];
        $deleteUser = $conn->query("DELETE FROM `users` WHERE id=$id");
        if($deleteUser) echo json_encode(array("status" => true));
    }
}
?>