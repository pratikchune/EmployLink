<?php
include 'db.php';
session_start();

/* LOGIN CHECK */

if(!isset($_SESSION['student'])){
    header("Location: student_login.php");
    exit();
}

$email = $_SESSION['student'];

/* FETCH DATA */

$query = mysqli_query($conn,
"SELECT * FROM users WHERE email='$email'");

$row = mysqli_fetch_assoc($query);

$message = "";

/* CREATE PROFILE FOLDER */

if(!file_exists("profiles")){
    mkdir("profiles",0777,true);
}

/* UPDATE PROFILE */

if(isset($_POST['update'])){

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $college = $_POST['college'];
    $course = $_POST['course'];
    $passing_year = $_POST['passing_year'];
    $skills = $_POST['skills'];

    /* PHOTO UPLOAD */

    if($_FILES['photo']['name'] != ""){

        $photo_name = $_FILES['photo']['name'];
        $tmp_name = $_FILES['photo']['tmp_name'];

        $folder = "profiles/" . time() . "_" . $photo_name;

        move_uploaded_file($tmp_name,$folder);

    }else{

        $folder = $row['photo'];
    }

    /* UPDATE QUERY */

    $update = mysqli_query($conn,

    "UPDATE users SET

    name='$name',
    phone='$phone',
    college='$college',
    course='$course',
    passing_year='$passing_year',
    skills='$skills',
    photo='$folder'

    WHERE email='$email'");

    if($update){

        $message = "Profile Updated Successfully";

        $query = mysqli_query($conn,
        "SELECT * FROM users WHERE email='$email'");

        $row = mysqli_fetch_assoc($query);

    }else{

        $message = "Profile Update Failed";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Student Profile</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#f5f7fa;
}

/* NAVBAR */

.navbar{
    background:#4a148c;
    padding:18px 60px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.navbar h2{
    color:white;
    font-size:24px;
}

.nav-links a{
    color:white;
    text-decoration:none;
    margin-left:25px;
    font-size:15px;
    transition:0.3s;
}

.nav-links a:hover{
    color:#ffcc80;
}

/* MAIN */

.main{
    padding:40px 20px;
}

/* FORM BOX */

.form-box{
    max-width:500px;
    margin:auto;
    background:white;
    padding:35px 30px;
    border-radius:18px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    border-top:5px solid #ff6f00;
}

/* TITLE */

.form-box h2{
    text-align:center;
    color:#4a148c;
    margin-bottom:25px;
    font-size:32px;
}

/* PHOTO */

.photo-container{
    position:relative;
    width:130px;
    margin:auto;
    margin-bottom:25px;
}

.profile-photo{
    width:130px;
    height:130px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid #ff6f00;
}

.default-icon{
    width:130px;
    height:130px;
    border-radius:50%;
    background:#4a148c;
    display:flex;
    justify-content:center;
    align-items:center;
}

.default-icon i{
    font-size:55px;
    color:white;
}

/* CAMERA */

.camera-icon{
    position:absolute;
    bottom:5px;
    right:0;
    width:38px;
    height:38px;
    border-radius:50%;
    background:#ff6f00;
    display:flex;
    justify-content:center;
    align-items:center;
    color:white;
    cursor:pointer;
    border:3px solid white;
}

/* SINGLE COLUMN */

.form-grid{
    display:flex;
    flex-direction:column;
    gap:18px;
}

/* INPUT */

.input-box label{
    display:block;
    margin-bottom:8px;
    color:#4a148c;
    font-weight:600;
    font-size:14px;
}

.input-box input,
.input-box textarea{
    width:100%;
    padding:13px;
    border:1px solid #ddd;
    border-radius:10px;
    outline:none;
    font-size:14px;
}

.input-box input:focus,
.input-box textarea:focus{
    border-color:#ff6f00;
    box-shadow:0 0 5px rgba(255,111,0,0.3);
}

textarea{
    height:100px;
    resize:none;
}

/* BUTTON */

.btn{
    width:100%;
    padding:14px;
    border:none;
    border-radius:10px;
    background:#ff6f00;
    color:white;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    margin-top:18px;
}

.btn:hover{
    background:#4a148c;
}

/* MESSAGE */

.message{
    margin-top:20px;
    background:#ede7f6;
    color:#4a148c;
    padding:14px;
    border-radius:8px;
    text-align:center;
    font-weight:600;
}

/* FOOTER */

.footer{
    margin-top:40px;
    background:#4a148c;
    color:white;
    text-align:center;
    padding:15px;
}

/* RESPONSIVE */

@media(max-width:700px){

    .navbar{
        flex-direction:column;
        gap:10px;
        text-align:center;
    }

    .form-box{
        width:95%;
        padding:25px 20px;
    }
}

</style>

</head>

<body>

<!-- NAVBAR -->

<div class="navbar">

    <h2>Placement System</h2>

    <div class="nav-links">

        <a href="dashboard.php">
            <i class="fa fa-home"></i> Home
        </a>

        <a href="view_companies_student.php">
            <i class="fa fa-building"></i> Companies
        </a>

        <a href="my_applications.php">
            <i class="fa fa-file"></i> Applications
        </a>

        <a href="profile.php">
            <i class="fa fa-user"></i> Profile
        </a>

        <a href="logout.php">
            <i class="fa fa-sign-out-alt"></i> Logout
        </a>

    </div>

</div>

<!-- MAIN -->

<div class="main">

    <div class="form-box">

        <h2>Edit Profile</h2>

        <!-- PHOTO -->

        <div class="photo-container">

            <?php if($row['photo'] != ""){ ?>

                <img src="<?php echo $row['photo']; ?>"
                class="profile-photo">

            <?php } else { ?>

                <div class="default-icon">
                    <i class="fa fa-user"></i>
                </div>

            <?php } ?>

            <label for="photo-upload" class="camera-icon">
                <i class="fa fa-camera"></i>
            </label>

        </div>

        <!-- FORM -->

        <form method="POST" enctype="multipart/form-data">

            <input type="file"
            id="photo-upload"
            name="photo"
            accept="image/*"
            hidden>

            <div class="form-grid">

                <div class="input-box">

                    <label>Full Name</label>

                    <input type="text"
                    name="name"
                    value="<?php echo $row['name']; ?>"
                    required>

                </div>

                <div class="input-box">

                    <label>Email Address</label>

                    <input type="email"
                    value="<?php echo $row['email']; ?>"
                    disabled>

                </div>

                <div class="input-box">

                    <label>Phone Number</label>

                    <input type="text"
                    name="phone"
                    value="<?php echo $row['phone']; ?>">

                </div>

                <div class="input-box">

                    <label>College Name</label>

                    <input type="text"
                    name="college"
                    value="<?php echo $row['college']; ?>">

                </div>

                <div class="input-box">

                    <label>Course</label>

                    <input type="text"
                    name="course"
                    value="<?php echo $row['course']; ?>">

                </div>

                <div class="input-box">

                    <label>Passing Year</label>

                    <input type="date"
                    name="passing_year"
                    value="<?php echo $row['passing_year']; ?>">

                </div>

                <div class="input-box">

                    <label>Skills</label>

                    <textarea
                    name="skills"><?php echo $row['skills']; ?></textarea>

                </div>

            </div>

            <button class="btn" name="update">
                Update Profile
            </button>

        </form>

        <?php if($message != ""){ ?>

            <div class="message">
                <?php echo $message; ?>
            </div>

        <?php } ?>

    </div>

</div>

<!-- FOOTER -->

<div class="footer">

    © 2026 Placement Management System

</div>

</body>
</html>