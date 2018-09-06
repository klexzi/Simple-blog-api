<?php
define("HOST", "localhost");
define("USER", "root");
define("PASSWORD", "");
define("DATABASE", "blog");

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die("could not connect to the database");
?>