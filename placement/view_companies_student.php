<?php
include 'db.php';
session_start();

if(!isset($_SESSION['student'])){
    header("Location: student_login.php");
    exit();
}

/* FETCH COMPANIES + JOBS */

$result = mysqli_query($conn,

"SELECT 
    companies.id,
    companies.name,
    jobs.job_role,
    jobs.package
FROM companies
LEFT JOIN jobs 
ON companies.id = jobs.company_id"

);

if(!$result){
    die("Query Failed : " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>

<title>View Companies</title>

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

/* TITLE */

.title{
    font-size:40px;
    color:#4a148c;
    margin-bottom:12px;
    font-weight:600;
}

.subtitle{
    color:#666;
    margin-bottom:35px;
    font-size:16px;
}

/* TABLE BOX */

.table-box{
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 2px 12px rgba(0,0,0,0.08);
    overflow-x:auto;
}

/* TABLE */

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#4a148c;
    color:white;
    padding:15px;
    text-align:left;
    font-size:15px;
}

td{
    padding:15px;
    border-bottom:1px solid #eee;
    color:#555;
    font-size:14px;
}

tr:hover{
    background:#fafafa;
}

/* APPLY BUTTON */

.apply-btn{
    display:inline-block;
    padding:10px 18px;
    background:#ff6f00;
    color:white;
    text-decoration:none;
    border-radius:5px;
    transition:0.3s;
    font-weight:500;
}

.apply-btn:hover{
    background:#4a148c;
    transform:translateY(-2px);
}

/* NO DATA */

.no-data{
    text-align:center;
    padding:20px;
    color:#4a148c;
    font-weight:600;
}

/* CARDS */

.cards{
    display:flex;
    justify-content:center;
    gap:25px;
    padding:60px 20px;
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
    margin-top:40px;
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

    .title{
        font-size:30px;
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

    <div class="title">
        Shape Your Career 🚀
    </div>

    <div class="subtitle">
        Explore available companies and apply for your dream job.
    </div>

    <!-- TABLE -->

    <div class="table-box">

        <table>

            <tr>
                
                <th>Company Name</th>
                <th>Job Role</th>
                <th>Package</th>
                <th>Action</th>
            </tr>

            <?php if(mysqli_num_rows($result) > 0){ ?>

                <?php while($row = mysqli_fetch_assoc($result)){ ?>

                <tr>


                    <td>
                        <?php echo $row['name']; ?>
                    </td>

                    <td>
                        <?php echo $row['job_role'] ?? 'Not Available'; ?>
                    </td>

                    <td>
                        ₹ <?php echo $row['package'] ?? 'Not Available'; ?>
                    </td>

                    <td>

                        <a class="apply-btn"
                        href="apply.php?id=<?php echo $row['id']; ?>">

                            Apply

                        </a>

                    </td>

                </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>

                    <td colspan="5" class="no-data">

                        No Companies Available

                    </td>

                </tr>

            <?php } ?>

        </table>

    </div>

</div>

<!-- FEATURES -->

<div class="cards">

    <div class="card">
        <h3>📄 Easy Apply</h3>
        <p>Apply to companies quickly with one click.</p>
    </div>

    <div class="card">
        <h3>🏢 Top Recruiters</h3>
        <p>Find opportunities from trusted companies.</p>
    </div>

    <div class="card">
        <h3>📊 Career Growth</h3>
        <p>Track your applications and placement progress.</p>
    </div>

</div>

<!-- FOOTER -->

<div class="footer">
    <p>© 2026 Placement Management System</p>
</div>

</body>
</html>