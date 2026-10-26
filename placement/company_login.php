<?php
include 'db.php';
session_start();

$error = "";

/* LOGIN */

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $pass = trim($_POST['password']);

    $query = mysqli_query($conn,

    "SELECT * FROM companies
     WHERE email='$email'
     AND password='$pass'");

    if(mysqli_num_rows($query) > 0){

        $row = mysqli_fetch_assoc($query);

        $_SESSION['company_id'] = $row['id'];
        $_SESSION['company_name'] = $row['name'];

        header("Location: company_dashboard.php");
        exit();

    }else{

        $error = "Invalid Email or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Company Login</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

/* ===== GLOBAL ===== */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#f5f7fa;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

/* ===== LOGIN BOX ===== */

.login-box{
    width:100%;
    max-width:360px;
    background:white;
    padding:28px 24px;
    border-radius:18px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    border-top:5px solid #ff6f00;
}

/* ===== TITLE ===== */

.login-box h2{
    text-align:center;
    color:#4a148c;
    margin-bottom:24px;
    font-size:26px;
    font-weight:700;
}

/* ===== INPUT BOX ===== */

.input-box{
    position:relative;
    margin-bottom:18px;
}

.input-box label{
    display:block;
    margin-bottom:7px;
    color:#4a148c;
    font-size:13px;
    font-weight:600;
}

/* ===== LEFT ICON ===== */

.left-icon{
    position:absolute;
    left:13px;
    top:41px;
    color:#ff6f00;
    font-size:14px;
}

/* ===== INPUT ===== */

.input-box input{
    width:100%;
    padding:11px 40px 11px 38px;
    border:1px solid #ddd;
    border-radius:10px;
    outline:none;
    font-size:13px;
    transition:0.3s;
}

.input-box input:focus{
    border-color:#ff6f00;
    box-shadow:0 0 5px rgba(255,111,0,0.3);
}

/* ===== EYE ICON ===== */

.eye-icon{
    position:absolute;
    right:13px;
    top:41px;
    color:#666;
    font-size:14px;
    cursor:pointer;
}

/* ===== FORGOT PASSWORD ===== */

.forgot{
    text-align:right;
    margin-bottom:18px;
}

.forgot a{
    text-decoration:none;
    color:#4a148c;
    font-size:12px;
    font-weight:500;
}

.forgot a:hover{
    color:#ff6f00;
}

/* ===== BUTTON ===== */

.btn{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:#ff6f00;
    color:white;
    font-size:14px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

.btn:hover{
    background:#4a148c;
}

/* ===== LINKS ===== */

.links{
    text-align:center;
    margin-top:18px;
}

.links a{
    text-decoration:none;
    color:#4a148c;
    font-size:13px;
    font-weight:500;
}

.links a:hover{
    color:#ff6f00;
}

/* ===== BACK BUTTON ===== */

.back-btn{
    display:block;
    width:100%;
    text-align:center;
    margin-top:14px;
    padding:11px;
    border:1px solid #4a148c;
    border-radius:10px;
    text-decoration:none;
    color:#4a148c;
    font-size:13px;
    font-weight:600;
    transition:0.3s;
}

.back-btn:hover{
    background:#4a148c;
    color:white;
}

/* ===== ERROR ===== */

.error{
    margin-top:18px;
    background:#ffe5e5;
    color:#d32f2f;
    padding:12px;
    border-radius:8px;
    text-align:center;
    font-size:13px;
    font-weight:600;
}

/* ===== RESPONSIVE ===== */

@media(max-width:450px){

    .login-box{
        width:95%;
        padding:24px 20px;
    }
}

</style>

</head>

<body>

<!-- LOGIN BOX -->

<div class="login-box">

    <h2>

        <i class="fa fa-building"></i>
        Company Login

    </h2>

    <!-- FORM -->

    <form method="POST">

        <!-- EMAIL -->

        <div class="input-box">

            <label>Email Address</label>

            <i class="fa fa-envelope left-icon"></i>

            <input type="email"
            name="email"
            placeholder="Enter Email"
            required>

        </div>

        <!-- PASSWORD -->

        <div class="input-box">

            <label>Password</label>

            <i class="fa fa-lock left-icon"></i>

            <input type="password"
            id="password"
            name="password"
            placeholder="Enter Password"
            required>

            <i class="fa fa-eye eye-icon"
            id="eyeIcon"
            onclick="togglePassword()"></i>

        </div>

        <!-- FORGOT PASSWORD -->

        <div class="forgot">

            <a href="company_forgot_password.php">

                Forgot Password?

            </a>

        </div>

        <!-- BUTTON -->

        <button class="btn" name="login">

            <i class="fa fa-sign-in-alt"></i>
            Login

        </button>

    </form>

    <!-- CREATE ACCOUNT -->

    <div class="links">

        <a href="company_register.php">

            Create Company Account

        </a>

    </div>

    <!-- BACK BUTTON -->

    <a href="index1.php" class="back-btn">

        <i class="fa fa-arrow-left"></i>
        Back To Home

    </a>

    <!-- ERROR MESSAGE -->

    <?php if($error != ""){ ?>

        <div class="error">

            <?php echo $error; ?>

        </div>

    <?php } ?>

</div>

<!-- PASSWORD TOGGLE -->

<script>

function togglePassword(){

    let pass = document.getElementById("password");
    let eye = document.getElementById("eyeIcon");

    if(pass.type === "password"){

        pass.type = "text";

        eye.classList.remove("fa-eye");
        eye.classList.add("fa-eye-slash");

    }else{

        pass.type = "password";

        eye.classList.remove("fa-eye-slash");
        eye.classList.add("fa-eye");
    }
}

</script>

</body>
</html>