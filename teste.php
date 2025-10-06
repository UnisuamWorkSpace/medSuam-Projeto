<?php
if($_SERVER["REQUEST_METHOD"] == "POST" ) {
    $email = $_POST["email"];
    echo $email . "<br>";
    echo "ioioi";
}else {
    echo"not a post request";
}