<?php
session_start();
include 'db.php';

$msg = "";

if(isset($_POST['reset'])){

    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if($new_pass == $confirm_pass){

        // Get email from session
        $email = $_SESSION['email'];

        // Update password
        mysqli_query($conn,
        "UPDATE companies 
         SET password='$new_pass' 
         WHERE email='$email'");

        echo "
        <script>
            alert('Password Reset Successful');
            window.location='company_login.php';
        </script>
        ";

    }else{
        $msg = 'Passwords do not match';
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Reset Password</title>

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
    font-size:14px;
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
    transition:0.3s;
}

button:hover{
    background:white;
}

.msg{
    margin-top:15px;
    color:#ff6b6b;
}

</style>

</head>

<body>

<div class="box">

    <h2>
        <i class="fa fa-lock"></i>
        Reset Password
    </h2>

    <form method="POST">

        <!-- NEW PASSWORD -->
        <div class="input-box">

            <i class="fa fa-key"></i>

            <input type="password"
                   name="new_password"
                   placeholder="Enter New Password"
                   required>

        </div>

        <!-- CONFIRM PASSWORD -->
        <div class="input-box">

            <i class="fa fa-lock"></i>

            <input type="password"
                   name="confirm_password"
                   placeholder="Confirm Password"
                   required>

        </div>

        <button name="reset">
            Reset Password
        </button>

    </form>

    <?php
    if($msg != ""){
        echo "<p class='msg'>$msg</p>";
    }
    ?>

</div>

</body>
</html>