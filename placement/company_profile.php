<?php
include 'db.php';
session_start();

if(!isset($_SESSION['company_id'])){
    header("Location: company_login.php");
    exit();
}

$company_id = $_SESSION['company_id'];

$query = mysqli_query($conn,
"SELECT * FROM companies WHERE id='$company_id'");

$company = mysqli_fetch_assoc($query);

$message = "";

/* UPDATE PROFILE */

if(isset($_POST['update'])){

    $name     = $_POST['name'];
    $hr_name  = $_POST['hr_name'];
    $email    = $_POST['email'];
    $phone    = $_POST['phone'];
    $website  = $_POST['website'];
    $location = $_POST['location'];

    $update = mysqli_query($conn,
    "UPDATE companies SET
    name='$name',
    hr_name='$hr_name',
    email='$email',
    phone='$phone',
    website='$website',
    location='$location'
    WHERE id='$company_id'");

    if($update){

        $message = "Company Profile Updated Successfully";

        $query = mysqli_query($conn,
        "SELECT * FROM companies WHERE id='$company_id'");

        $company = mysqli_fetch_assoc($query);

    }else{

        $message = "Profile Update Failed: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Company Profile</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

/* YOUR CSS IS SAME */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#eef2f7;
}

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

.main{
    padding:50px 20px;
}

.form-box{
    max-width:550px;
    margin:auto;
    background:white;
    padding:40px;
    border-radius:22px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    position:relative;
    overflow:hidden;
}

.form-box::before{
    content:"";
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:8px;
    background:linear-gradient(90deg,#ff6f00,#ff9800);
}

.company-header{
    text-align:center;
    margin-bottom:30px;
}

.company-badge{
    width:80px;
    height:80px;
    margin:auto;
    border-radius:22px;
    background:linear-gradient(135deg,#4a148c,#7b1fa2);
    display:flex;
    justify-content:center;
    align-items:center;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
    margin-bottom:18px;
}

.company-badge i{
    font-size:34px;
    color:white;
}

.company-header h1{
    font-size:34px;
    color:#4a148c;
    font-weight:700;
    letter-spacing:1px;
    margin-bottom:8px;
}

.company-header p{
    color:#777;
    font-size:14px;
    font-weight:500;
}

.form-grid{
    display:flex;
    flex-direction:column;
    gap:18px;
}

.input-box label{
    display:block;
    margin-bottom:8px;
    color:#4a148c;
    font-weight:600;
    font-size:14px;
}

.input-box input{
    width:100%;
    padding:14px;
    border:1px solid #ddd;
    border-radius:12px;
    outline:none;
    font-size:14px;
    background:#fafafa;
    transition:0.3s;
}

.input-box input:focus{
    border-color:#ff6f00;
    background:white;
    box-shadow:0 0 8px rgba(255,111,0,0.2);
}

.btn{
    width:100%;
    padding:15px;
    border:none;
    border-radius:12px;
    background:linear-gradient(90deg,#ff6f00,#ff9800);
    color:white;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    margin-top:10px;
    transition:0.3s;
}

.btn:hover{
    background:linear-gradient(90deg,#4a148c,#6a1b9a);
    transform:translateY(-2px);
}

.message{
    margin-top:20px;
    background:#ede7f6;
    color:#4a148c;
    padding:14px;
    border-radius:10px;
    text-align:center;
    font-weight:600;
}

.footer{
    margin-top:50px;
    background:#4a148c;
    color:white;
    text-align:center;
    padding:16px;
    font-size:14px;
}

@media(max-width:700px){
    .navbar{
        flex-direction:column;
        gap:12px;
        text-align:center;
        padding:18px 20px;
    }

    .nav-links{
        display:flex;
        flex-wrap:wrap;
        justify-content:center;
        gap:10px;
    }

    .form-box{
        width:95%;
        padding:30px 20px;
    }

    .company-header h1{
        font-size:28px;
    }
}

</style>

</head>

<body>

<div class="navbar">

    <h2>
        <i class="fa fa-building"></i>
        Company Panel
    </h2>

    <div class="nav-links">

        <a href="company_dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
        <a href="add_job_company.php"><i class="fa fa-plus"></i> Add Job</a>
        <a href="view_applications_company.php"><i class="fa fa-file"></i> Applications</a>
        <a href="company_profile.php"><i class="fa fa-user"></i> Profile</a>
        <a href="company_logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>

    </div>

</div>

<div class="main">

    <div class="form-box">

        <div class="company-header">

            <div class="company-badge">
                <i class="fa fa-building"></i>
            </div>

            <h1>
                <?php echo $company['name']; ?>
            </h1>

            <!-- CHANGED: removed HR name, now showing location -->
            <p>
                <i class="fa fa-map-marker-alt"></i>
                <?php echo $company['location']; ?>
            </p>

        </div>

        <form method="POST">

            <div class="form-grid">

                <div class="input-box">
                    <label>Company Name</label>
                    <input type="text" name="name"
                    value="<?php echo $company['name']; ?>" required>
                </div>

                <div class="input-box">
                    <label>HR / Recruiter Name</label>
                    <input type="text" name="hr_name"
                    value="<?php echo $company['hr_name']; ?>" required>
                </div>

                <div class="input-box">
                    <label>Email Address</label>
                    <input type="email" name="email"
                    value="<?php echo $company['email']; ?>" required>
                </div>

                <div class="input-box">
                    <label>Phone Number</label>
                    <input type="text" name="phone"
                    value="<?php echo $company['phone']; ?>">
                </div>

                <div class="input-box">
                    <label>Website</label>
                    <input type="text" name="website"
                    value="<?php echo $company['website']; ?>">
                </div>

                <div class="input-box">
                    <label>Location</label>
                    <input type="text" name="location"
                    value="<?php echo $company['location']; ?>">
                </div>

            </div>

            <button class="btn" name="update">
                <i class="fa fa-save"></i>
                Update Company Profile
            </button>

        </form>

        <?php if($message != ""){ ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
        <?php } ?>

    </div>

</div>

<div class="footer">
    © 2026 Placement Management System
</div>

</body>
</html>