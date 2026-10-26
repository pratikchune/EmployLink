<?php
include 'db.php';
session_start();

/* LOGIN CHECK */
if(!isset($_SESSION['company_id'])){
    header("Location: company_login.php");
    exit();
}

$company_id = intval($_SESSION['company_id']);

/* UPDATE STATUS */
if(isset($_GET['action']) && isset($_GET['id'])){

    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if($action == "approve"){
        mysqli_query($conn,
        "UPDATE applications SET status='Approved' WHERE id=$id");
    }

    if($action == "reject"){
        mysqli_query($conn,
        "UPDATE applications SET status='Rejected' WHERE id=$id");
    }

    header("Location: view_applications_company.php");
    exit();
}

/* FETCH APPLICATIONS */
$sql = "SELECT
    applications.id AS app_id,
    applications.fullname,
    applications.student_email,
    applications.resume,
    applications.status,
    applications.apply_date,
    jobs.job_role
FROM applications
INNER JOIN jobs ON applications.job_id = jobs.company_id
INNER JOIN companies ON jobs.company_id = companies.id
WHERE companies.id = $company_id
ORDER BY applications.apply_date DESC
";

$result = mysqli_query($conn,$sql);

if(!$result){
    die("Query Failed : " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Job Applications</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

/* YOUR CSS KEPT EXACTLY SAME */
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
    padding:50px;
}

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

.table-box{
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 2px 12px rgba(0,0,0,0.08);
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
    color:#555;
    font-size:14px;
}

tr:hover{
    background:#fafafa;
}

.status{
    padding:7px 14px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
    display:inline-block;
}

.Pending{ background:#ffe082; color:#333; }
.Approved{ background:#4caf50; color:white; }
.Rejected{ background:#e53935; color:white; }

.btn{
    display:inline-block;
    padding:8px 14px;
    border-radius:5px;
    text-decoration:none;
    font-size:13px;
    color:white;
    margin-right:5px;
    transition:0.3s;
}

.approve{ background:#4caf50; }
.reject{ background:#e53935; }

.btn:hover{
    transform:translateY(-2px);
}

.email-link{
    color:#4a148c;
    text-decoration:none;
    font-weight:500;
}

.email-link:hover{
    color:#ff6f00;
    text-decoration:underline;
}

.no-data{
    text-align:center;
    padding:20px;
    color:#4a148c;
    font-weight:600;
}

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

.footer{
    background:#4a148c;
    color:white;
    text-align:center;
    padding:15px;
    margin-top:40px;
}

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

    .btn{
        display:block;
        margin-bottom:6px;
        text-align:center;
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

    <div class="title">Job Applications 📄</div>
    <div class="subtitle">Manage student applications and update status.</div>

    <div class="table-box">

        <table>

            <tr>
                <th>Student Name</th>
                <th>Email</th>
                <th>Job Role</th>
                <th>Resume</th>
                <th>Status</th>
                <th>Apply Date</th>
                <th>Action</th>
            </tr>

            <?php if(mysqli_num_rows($result) > 0){ ?>

                <?php while($row = mysqli_fetch_assoc($result)){ ?>

                <tr>

                    <td><?php echo $row['fullname']; ?></td>

                    <td>
                        <a class="email-link"
                        href="mailto:<?php echo $row['student_email']; ?>">
                            <?php echo $row['student_email']; ?>
                        </a>
                    </td>

                    <td><?php echo $row['job_role']; ?></td>
                <td>
                        <?php if(!empty($row['resume'])) { ?>
                            <a href="<?php echo $row['resume']; ?>" target="_blank" class="btn approve">
                                View Resume
                            </a>
                        <?php } else { ?>
                            No Resume
                        <?php } ?>
                    </td>
                    <td>
                        <span class="status <?php echo $row['status']; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>

                    <td>
                        <?php
                        echo date("d M Y", strtotime($row['apply_date']));
                        ?>
                    </td>

                    <td>

                        <?php if($row['status']=="Pending"){ ?>

                            <a class="btn approve"
                            href="?action=approve&id=<?php echo $row['app_id']; ?>">
                                Approve
                            </a>

                            <a class="btn reject"
                            href="?action=reject&id=<?php echo $row['app_id']; ?>">
                                Reject
                            </a>

                        <?php } ?>

                        <?php if($row['status']=="Approved"){ ?>

                            <a class="btn reject"
                            href="?action=reject&id=<?php echo $row['app_id']; ?>">
                                Reject
                            </a>

                        <?php } ?>

                        <?php if($row['status']=="Rejected"){ ?>

                            <a class="btn approve"
                            href="?action=approve&id=<?php echo $row['app_id']; ?>">
                                Approve
                            </a>

                        <?php } ?>

                    </td>

                </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>
                    <td colspan="6" class="no-data">
                        No Applications Found
                    </td>
                </tr>

            <?php } ?>

        </table>

    </div>

</div>

<div class="cards">

    <div class="card">
        <h3>📄 Easy Management</h3>
        <p>Approve or reject applications quickly.</p>
    </div>

    <div class="card">
        <h3>👨‍🎓 Student Details</h3>
        <p>View student applications and job roles easily.</p>
    </div>

    <div class="card">
        <h3>⚡ Fast Hiring</h3>
        <p>Manage placements with a smart dashboard.</p>
    </div>

</div>

<div class="footer">
    <p>© 2026 Placement Management System</p>
</div>

</body>
</html>