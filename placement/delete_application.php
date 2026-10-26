<?php
include 'db.php';
session_start();

if(!isset($_SESSION['student'])){
    header("Location: student_login.php");
    exit();
}

if(isset($_GET['id'])){

    $id = $_GET['id'];

    mysqli_query($conn,

    "DELETE FROM applications
     WHERE id='$id'");

}

header("Location: my_applications.php");
exit();
?>