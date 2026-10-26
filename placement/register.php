<?php 
include 'db.php'; 
?>

<!DOCTYPE html>
<html>
<head>

<title>Student Registration</title>

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

/* REGISTER BOX */

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
    margin-bottom:30px;
    color:#4a148c;
    font-size:32px;
}

/* INPUT BOX */

.input-box{
    position:relative;
    margin-bottom:22px;
}

/* LEFT ICON */

.left-icon{
    position:absolute;
    left:15px;
    top:50%;
    transform:translateY(-50%);
    color:#777;
    font-size:15px;
}

/* INPUT FIELD */

.input-box input{
    width:100%;
    padding:14px 45px;
    border:1px solid #ddd;
    border-radius:8px;
    outline:none;
    font-size:15px;
    transition:0.3s;
}

.input-box input:focus{
    border-color:#ff6f00;
    box-shadow:0 0 5px rgba(255,111,0,0.3);
}

/* EYE ICON */

.eye-icon{
    position:absolute;
    right:15px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    color:#777;
    transition:0.3s;
}

.eye-icon:hover{
    color:#4a148c;
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
    transition:0.3s;
}

button:hover{
    background:#4a148c;
}

/* LINKS */

.links{
    text-align:center;
    margin-top:22px;
}

.links a{
    text-decoration:none;
    color:#4a148c;
    font-size:14px;
    transition:0.3s;
}

.links a:hover{
    color:#ff6f00;
    text-decoration:underline;
}

/* SUCCESS */

.success{
    margin-top:15px;
    background:#e8f5e9;
    color:#2e7d32;
    padding:12px;
    border-radius:8px;
    text-align:center;
    font-size:14px;
    font-weight:600;
}

/* ERROR */

.error{
    margin-top:15px;
    background:#ffebee;
    color:#c62828;
    padding:12px;
    border-radius:8px;
    text-align:center;
    font-size:14px;
    font-weight:600;
}

/* BACK BUTTON */

.back-btn{
    display:block;
    text-align:center;
    margin-top:18px;
    padding:12px;
    border:2px solid #4a148c;
    border-radius:8px;
    color:#4a148c;
    text-decoration:none;
    font-weight:500;
    transition:0.3s;
}

.back-btn:hover{
    background:#4a148c;
    color:white;
}

/* RESPONSIVE */

@media(max-width:500px){

    .register-box{
        width:100%;
        padding:25px;
    }

    .register-box h2{
        font-size:26px;
    }
}

</style>

</head>

<body>

<div class="register-box">

    <h2>Student Registration</h2>

    <form method="POST">

        <!-- NAME -->

        <div class="input-box">

            <i class="fa fa-user left-icon"></i>

            <input type="text"
            name="name"
            placeholder="Full Name"
            required>

        </div>

        <!-- EMAIL -->

        <div class="input-box">

            <i class="fa fa-envelope left-icon"></i>

            <input type="email"
            name="email"
            placeholder="Email Address"
            required>

        </div>

        <!-- PASSWORD -->

        <div class="input-box">

            <i class="fa fa-lock left-icon"></i>

            <input type="password"
            id="password"
            name="password"
            placeholder="Password"
            required>

            <i class="fa fa-eye eye-icon"
            onclick="togglePassword('password',this)"></i>

        </div>

        <!-- CONFIRM PASSWORD -->

        <div class="input-box">

            <i class="fa fa-lock left-icon"></i>

            <input type="password"
            id="confirm"
            name="confirm"
            placeholder="Confirm Password"
            required>

            <i class="fa fa-eye eye-icon"
            onclick="togglePassword('confirm',this)"></i>

        </div>

        <!-- BUTTON -->

        <button type="submit" name="register">

            Register

        </button>

    </form>

    <!-- PHP -->

    <?php

    if(isset($_POST['register'])){

        $name = $_POST['name'];
        $email = $_POST['email'];
        $pass = $_POST['password'];
        $confirm = $_POST['confirm'];

        if($pass != $confirm){

            echo "<div class='error'>
                    Passwords do not match
                  </div>";

        } else {

            $check = mysqli_query($conn,

            "SELECT * FROM users
             WHERE email='$email'");

            if(mysqli_num_rows($check) > 0){

                echo "<div class='error'>
                        Email already exists
                      </div>";

            } else {

                $sql = "INSERT INTO users
                        (name,email,password,role)

                        VALUES

                        ('$name','$email',
                        '$pass','student')";

                $insert = mysqli_query($conn,$sql);

                if($insert){

                    echo "<div class='success'>
                            Registration Successful!
                          </div>";

                } else {

                    echo "<div class='error'>
                            Registration Failed
                          </div>";
                }
            }
        }
    }
    ?>

    <!-- LOGIN LINK -->

    <div class="links">

        <a href="student_login.php">

            Already have an account? Login

        </a>

    </div>


</div>

<!-- PASSWORD TOGGLE -->

<script>

function togglePassword(id,icon){

    let field = document.getElementById(id);

    if(field.type === "password"){

        field.type = "text";

        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");

    } else {

        field.type = "password";

        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

</script>

</body>
</html>