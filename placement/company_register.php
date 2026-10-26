<?php 
include 'db.php'; 

$msg = "";
$msg_class = "";

if(isset($_POST['register'])){

    $company_name = $_POST['company_name'];
    $hr_name = $_POST['hr_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $pass = $_POST['password'];
    $confirm = $_POST['confirm'];

    /* PASSWORD CHECK */

    if($pass != $confirm){

        $msg = "Passwords do not match";
        $msg_class = "error";

    } else {

        /* EMAIL CHECK */

        $check = mysqli_query($conn,
        "SELECT * FROM companies WHERE email='$email'");

        if(mysqli_num_rows($check) > 0){

            $msg = "Email already exists";
            $msg_class = "error";

        } else {

            /* INSERT DATA */

            $sql = "INSERT INTO companies
                    (name, hr_name, email, phone, password)

                    VALUES

                    ('$company_name', '$hr_name',
                    '$email', '$phone', '$pass')";

            $insert = mysqli_query($conn,$sql);

            if($insert){

                $msg = "Company Registered Successfully!";
                $msg_class = "success";

            } else {

                $msg = "Registration Failed: " . mysqli_error($conn);
                $msg_class = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Company Registration</title>

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
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#f5f7fa;
    padding:20px;
}

/* BOX */

.register-box{
    width:420px;
    background:white;
    padding:40px;
    border-radius:14px;
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
}

/* TITLE */

.register-box h2{
    text-align:center;
    margin-bottom:25px;
    color:#4a148c;
    font-size:30px;
}

/* INPUT */

.input-box{
    position:relative;
    margin-bottom:18px;
}

.input-box input{
    width:100%;
    padding:14px 45px;
    border:1px solid #ddd;
    border-radius:8px;
    font-size:15px;
}

/* ICON */

.left-icon{
    position:absolute;
    left:15px;
    top:50%;
    transform:translateY(-50%);
    color:#777;
}

/* BUTTON */

button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:8px;
    background:#ff6f00;
    color:white;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
}

button:hover{
    background:#4a148c;
}

/* MESSAGE */

.success{
    margin-top:12px;
    padding:12px;
    background:#e8f5e9;
    color:#2e7d32;
    text-align:center;
    border-radius:8px;
}

.error{
    margin-top:12px;
    padding:12px;
    background:#ffebee;
    color:#c62828;
    text-align:center;
    border-radius:8px;
}

/* LINKS */

.links{
    text-align:center;
    margin-top:15px;
}

.links a{
    color:#4a148c;
    text-decoration:none;
}

</style>

</head>

<body>

<div class="register-box">

    <h2>Company Registration</h2>

    <form method="POST">

        <!-- COMPANY NAME -->

        <div class="input-box">

            <i class="fa fa-building left-icon"></i>

            <input type="text"
            name="company_name"
            placeholder="Company Name"
            required>

        </div>

        <!-- HR NAME -->

        <div class="input-box">

            <i class="fa fa-user left-icon"></i>

            <input type="text"
            name="hr_name"
            placeholder="HR / Recruiter Full Name"
            required>

        </div>

        <!-- EMAIL -->

        <div class="input-box">

            <i class="fa fa-envelope left-icon"></i>

            <input type="email"
            name="email"
            placeholder="Company Email"
            required>

        </div>

        <!-- PHONE -->

        <div class="input-box">

            <i class="fa fa-phone left-icon"></i>

            <input type="text"
            name="phone"
            placeholder="Phone Number"
            required>

        </div>

        <!-- PASSWORD -->

        <div class="input-box">

            <i class="fa fa-lock left-icon"></i>

            <input type="password"
            name="password"
            placeholder="Password"
            required>

        </div>

        <!-- CONFIRM PASSWORD -->

        <div class="input-box">

            <i class="fa fa-lock left-icon"></i>

            <input type="password"
            name="confirm"
            placeholder="Confirm Password"
            required>

        </div>

        <button type="submit" name="register">
            Register
        </button>

    </form>

    <?php
    if($msg != ""){
        echo "<div class='$msg_class'>$msg</div>";
    }
    ?>

    <div class="links">
        <a href="company_login.php">Already have account? Login</a>
    </div>

</div>

</body>
</html>