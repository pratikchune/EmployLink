<?php
include 'db.php';
session_start();

/* =========================
   LOGIN CHECK
========================= */

if(!isset($_SESSION['student'])){
    header("Location: student_login.php");
    exit();
}

/* =========================
   JOB CHECK
========================= */

if(!isset($_GET['id'])){
    die("Invalid Job");
}

$email = $_SESSION['student'];
$job_id = $_GET['id'];

$message = "";
$icon = "fa-file";

/* =========================
   APPLY FORM SUBMIT
========================= */

if(isset($_POST['apply'])){

    $fullname = $_POST['fullname'];
    $type = $_POST['type'];
    $skills = $_POST['skills'];

    /* CREATE RESUME FOLDER */

    if(!file_exists("resumes")){
        mkdir("resumes",0777,true);
    }

    /* RESUME UPLOAD */

    if(isset($_FILES['resume']) && $_FILES['resume']['error']==0){

        $resume_name = $_FILES['resume']['name'];
        $tmp_name = $_FILES['resume']['tmp_name'];

        $filename = time()."_".str_replace(" ","_",$resume_name);

        $folder = "resumes/".$filename;

        move_uploaded_file($tmp_name,$folder);

        $resume = $folder;

    }else{

        $resume = "";
        $message = "Resume Upload Failed";
        $icon = "fa-xmark";
    }

    /* TYPE CHECK */

    if($type=="Fresher"){

        $passing_year = $_POST['passing_year'];
        $experience = "0 Months";

    }else{

        $experience = $_POST['experience'];
        $passing_year = "";
    }

    /* CHECK EXISTING APPLICATION */

    $check = mysqli_query($conn,

    "SELECT * FROM applications
     WHERE student_email='$email'
     AND job_id='$job_id'");

    if(mysqli_num_rows($check)>0){

        $row = mysqli_fetch_assoc($check);

        if($row['status']=="Pending"){

            $message = "Application Already Pending";
            $icon = "fa-clock";

        }

        else if($row['status']=="Approved"){

            $message = "You Are Already Selected";
            $icon = "fa-check";

        }

        else if($row['status']=="Rejected"){

            mysqli_query($conn,

            "UPDATE applications SET

            status='Pending',
            fullname='$fullname',
            resume='$resume',
            experience='$experience',
            passing_year='$passing_year',
            type='$type',
            skills='$skills'

            WHERE student_email='$email'
            AND job_id='$job_id'");

            $message = "Reapplied Successfully";
            $icon = "fa-rotate-right";
        }

    }else{

        /* INSERT APPLICATION */

        $insert = mysqli_query($conn,

        "INSERT INTO applications

        (student_email,
        job_id,
        fullname,
        resume,
        experience,
        passing_year,
        type,
        skills,
        status)

        VALUES

        ('$email',
        '$job_id',
        '$fullname',
        '$resume',
        '$experience',
        '$passing_year',
        '$type',
        '$skills',
        'Pending')");

        if($insert){

            $message = "Application Submitted Successfully";
            $icon = "fa-check";

        }else{

            $message = "Application Failed";
            $icon = "fa-xmark";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Apply Job</title>

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
    background:#f4f6fb;
    color:#333;
}

/* ===== NAVBAR ===== */

.navbar{
    width:100%;
    background:linear-gradient(90deg,#4a148c,#6a1b9a);
    padding:18px 50px;
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
    margin-left:20px;
    font-size:14px;
    transition:0.3s;
}

.nav-links a:hover{
    color:#ffd180;
}

/* ===== MAIN ===== */

.main{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px 20px;
}

/* ===== FORM BOX ===== */

.box{
    width:100%;
    max-width:500px;
    background:white;
    padding:32px 28px;
    border-radius:22px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    position:relative;
}

.box::before{
    content:'';
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:5px;
    background:linear-gradient(to right,#ff9800,#4a148c);
    border-radius:22px 22px 0 0;
}

.box h2{
    text-align:center;
    font-size:30px;
    color:#4a148c;
    margin-bottom:25px;
}

/* ===== FORM GRID ===== */

.form-grid{
    display:flex;
    flex-direction:column;
    gap:18px;
}

/* ===== PROFILE ICON ===== */

.profile-icon{
    width:95px;
    height:95px;
    background:#4a148c;
    color:white;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:0 auto 25px;
    font-size:40px;
}

/* ===== INPUT BOX ===== */

.input-box label{
    display:block;
    margin-bottom:7px;
    color:#4a148c;
    font-weight:600;
    font-size:14px;
}

.input-box input,
.input-box textarea,
.input-box select{
    width:100%;
    padding:13px 14px;
    border:1px solid #ddd;
    border-radius:12px;
    outline:none;
    background:#fafafa;
    font-size:14px;
    transition:0.3s;
}

.input-box input:focus,
.input-box textarea:focus,
.input-box select:focus{
    border-color:#ff9800;
    background:white;
    box-shadow:0 0 8px rgba(255,152,0,0.2);
}

textarea{
    height:100px;
    resize:none;
}

/* ===== BUTTON ===== */

.btn{
    width:100%;
    padding:13px;
    border:none;
    border-radius:12px;
    background:linear-gradient(90deg,#ff9800,#ff6f00);
    color:white;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    margin-top:10px;
    transition:0.3s;
}

.btn:hover{
    background:linear-gradient(90deg,#4a148c,#6a1b9a);
    transform:translateY(-2px);
}

/* ===== BACK BUTTON ===== */

.back-btn{
    display:block;
    text-align:center;
    margin-top:18px;
    text-decoration:none;
    color:#4a148c;
    font-weight:600;
    font-size:14px;
}

.back-btn:hover{
    color:#ff9800;
}

/* ===== MESSAGE ===== */

.message-box{
    position:fixed;
    top:20px;
    right:20px;
    width:280px;
    background:white;
    padding:18px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,0.12);
    border-left:5px solid #ff9800;
    text-align:center;
}

.message-box i{
    font-size:24px;
    color:#4a148c;
    margin-bottom:10px;
}

.message-box div{
    font-size:14px;
    font-weight:600;
}

/* ===== RESPONSIVE ===== */

@media(max-width:700px){

    .navbar{
        flex-direction:column;
        gap:10px;
        text-align:center;
        padding:18px;
    }

    .nav-links a{
        display:inline-block;
        margin:8px;
    }

    .box{
        padding:25px 20px;
    }
}

</style>

</head>

<body>

<!-- NAVBAR -->

<div class="navbar">

    <h2>
        <i class="fa fa-graduation-cap"></i>
        Placement System
    </h2>

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

    <div class="box">

        <div class="profile-icon">
            <i class="fa fa-user"></i>
        </div>

        <h2>Apply For Job</h2>

        <form method="POST" enctype="multipart/form-data">

            <div class="form-grid">

                <!-- FULL NAME -->

                <div class="input-box">

                    <label>Full Name</label>

                    <input type="text"
                    name="fullname"
                    placeholder="Enter your full name"
                    required>

                </div>

                <!-- TYPE -->

                <div class="input-box">

                    <label>Candidate Type</label>

                    <select name="type"
                    id="type"
                    onchange="toggleFields()"
                    required>

                        <option value="">Select Type</option>

                        <option value="Fresher">
                            Fresher
                        </option>

                        <option value="Experienced">
                            Experienced
                        </option>

                    </select>

                </div>

                <!-- PASSING YEAR -->

                <div class="input-box"
                id="passingYearBox"
                style="display:none;">

                    <label>Passing Year</label>

                    <input type="date"
                    name="passing_year">

                </div>

                <!-- EXPERIENCE -->

                <div class="input-box"
                id="experienceBox"
                style="display:none;">

                    <label>Experience</label>

                    <input type="text"
                    name="experience"
                    placeholder="Example: 6 Months">

                </div>

                <!-- RESUME -->

                <div class="input-box">

                    <label>Upload Resume</label>

                    <input type="file"
                    name="resume"
                    required>

                </div>

                <!-- SKILLS -->

                <div class="input-box">

                    <label>Skills</label>

                    <textarea
                    name="skills"
                    placeholder="Enter your skills..."
                    required></textarea>

                </div>

            </div>

            <!-- BUTTON -->

            <button class="btn" name="apply">

                <i class="fa fa-paper-plane"></i>
                Apply Now

            </button>

        </form>

        <!-- BACK -->

        <a class="back-btn"
        href="view_companies_student.php">

            ← Back To Companies

        </a>

    </div>

</div>

<!-- MESSAGE -->

<?php if($message != ""){ ?>

<div class="message-box">

    <i class="fa <?php echo $icon; ?>"></i>

    <div>
        <?php echo $message; ?>
    </div>

</div>

<?php } ?>

<script>

function toggleFields(){

    var type = document.getElementById("type").value;

    document.getElementById("passingYearBox").style.display =
    (type == "Fresher") ? "block" : "none";

    document.getElementById("experienceBox").style.display =
    (type == "Experienced") ? "block" : "none";
}

</script>

</body>
</html>