<?php
session_start();

$error = "";

if(isset($_POST['verify'])){

    $entered_otp = $_POST['otp'];

    if($entered_otp == $_SESSION['otp']){

        echo "
        <script>
            alert('OTP Verified Successfully');
            window.location='company_reset_password.php';
        </script>
        ";

    }else{
        $error = "Invalid OTP";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Verify OTP</title>

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
    margin-bottom:20px;
}

.otp-show{
    background:rgba(0,212,255,0.15);
    padding:15px;
    border-radius:12px;
    margin-bottom:20px;
    color:#00d4ff;
    font-size:24px;
    font-weight:bold;
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

.error{
    color:#ff6b6b;
    margin-top:12px;
}

</style>

</head>

<body>

<div class="box">

    <h2>
        <i class="fa fa-lock"></i>
        Verify OTP
    </h2>

    <!-- OTP SHOW -->
    <div class="otp-show">
        Your OTP is : <?php echo $_SESSION['otp']; ?>
    </div>

    <!-- OTP INPUT -->
    <form method="POST">

        <div class="input-box">

            <i class="fa fa-key"></i>

            <input type="text"
                   name="otp"
                   placeholder="Enter OTP"
                   required>

        </div>

        <button name="verify">
            Verify OTP
        </button>

    </form>

    <?php
    if($error != ""){
        echo "<p class='error'>$error</p>";
    }
    ?>

</div>

</body>
</html>