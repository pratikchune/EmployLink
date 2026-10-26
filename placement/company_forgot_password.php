<?php
session_start();

$msg = "";

if(isset($_POST['generate'])){

    $otp = rand(100000,999999);

    $_SESSION['otp'] = $otp;

    header("Location: company_verify_otp.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Forgot Password</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
}

.box{
    width:380px;
    padding:40px;
    border-radius:20px;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(20px);
    text-align:center;
    color:white;
}

h2{
    color:#00d4ff;
    margin-bottom:25px;
}

.input-box{
    display:flex;
    align-items:center;
    background:rgba(255,255,255,0.12);
    border-radius:30px;
    padding:12px 15px;
    margin:15px 0;
}

.input-box i{
    color:#00d4ff;
    margin-right:10px;
}

.input-box input{
    flex:1;
    border:none;
    outline:none;
    background:transparent;
    color:white;
}

button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:30px;
    background:#00d4ff;
    color:#001f2f;
    font-weight:bold;
    cursor:pointer;
    margin-top:15px;
}

button:hover{
    background:white;
}

</style>

</head>

<body>

<div class="box">

    <h2>
        <i class="fa fa-key"></i>
        Forgot Password
    </h2>

    <form method="POST">

        <div class="input-box">

            <i class="fa fa-envelope"></i>

            <input type="email"
                   name="email"
                   placeholder="Enter Registered Email"
                   required>

        </div>

        <button name="generate">
            Generate OTP
        </button>

    </form>

</div>

</body>
</html>