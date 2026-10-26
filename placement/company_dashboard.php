<?php
session_start();

if(!isset($_SESSION['company_id'])){
    header("Location: company_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Company Dashboard</title>

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
    padding:70px 50px;
}

/* HERO SECTION */

.hero{
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:white;
    padding:45px;
    border-radius:15px;
    box-shadow:0 2px 12px rgba(0,0,0,0.1);
    margin-bottom:50px;
    flex-wrap:wrap;
    border-left:6px solid #ff6f00;
}

.hero-text{
    max-width:600px;
}

.hero-text h1{
    font-size:45px;
    color:#4a148c;
    margin-bottom:20px;
}

.hero-text span{
    color:#ff6f00;
}

.hero-text p{
    color:#555;
    font-size:17px;
    line-height:1.8;
}

.hero img{
    width:280px;
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
        padding:30px 20px;
    }

    .hero{
        flex-direction:column;
        text-align:center;
        padding:30px;
    }

    .hero img{
        width:220px;
        margin-top:25px;
    }

    .hero-text h1{
        font-size:34px;
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

    <h2>
        <i class="fa fa-building"></i>
        Company Panel
    </h2>

    <div class="nav-links">

        <a href="company_dashboard.php">
            <i class="fa fa-home"></i>
            Dashboard
        </a>

        <a href="add_job_company.php">
            <i class="fa fa-plus"></i>
            Add Job
        </a>

        <a href="view_applications_company.php">
            <i class="fa fa-file"></i>
            Applications
        </a>

        <a href="company_profile.php">
            <i class="fa fa-user"></i>
            Profile
        </a>

        <a href="company_logout.php">
            <i class="fa fa-sign-out-alt"></i>
            Logout
        </a>

    </div>

</div>

<!-- MAIN -->

<div class="main">

    <!-- HERO SECTION -->

    <div class="hero">

        <div class="hero-text">

            <h1>
                Welcome,
                <span><?php echo $_SESSION['company_name']; ?></span> 🚀
            </h1>

            <p>
                Manage your company profile, post new job opportunities,
                and track student applications easily with our smart
                placement management platform.
            </p>

        </div>

        <img src="https://cdn-icons-png.flaticon.com/512/1995/1995574.png">

    </div>

    <!-- CARDS -->

    <div class="cards">

        <!-- ADD JOB -->

        <div class="card">

            <i class="fa fa-plus"></i>

            <h3>Add Job</h3>

            <p>
                Post new job openings and placement opportunities
                for students easily.
            </p>

            <a href="add_job_company.php">
                Add Job
            </a>

        </div>

        <!-- APPLICATIONS -->

        <div class="card">

            <i class="fa fa-file"></i>

            <h3>Applications</h3>

            <p>
                View all student applications,
                resumes and hiring details.
            </p>

            <a href="view_applications_company.php">
                View Applications
            </a>

        </div>

        <!-- PROFILE -->

        <div class="card">

            <i class="fa fa-building"></i>

            <h3>Company Profile</h3>

            <p>
                Manage company information,
                contact details and profile settings.
            </p>

            <a href="company_profile.php">
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