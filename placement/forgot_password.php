<?php
include 'db.php';
session_start();

$message = "";

/* =========================
   STEP 1: VERIFY PHONE
========================= */
if(isset($_POST['verify'])) {

    $phone = $_POST['phone'];

    $query = mysqli_query($conn,
    "SELECT * FROM users WHERE phone='$phone'");

    if(mysqli_num_rows($query) > 0) {

        $_SESSION['reset_phone'] = $phone;
        $message = "Phone verified successfully. Now set new password.";

    } else {
        $message = "Phone not found!";
    }
}

/* =========================
   STEP 2: RESET PASSWORD
========================= */
if(isset($_POST['reset'])) {

    if(isset($_SESSION['reset_phone'])) {

        $phone = $_SESSION['reset_phone'];
        $newpass = $_POST['newpass'];

        mysqli_query($conn,
        "UPDATE users SET password='$newpass' WHERE phone='$phone'");

        unset($_SESSION['reset_phone']);

        // ✅ Redirect to login page after success
        header("Location: student_login.php?reset=success");
        exit();

    } else {
        $message = "Please verify phone first!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>

<style>
body{
    font-family:Poppins;
    background:#1d2671;
    color:white;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.box{
    background:rgba(255,255,255,0.1);
    padding:30px;
    border-radius:15px;
    width:350px;
    text-align:center;
    box-shadow:0 10px 40px rgba(0,0,0,0.4);
}

input{
    width:100%;
    padding:10px;
    margin-top:10px;
    border-radius:10px;
    border:none;
    outline:none;
}

button{
    margin-top:15px;
    padding:10px;
    width:100%;
    background:#ffd369;
    border:none;
    border-radius:20px;
    cursor:pointer;
    font-weight:bold;
}

button:hover{
    background:white;
}

.msg{
    color:#ffd369;
    margin-top:15px;
}
</style>

</head>
<body>

<div class="box">

<h2>Forgot Password</h2>

<!-- STEP 1: VERIFY PHONE -->
<?php if(!isset($_SESSION['reset_phone'])) { ?>

    <form method="POST">
        <input type="text" name="phone" placeholder="Enter your phone number" required>
        <button name="verify">Verify</button>
    </form>

<?php } else { ?>

<!-- STEP 2: RESET PASSWORD -->
    <form method="POST">
        <input type="text" name="newpass" placeholder="Enter new password" required>
        <button name="reset">Update Password</button>
    </form>

<?php } ?>

<!-- MESSAGE -->
<?php if($message != "") { ?>
    <p class="msg"><?php echo $message; ?></p>
<?php } ?>

</div>

</body>
</html>