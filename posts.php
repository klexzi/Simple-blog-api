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
       $post = $conn->query("SELECT * FROM posts WHERE `id` = $id");
       if($post->num_rows > 0) {
           echo json_encode($post->fetch_assoc());
       }
       else {
           echo "Post not Found";
       }
    } else {
           $return_arr = [];
    $posts = $conn->query("SELECT * FROM posts");
    if($posts->num_rows > 0) {  
        if(!$posts) http_response_code(500);

        while($row = $posts->fetch_assoc()) {
            array_push($return_arr, $row);
        }
        echo json_encode($return_arr);
        http_response_code(200);
    } else {
        echo "No Post found";
        http_response_code(404);
    } 
    }


} elseif ($request === "POST") {
    $body = json_decode(file_get_contents("php://input"));
    $title = $body->title;
    $content = $body->content;
    $author = $body->author;
    $addPost = $conn->query("INSERT into posts (`title`, `content`, `author`) VALUES ('$title', '$content', '$author');"); 
    if($addPost) {
        $lastId = $conn->insert_id;
        $newPost = $conn->query("SELECT * FROM posts WHERE id = $lastId");
        echo json_encode($newPost->fetch_assoc());
    }
} elseif ($request === "PUT") {
    $id = $_GET['id'];
    if(isset($id)) {
        $body = json_decode(file_get_contents("php://input"));
        $title = $body->title;
        $content = $body->content;
        $author = $body->author;
        $checkPost = $conn->query("SELECT * FROM posts WHERE `id` = $id");
        if($checkPost->num_rows < 1) {
            echo "No post with the given id";
            http_response_code(404);
        } else {
        $updatePost = $conn->query("UPDATE posts set `title`='$title', `content`='$content', `author`='$author' WHERE `id` = $id");
        if($updatePost) {
            $post = $conn->query("SELECT * FROM posts WHERE `id` = $id");
            echo json_encode($post->fetch_assoc());
        }
        }

    } else {
        echo "Id not sent";
    }
} elseif ($request === "DELETE") {
    if(isset($_GET['id'])) {
        $id = $_GET['id'];
        $deletePost = $conn->query("DELETE FROM `posts` WHERE id=$id");
        if($deletePost) echo json_encode(array("status" => true));
    }
}
?>