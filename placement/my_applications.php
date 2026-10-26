<?php
include 'db.php';
session_start();

/* LOGIN CHECK */
if(!isset($_SESSION['student'])){
    header("Location: student_login.php");
    exit();
}

$email = mysqli_real_escape_string($conn, $_SESSION['student']);

/* =========================
   APPLICATION QUERY
========================= */

$sql = "SELECT 
    applications.id AS app_id,
    applications.status,
    applications.apply_date,
    applications.student_email,

    users.name AS student_name,

    companies.name AS company_name,
    jobs.job_role,
    jobs.package,
    jobs.location

FROM applications

LEFT JOIN jobs 
    ON applications.job_id = jobs.company_id

LEFT JOIN companies 
    ON jobs.company_id = companies.id

LEFT JOIN users
    ON applications.student_email = users.email

WHERE applications.student_email = '$email'

ORDER BY applications.apply_date DESC
";

$result = mysqli_query($conn, $sql);

if(!$result){
    die('Query Failed: ' . mysqli_error($conn));
}

$count = mysqli_num_rows($result);

/* =========================
   STATS
========================= */

$pending_count = mysqli_num_rows(mysqli_query($conn,
"SELECT * FROM applications
 WHERE student_email='$email'
 AND status='Pending'"));

$approved_count = mysqli_num_rows(mysqli_query($conn,
"SELECT * FROM applications
 WHERE student_email='$email'
 AND status='Approved'"));

$rejected_count = mysqli_num_rows(mysqli_query($conn,
"SELECT * FROM applications
 WHERE student_email='$email'
 AND status='Rejected'"));
?>

<!DOCTYPE html>
<html>
<head>

<title>My Applications</title>

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
    padding:40px;
}

/* PAGE TITLE */

.title{
    font-size:35px;
    color:#4a148c;
    margin-bottom:30px;
    font-weight:600;
}

/* STATS */

.stats{
    display:flex;
    gap:20px;
    margin-bottom:35px;
    flex-wrap:wrap;
}

.stat-card{
    flex:1;
    min-width:200px;
    background:white;
    padding:30px;
    border-radius:12px;
    text-align:center;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
    border-top:4px solid #ff6f00;
    transition:0.3s;
}

.stat-card:hover{
    transform:translateY(-5px);
}

.stat-card h3{
    color:#4a148c;
    font-size:32px;
    margin-bottom:10px;
}

.stat-card p{
    color:#666;
    font-size:15px;
}

/* TABLE */

.table-box{
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
    overflow-x:auto;
}

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
    color:#444;
    font-size:14px;
}

tr:hover{
    background:#faf7ff;
}

/* STATUS */

.status{
    padding:7px 15px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
    display:inline-block;
}

.pending{
    background:#ffe082;
    color:#333;
}

.approved{
    background:#4caf50;
    color:white;
}

.rejected{
    background:#e53935;
    color:white;
}

/* NO DATA */

.no-data{
    text-align:center;
    padding:25px;
    color:#4a148c;
    font-weight:600;
    font-size:16px;
}

/* FOOTER */

.footer{
    background:#4a148c;
    color:white;
    text-align:center;
    padding:15px;
    margin-top:50px;
}

/* RESPONSIVE */

@media(max-width:900px){

    .navbar{
        flex-direction:column;
        gap:10px;
        text-align:center;
    }

    .main{
        padding:20px;
    }

    .stats{
        flex-direction:column;
    }

    .title{
        font-size:28px;
        text-align:center;
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

    <div class="title">
        My Applications
    </div>

    <!-- STATS -->

    <div class="stats">

        <div class="stat-card">
            <h3><?php echo $count; ?></h3>
            <p>Total Applications</p>
        </div>

        <div class="stat-card">
            <h3><?php echo $pending_count; ?></h3>
            <p>Pending</p>
        </div>

        <div class="stat-card">
            <h3><?php echo $approved_count; ?></h3>
            <p>Approved</p>
        </div>

        <div class="stat-card">
            <h3><?php echo $rejected_count; ?></h3>
            <p>Rejected</p>
        </div>

    </div>

    <!-- TABLE -->

    <div class="table-box">

        <table>

            <tr>
            
                <th>Applicant Name</th>
                <th>Company</th>
                <th>Job Role</th>
                <th>Package</th>
                <th>Location</th>
                <th>Status</th>
            </tr>

            <?php if($count > 0){ ?>

                <?php while($row = mysqli_fetch_assoc($result)){ ?>

                <tr>

                    

                    <td>
                        <?php echo $row['student_name'] ?? 'N/A'; ?>
                    </td>

                    <td>
                        <?php echo $row['company_name'] ?? 'Not Found'; ?>
                    </td>

                    <td>
                        <?php echo $row['job_role'] ?? 'Not Found'; ?>
                    </td>

                    <td>
                        ₹ <?php echo $row['package'] ?? '0'; ?>
                    </td>

                    <td>
                        <?php echo $row['location'] ?? 'N/A'; ?>
                    </td>

                    <td>

                        <span class="status <?php echo strtolower($row['status']); ?>">

                            <?php echo $row['status']; ?>

                        </span>

                    </td>

                </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>

                    <td colspan="7" class="no-data">

                        No Applications Found

                    </td>

                </tr>

            <?php } ?>

        </table>

    </div>

</div>

<!-- FOOTER -->

<div class="footer">

    <p>© 2026 Placement Management System</p>

</div>

</body>
</html>