<?php 
include 'db.php'; 
session_start(); 

$error = "";

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $pass = $_POST['password'];

    $result = mysqli_query($conn,"SELECT * FROM users WHERE email='$email' AND password='$pass'");

    if(mysqli_num_rows($result) > 0){

        $_SESSION['student'] = $email;

        header("Location: dashboard.php");
        exit();

    } else {

        $error = "Invalid Email or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Student Login</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

/* BODY */

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#f5f7fa;
}

/* LOGIN BOX */

.login-box{
    width:380px;
    background:white;
    padding:40px;
    border-radius:12px;
    box-shadow:0 5px 20px rgba(0,0,0,0.1);
}

/* TITLE */

.login-box h2{
    text-align:center;
    margin-bottom:30px;
    color:#4a148c;
    font-size:30px;
}

/* INPUT BOX */

.input-box{
    position:relative;
    margin-bottom:20px;
}

/* LEFT ICON */

.left-icon{
    position:absolute;
    left:15px;
    top:50%;
    transform:translateY(-50%);
    color:#777;
}

/* INPUT */

.input-box input{
    width:100%;
    padding:13px 45px 13px 45px;
    border:1px solid #ddd;
    border-radius:6px;
    outline:none;
    font-size:14px;
    transition:0.3s;
}

.input-box input:focus{
    border-color:#4a148c;
    box-shadow:0 0 5px rgba(74,20,140,0.2);
}

/* EYE ICON */

.eye-icon{
    position:absolute;
    right:15px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    color:#777;
    font-size:15px;
}

/* FORGOT PASSWORD */

.forgot-center{
    text-align:right;
    margin-bottom:20px;
}

.forgot-center a{
    text-decoration:none;
    color:#4a148c;
    font-size:13px;
}

.forgot-center a:hover{
    color:#ff6f00;
}

/* BUTTON */

button{
    width:100%;
    padding:13px;
    border:none;
    border-radius:6px;
    background:#4a148c;
    color:white;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#ff6f00;
}

/* LINKS */

.links{
    text-align:center;
    margin-top:20px;
}

.links a{
    text-decoration:none;
    color:#4a148c;
    font-size:14px;
}

.links a:hover{
    color:#ff6f00;
}

/* BACK BUTTON */

.back-btn{
    display:block;
    text-align:center;
    margin-top:15px;
    padding:11px;
    border:1px solid #4a148c;
    border-radius:6px;
    color:#4a148c;
    text-decoration:none;
    transition:0.3s;
    font-weight:500;
}

.back-btn:hover{
    background:#4a148c;
    color:white;
}

/* ERROR */

.error{
    margin-top:15px;
    color:red;
    text-align:center;
    font-size:14px;
    font-weight:500;
}

/* RESPONSIVE */

@media(max-width:450px){

    .login-box{
        width:90%;
        padding:30px 20px;
    }

    .login-box h2{
        font-size:24px;
    }
}

</style>

</head>

<body>

<div class="login-box">

    <h2>
        <i class="fa fa-user-graduate"></i>
        Student Login
    </h2>

    <form method="POST">

        <!-- EMAIL -->

        <div class="input-box">

            <i class="fa fa-envelope left-icon"></i>

            <input type="email"
            name="email"
            placeholder="Enter Email"
            required>

        </div>

        <!-- PASSWORD -->

        <div class="input-box">

            <i class="fa fa-lock left-icon"></i>

            <input type="password"
            name="password"
            id="password"
            placeholder="Enter Password"
            required>

            <i class="fa fa-eye eye-icon"
            onclick="togglePassword()"></i>

        </div>

        <!-- FORGOT PASSWORD -->

        <div class="forgot-center">

            <a href="forgot_password.php">
                Forgot Password?
            </a>

        </div>

        <!-- LOGIN BUTTON -->

        <button name="login">
            Login
        </button>

    </form>

    <!-- ERROR MESSAGE -->

    <?php
    if($error != ""){
        echo "<p class='error'>$error</p>";
    }
    ?>

    <!-- REGISTER -->

    <div class="links">

        <a href="register.php">
            Create Account
        </a>

    </div>

    <!-- BACK BUTTON -->

    <a href="index1.php" class="back-btn">

        <i class="fa fa-arrow-left"></i>

        Back to Home

    </a>

</div>

<script>

function togglePassword(){

    let pass = document.getElementById("password");

    if(pass.type === "password"){

        pass.type = "text";

    }else{

        pass.type = "password";
    }
}

</script>

</body>
</html>