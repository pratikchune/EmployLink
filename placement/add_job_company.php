<?php
include 'db.php';
session_start();

if(!isset($_SESSION['company_id'])){
    header("Location: company_login.php");
    exit();
}

$company_id = $_SESSION['company_id'];
$msg = "";

if(isset($_POST['add'])){

    $job_role   = $_POST['job_role'];
    $description = $_POST['description'];
    $skills     = $_POST['skills'];
    $package    = $_POST['package'];
    $location   = $_POST['location'];

    mysqli_query($conn,
    "INSERT INTO jobs(company_id,job_role,description,skills,package,location)
     VALUES('$company_id','$job_role','$description','$skills','$package','$location')");

    $msg = "Job Added Successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Add Job</title>

<!-- GOOGLE FONT -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#f5f7fa;
    color:#333;
    overflow-x:hidden;
}

/* NAVBAR */

.navbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:18px 60px;
    background:#4a148c;
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
    display:flex;
    justify-content:center;
    align-items:center;
    padding:70px 20px;
}

/* FORM BOX */

.form-box{
    width:100%;
    max-width:550px;
    background:white;
    padding:35px;
    border-radius:12px;
    box-shadow:0 2px 12px rgba(0,0,0,0.1);
    border-top:4px solid #ff6f00;
}

/* TITLE */

.form-box h2{
    text-align:center;
    color:#4a148c;
    margin-bottom:25px;
    font-size:32px;
}

.form-box h2 i{
    color:#ff6f00;
}

/* INPUTS */

input,
textarea{
    width:100%;
    padding:14px;
    margin:12px 0;
    border:1px solid #ddd;
    border-radius:8px;
    outline:none;
    font-size:15px;
    transition:0.3s;
}

input:focus,
textarea:focus{
    border-color:#4a148c;
    box-shadow:0 0 5px rgba(74,20,140,0.2);
}

textarea{
    resize:none;
}

#description{
    height:140px;
}

#skills{
    height:90px;
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
    font-weight:500;
    cursor:pointer;
    transition:0.3s;
    margin-top:10px;
}

button:hover{
    background:#4a148c;
    transform:translateY(-2px);
}

/* MESSAGE */

.msg{
    text-align:center;
    margin-top:18px;
    color:green;
    font-weight:600;
}

/* FEATURES */

.cards{
    display:flex;
    justify-content:center;
    gap:25px;
    padding:20px 20px 60px;
    flex-wrap:wrap;
}

.card{
    width:260px;
    background:white;
    padding:25px;
    border-radius:10px;
    text-align:center;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
    transition:0.3s;
    border-top:4px solid #ff6f00;
}

.card:hover{
    transform:translateY(-5px);
}

.card h3{
    color:#4a148c;
    margin-bottom:12px;
}

.card p{
    color:#666;
    line-height:1.5;
}

/* FOOTER */

.footer{
    background:#4a148c;
    color:white;
    text-align:center;
    padding:15px;
}

/* RESPONSIVE */

@media(max-width:900px){

    .navbar{
        flex-direction:column;
        gap:10px;
        text-align:center;
    }

    .nav-links{
        margin-top:10px;
    }

    .main{
        padding:40px 15px;
    }

    .form-box{
        padding:25px;
    }

    .form-box h2{
        font-size:26px;
    }

    .cards{
        flex-direction:column;
        align-items:center;
    }
}

</style>

</head>

<body>

<!-- NAVBAR -->

<div class="navbar">

    <h2>
        <i class="fa fa-building"></i>
        Company Panel
    </h2>

    <div class="nav-links">

        <a href="company_dashboard.php">
            <i class="fa fa-home"></i> Dashboard
        </a>

        <a href="add_job_company.php">
            <i class="fa fa-plus"></i> Add Job
        </a>

        <a href="view_applications_company.php">
            <i class="fa fa-file"></i> Applications
        </a>

        <a href="company_profile.php">
            <i class="fa fa-user"></i> Profile
        </a>

        <a href="company_logout.php">
            <i class="fa fa-sign-out-alt"></i> Logout
        </a>

    </div>

</div>

<!-- MAIN -->

<div class="main">

    <div class="form-box">

        <h2>
            <i class="fa fa-briefcase"></i>
            Add New Job
        </h2>

        <form method="POST">

            <input type="text"
                   name="job_role"
                   placeholder="Enter Job Role"
                   required>

            <textarea name="description"
                      id="description"
                      placeholder="Enter Job Description"
                      required></textarea>

            <textarea name="skills"
                      id="skills"
                      placeholder="Enter Required Skills"
                      required></textarea>

            <input type="text"
                   name="package"
                   placeholder="Enter Salary Package"
                   required>

            <input type="text"
                   name="location"
                   placeholder="Enter Job Location"
                   required>

            <button name="add">
                <i class="fa fa-plus"></i>
                Add Job
            </button>

        </form>

        <?php
        if($msg != ""){
            echo "<p class='msg'>$msg</p>";
        }
        ?>

    </div>

</div>


<!-- FOOTER -->

<div class="footer">
    <p>© 2026 Placement Management System</p>
</div>

</body>
</html>