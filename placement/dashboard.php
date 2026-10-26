<?php
include 'db.php';
session_start();

if(!isset($_SESSION['student'])){
    header("Location: student_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Student Dashboard</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
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
    padding:50px;
}

/* WELCOME BOX */

.welcome-box{
    background:white;
    padding:35px;
    border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
    margin-bottom:40px;
    border-left:6px solid #ff6f00;
}

.welcome-box h1{
    color:#4a148c;
    margin-bottom:10px;
    font-size:36px;
}

.welcome-box p{
    color:#555;
    font-size:16px;
    line-height:1.7;
}

/* CARDS */

.cards{
    display:flex;
    justify-content:center;
    gap:25px;
    flex-wrap:wrap;
}

.card{
    width:300px;
    background:white;
    padding:30px;
    border-radius:12px;
    text-align:center;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
    transition:0.3s;
    border-top:4px solid #ff6f00;
}

.card:hover{
    transform:translateY(-5px);
}

.card i{
    font-size:45px;
    color:#4a148c;
    margin-bottom:20px;
}

.card h3{
    color:#4a148c;
    margin-bottom:12px;
}

.card p{
    color:#666;
    line-height:1.6;
    margin-bottom:25px;
    font-size:14px;
}

/* BUTTON */

.card a{
    display:inline-block;
    padding:11px 22px;
    background:#ff6f00;
    color:white;
    text-decoration:none;
    border-radius:5px;
    transition:0.3s;
    font-weight:500;
}

.card a:hover{
    background:#4a148c;
}

/* FOOTER */

.footer{
    background:#4a148c;
    color:white;
    text-align:center;
    padding:15px;
    margin-top:60px;
}

/* RESPONSIVE */

@media(max-width:900px){

    .navbar{
        flex-direction:column;
        gap:10px;
        text-align:center;
    }

    .main{
        padding:25px;
    }

    .welcome-box h1{
        font-size:28px;
    }

    .cards{
        flex-direction:column;
        align-items:center;
    }

    .card{
        width:100%;
        max-width:350px;
    }
}

</style>

</head>

<body>

<!-- NAVBAR -->

<div class="navbar">

    <h2>Placement System</h2>

    <div class="nav-links">

        <a href="home.php">
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

    <!-- WELCOME -->

    <div class="welcome-box">

        <h1>
            Welcome,<?php echo $_SESSION['student']; ?> 🚀
        </h1>

        <p>
            Manage your placement journey, explore top companies,
            track applications and build your career with ease.
        </p>

    </div>

    <!-- CARDS -->

    <div class="cards">

        <!-- COMPANY -->

        <div class="card">

            <i class="fa fa-building"></i>

            <h3>Company Listings</h3>

            <p>
                Explore all available placement companies
                and apply for jobs easily.
            </p>

            <a href="view_companies_student.php">
                View Companies
            </a>

        </div>

        <!-- APPLICATION -->

        <div class="card">

            <i class="fa fa-file"></i>

            <h3>My Applications</h3>

            <p>
                Check your application status,
                approvals and placement updates.
            </p>

            <a href="my_applications.php">
                View Applications
            </a>

        </div>

        <!-- PROFILE -->

        <div class="card">

            <i class="fa fa-user"></i>

            <h3>Student Profile</h3>

            <p>
                Manage your profile, skills,
                resume and personal information.
            </p>

            <a href="profile.php">
                Open Profile
            </a>

        </div>

    </div>

</div>

<!-- FOOTER -->

<div class="footer">

    <p>© 2026 Placement Management System</p>

</div>

</body>
</html>