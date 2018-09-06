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
/*
* 
*
*
*/
function checkAuth() {
    if(isset(apache_request_headers()["X-AUTH-TOKEN"])) {
        try {
      $jwt = apache_request_headers()["X-AUTH-TOKEN"];
      $key = "keyCode";
      $decoded = JWT::decode($jwt, $key, array('HS256'));
      return $decoded;            
        } catch(\Exception $err) {
            return false;
        }

    }
}
if ($request === "GET") {
    if(isset($_GET['id']) && is_numeric($_GET['id'])) {
       $id = $_GET['id'];
       $comment = $conn->query("SELECT * FROM comments WHERE `id` = $id");
       if($comment->num_rows > 0) {
           echo json_encode($comment->fetch_assoc());
       }
       else {
           echo "comment not Found";
       }
    } else {
           $return_arr = [];
    $comments = $conn->query("SELECT * FROM comments");
    if($comments->num_rows > 0) {  
        if(!$comments) http_response_code(500);

        while($row = $comments->fetch_assoc()) {
            array_push($return_arr, $row);
        }
        echo json_encode($return_arr);
        http_response_code(200);
    } else {
        echo "No comment found";
        http_response_code(404);
    } 
    }


} elseif ($request === "POST") {
    if(checkAuth()) {
    $body = json_decode(file_get_contents("php://input"));
    $uid = $body->uid;
    $pid = $body->pid;
    $comment = $body->comment;
    $addcomment = $conn->query("INSERT into comments (`uid`, `pid`, `comment`) VALUES ('$uid', '$pid', '$comment');"); 
    if($addcomment) {
        $lastId = $conn->insert_id;
        $newcomment = $conn->query("SELECT * FROM comments WHERE id = $lastId");
        echo json_encode($newcomment->fetch_assoc());
    }        
    } else {
        echo json_encode("only authenticated user can access this endpoint");
    }

} elseif ($request === "PUT") {
    if(checkAuth()) {
    $id = $_GET['id'];
    if(isset($id)) {
        $body = json_decode(file_get_contents("php://input"));
        $uid = $body->uid;
        $pid = $body->pid;
        $comment = $body->comment;
        $checkcomments = $conn->query("SELECT * FROM comments WHERE `id` = $id");
        if($checkcomments->num_rows < 1) {
            echo "No comment with the given id";
            http_response_code(404);
        } else {
        $updatecomment = $conn->query("UPDATE comments set `uid`='$uid', `pid`='$pid', `comment`='$comment' WHERE `id` = $id");
        if($updatecomment) {
            $comment = $conn->query("SELECT * FROM comments WHERE `id` = $id");
            echo json_encode($comment->fetch_assoc());
        }
        }

    } else {
        echo "Id not sent";
    }        
    } else {
        echo json_encode("only authenticated user can access this endpoint");
    }

} elseif ($request === "DELETE") {
    if(isset($_GET['id'])) {
        $id = $_GET['id'];
        $deletecomment = $conn->query("DELETE FROM `comments` WHERE id=$id");
        if($deletecomment) echo json_encode(array("status" => true));
    }
}
?>