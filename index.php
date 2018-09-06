<?php
require_once("config/database.php");
$request = $_SERVER['REQUEST_METHOD'];
if ($_SERVER['REQUEST_METHOD'] === "GET") {
       $posts = $conn->query("SELECT * FROM posts WHERE 1");
       print_r($posts);
}
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    echo $request;
}
if ($_SERVER['REQUEST_METHOD'] === "PUT") {
    echo $request;
}
?>