<!DOCTYPE html>
<html>
<head>
    <title>Placement Management System</title>


    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

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

/* HERO */

.hero{
    display:flex;
    justify-content:space-around;
    align-items:center;
    padding:70px 50px;
    background:white;
}

.hero-text{
    max-width:500px;
}

.hero-text h1{
    font-size:48px;
    margin-bottom:20px;
    color:#4a148c;
}

.hero-text span{
    color:#ff6f00;
}

.hero-text p{
    font-size:17px;
    line-height:1.7;
    color:#555;
    margin-bottom:30px;
}

/* BUTTONS */

.buttons a{
    display:inline-block;
    padding:12px 24px;
    margin-right:15px;
    background:#ff6f00;
    color:white;
    text-decoration:none;
    border-radius:5px;
    transition:0.3s;
    font-weight:500;
}

.buttons a:hover{
    background:#4a148c;
}

/* HERO IMAGE */

.hero img{
    width:350px;
}

/* CARDS */

.cards{
    display:flex;
    justify-content:center;
    gap:25px;
    padding:60px 30px;
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

    .hero{
        flex-direction:column;
        text-align:center;
    }

    .hero img{
        width:240px;
        margin-top:30px;
    }

    .cards{
        flex-direction:column;
        align-items:center;
    }

    .navbar{
        flex-direction:column;
        gap:10px;
    }
}

</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <h2>Placement System</h2>

    <div class="nav-links">
        <a href="student_login.php">Student Login</a>
        <a href="company_login.php">Company Login</a>
    </div>
</div>

<!-- HERO -->
<div class="hero">

    <div class="hero-text">
        <h1>Shape Your <span>Career</span> Journey 🚀</h1>

        <p>
            Discover opportunities, connect with top recruiters, and manage your
            placement journey with ease using our smart platform.
        </p>

        <div class="buttons">
            <a href="student_login.php">Student Login</a>
            <a href="company_login.php">Company Login</a>
        </div>
    </div>

    <!-- IMAGE -->
    <img src="man.png">

</div>

<!-- FEATURES -->
<div class="cards">

    <div class="card">
        <h3>📄 Easy Registration</h3>
        <p>Quick and simple student profile creation.</p>
    </div>

    <div class="card">
        <h3>🏢 Company Listings</h3>
        <p>Explore jobs from top recruiters.</p>
    </div>

    <div class="card">
        <h3>📊 Track Progress</h3>
        <p>Monitor your application status easily.</p>
    </div>

</div>

<!-- FOOTER -->
<div class="footer">
    <p>© 2026 Placement Management System</p>
</div>

</body>
</html>