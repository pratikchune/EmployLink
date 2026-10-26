from flask import Flask, render_template, request, redirect, url_for, session
import mysql.connector
from werkzeug.security import generate_password_hash, check_password_hash

from config import Config


# ============================================================
# FLASK APPLICATION
# ============================================================

app = Flask(__name__)
app.config.from_object(Config)


# ============================================================
# DATABASE CONNECTION
# ============================================================

def get_db_connection():

    return mysql.connector.connect(
        host=app.config["MYSQL_HOST"],
        user=app.config["MYSQL_USER"],
        password=app.config["MYSQL_PASSWORD"],
        database=app.config["MYSQL_DATABASE"],
        port=app.config["MYSQL_PORT"]
    )


# ============================================================
# HOME PAGE
# ============================================================

@app.route("/")
def home():

    return render_template("index.html")


# ============================================================
# REGISTER
# ============================================================

@app.route("/register", methods=["GET", "POST"])
def register():

    if request.method == "POST":

        full_name = request.form.get("full_name", "").strip()
        email = request.form.get("email", "").strip().lower()
        phone = request.form.get("phone", "").strip()
        password = request.form.get("password", "")
        role = request.form.get("role", "").strip().lower()

        # ----------------------------------------------------
        # VALIDATION
        # ----------------------------------------------------

        if not full_name or not email or not phone or not password or not role:

            return render_template(
                "register.html",
                error="Please fill in all fields."
            )

        allowed_roles = [
            "trainee",
            "provider",
            "employer"
        ]

        if role not in allowed_roles:

            return render_template(
                "register.html",
                error="Invalid account type."
            )

        if len(password) < 6:

            return render_template(
                "register.html",
                error="Password must contain at least 6 characters."
            )

        connection = None
        cursor = None

        try:

            connection = get_db_connection()

            cursor = connection.cursor()

            # ------------------------------------------------
            # CHECK EMAIL
            # ------------------------------------------------

            cursor.execute(
                """
                SELECT user_id
                FROM users
                WHERE email = %s
                """,
                (email,)
            )

            existing_email = cursor.fetchone()

            if existing_email:

                return render_template(
                    "register.html",
                    error="An account with this email already exists."
                )

            # ------------------------------------------------
            # CHECK PHONE
            # ------------------------------------------------

            cursor.execute(
                """
                SELECT user_id
                FROM users
                WHERE phone = %s
                """,
                (phone,)
            )

            existing_phone = cursor.fetchone()

            if existing_phone:

                return render_template(
                    "register.html",
                    error="This mobile number is already registered."
                )

            # ------------------------------------------------
            # HASH PASSWORD
            # ------------------------------------------------

            password_hash = generate_password_hash(password)

            # ------------------------------------------------
            # INSERT USER
            # ------------------------------------------------

            cursor.execute(
                """
                INSERT INTO users
                (
                    full_name,
                    email,
                    phone,
                    password_hash,
                    role
                )
                VALUES (%s, %s, %s, %s, %s)
                """,
                (
                    full_name,
                    email,
                    phone,
                    password_hash,
                    role
                )
            )

            user_id = cursor.lastrowid

            # ------------------------------------------------
            # CREATE TRAINEE PROFILE
            # ------------------------------------------------

            if role == "trainee":

                skilltrack_id = "ST" + str(user_id).zfill(6)

                cursor.execute(
                    """
                    INSERT INTO trainees
                    (
                        user_id,
                        skilltrack_id,
                        employment_status,
                        profile_completed
                    )
                    VALUES (%s, %s, %s, %s)
                    """,
                    (
                        user_id,
                        skilltrack_id,
                        "unknown",
                        False
                    )
                )

            # ------------------------------------------------
            # CREATE TRAINING PROVIDER PROFILE
            # ------------------------------------------------

            elif role == "provider":

                cursor.execute(
                    """
                    INSERT INTO training_providers
                    (
                        user_id,
                        provider_name,
                        verification_status
                    )
                    VALUES (%s, %s, %s)
                    """,
                    (
                        user_id,
                        full_name,
                        "pending"
                    )
                )

            # ------------------------------------------------
            # CREATE EMPLOYER PROFILE
            # ------------------------------------------------

            elif role == "employer":

                cursor.execute(
                    """
                    INSERT INTO employers
                    (
                        user_id,
                        company_name,
                        verification_status
                    )
                    VALUES (%s, %s, %s)
                    """,
                    (
                        user_id,
                        full_name,
                        "pending"
                    )
                )

            connection.commit()

            return redirect(
                url_for(
                    "login",
                    registered="success"
                )
            )

        except mysql.connector.Error as error:

            if connection:
                connection.rollback()

            return render_template(
                "register.html",
                error=f"Registration failed: {error}"
            )

        finally:

            if cursor:
                cursor.close()

            if connection:
                connection.close()

    return render_template("register.html")


# ============================================================
# LOGIN
# ============================================================

@app.route("/login", methods=["GET", "POST"])
def login():

    registered = request.args.get("registered")

    if request.method == "POST":

        email = request.form.get(
            "email",
            ""
        ).strip().lower()

        password = request.form.get(
            "password",
            ""
        )

        if not email or not password:

            return render_template(
                "login.html",
                error="Please enter email and password."
            )

        connection = None
        cursor = None

        try:

            connection = get_db_connection()

            cursor = connection.cursor(
                dictionary=True
            )

            cursor.execute(
                """
                SELECT
                    user_id,
                    full_name,
                    email,
                    phone,
                    password_hash,
                    role,
                    account_status
                FROM users
                WHERE email = %s
                """,
                (email,)
            )

            user = cursor.fetchone()

            if not user:

                return render_template(
                    "login.html",
                    error="Invalid email or password."
                )

            if user["account_status"] != "active":

                return render_template(
                    "login.html",
                    error="Your account is not active."
                )

            if not check_password_hash(
                user["password_hash"],
                password
            ):

                return render_template(
                    "login.html",
                    error="Invalid email or password."
                )

            # ------------------------------------------------
            # CREATE SESSION
            # ------------------------------------------------

            session["user_id"] = user["user_id"]
            session["full_name"] = user["full_name"]
            session["email"] = user["email"]
            session["role"] = user["role"]

            return redirect(
                url_for("dashboard")
            )

        except mysql.connector.Error as error:

            return render_template(
                "login.html",
                error=f"Login failed: {error}"
            )

        finally:

            if cursor:
                cursor.close()

            if connection:
                connection.close()

    return render_template(
        "login.html",
        registered=registered
    )


# ============================================================
# DASHBOARD
# ============================================================

@app.route("/dashboard")
def dashboard():

    # --------------------------------------------------------
    # LOGIN CHECK
    # --------------------------------------------------------

    if "user_id" not in session:

        return redirect(
            url_for("login")
        )

    # --------------------------------------------------------
    # TRAINEE DASHBOARD
    # --------------------------------------------------------

    if session.get("role") != "trainee":

        return redirect(
            url_for("home")
        )

    connection = None
    cursor = None

    try:

        connection = get_db_connection()

        cursor = connection.cursor(
            dictionary=True
        )

        # ----------------------------------------------------
        # GET TRAINEE
        # ----------------------------------------------------

        cursor.execute(
            """
            SELECT

                u.user_id,
                u.full_name,
                u.email,
                u.phone,

                t.trainee_id,
                t.skilltrack_id,
                t.date_of_birth,
                t.gender,
                t.education_level,
                t.qualification,
                t.address,
                t.career_goal,
                t.experience_years,
                t.employment_status,
                t.profile_completed,

                d.district_name

            FROM users u

            INNER JOIN trainees t
                ON u.user_id = t.user_id

            LEFT JOIN districts d
                ON t.district_id = d.district_id

            WHERE u.user_id = %s
            """,
            (session["user_id"],)
        )

        trainee = cursor.fetchone()

        if not trainee:

            return """
            <div style="
                font-family:Arial;
                text-align:center;
                padding:60px;
            ">

                <h2>Trainee Profile Not Found</h2>

                <p>
                    Your trainee profile does not exist.
                </p>

                <a href="/logout">
                    Logout
                </a>

            </div>
            """

        trainee_id = trainee["trainee_id"]

        # ----------------------------------------------------
        # SKILL COUNT
        # ----------------------------------------------------

        cursor.execute(
            """
            SELECT COUNT(*) AS total
            FROM trainee_skills
            WHERE trainee_id = %s
            """,
            (trainee_id,)
        )

        skill_count = cursor.fetchone()["total"]

        # ----------------------------------------------------
        # TOTAL TRAININGS
        # ----------------------------------------------------

        cursor.execute(
            """
            SELECT COUNT(*) AS total
            FROM enrollments
            WHERE trainee_id = %s
            """,
            (trainee_id,)
        )

        training_count = cursor.fetchone()["total"]

        # ----------------------------------------------------
        # COMPLETED TRAININGS
        # ----------------------------------------------------

        cursor.execute(
            """
            SELECT COUNT(*) AS total
            FROM enrollments
            WHERE trainee_id = %s
            AND enrollment_status = 'completed'
            """,
            (trainee_id,)
        )

        completed_training_count = cursor.fetchone()["total"]

        # ----------------------------------------------------
        # CERTIFICATE COUNT
        # ----------------------------------------------------

        cursor.execute(
            """
            SELECT COUNT(*) AS total

            FROM certificates c

            INNER JOIN enrollments e
                ON c.enrollment_id = e.enrollment_id

            WHERE e.trainee_id = %s
            """,
            (trainee_id,)
        )

        certificate_count = cursor.fetchone()["total"]

        # ----------------------------------------------------
        # PROFILE COMPLETION
        # ----------------------------------------------------

        fields = [

            trainee["full_name"],
            trainee["email"],
            trainee["phone"],
            trainee["skilltrack_id"],
            trainee["education_level"],
            trainee["qualification"],
            trainee["district_name"],
            trainee["career_goal"]

        ]

        completed_fields = sum(
            1 for field in fields
            if field
        )

        profile_completion = int(
            (
                completed_fields /
                len(fields)
            ) * 100
        )

        # ----------------------------------------------------
        # TRAINEE SKILLS
        # ----------------------------------------------------

        cursor.execute(
            """
            SELECT

                s.skill_name,
                ts.proficiency_level

            FROM trainee_skills ts

            INNER JOIN skills s
                ON ts.skill_id = s.skill_id

            WHERE ts.trainee_id = %s

            ORDER BY s.skill_name
            """,
            (trainee_id,)
        )

        skills = cursor.fetchall()

        # ----------------------------------------------------
        # RECENT COMPLETED TRAININGS
        # ----------------------------------------------------

        cursor.execute(
            """
            SELECT

                e.enrollment_id,
                e.course_id,
                e.completion_date,

                c.course_name,

                p.provider_name

            FROM enrollments e

            INNER JOIN training_courses c
                ON e.course_id = c.course_id

            INNER JOIN training_providers p
                ON c.provider_id = p.provider_id

            WHERE e.trainee_id = %s

            AND e.enrollment_status = 'completed'

            ORDER BY e.completion_date DESC

            LIMIT 5
            """,
            (trainee_id,)
        )

        recent_trainings = cursor.fetchall()

        # ----------------------------------------------------
        # JOBS
        # ----------------------------------------------------

        jobs = []

        recommended_jobs = len(jobs)

        # ----------------------------------------------------
        # RENDER DASHBOARD
        # ----------------------------------------------------

        return render_template(
            "dashboard.html",

            trainee=trainee,

            skills=skills,

            skill_count=skill_count,

            training_count=training_count,

            completed_training_count=completed_training_count,

            certificate_count=certificate_count,

            profile_completion=profile_completion,

            recent_trainings=recent_trainings,

            jobs=jobs,

            recommended_jobs=recommended_jobs
        )

    except mysql.connector.Error as error:

        return f"""
        <div style="
            font-family:Arial;
            padding:50px;
        ">

            <h2>Database Error</h2>

            <p>{error}</p>

        </div>
        """

    finally:

        if cursor:
            cursor.close()

        if connection:
            connection.close()


# ============================================================
# PROFILE
# ============================================================

@app.route("/profile", methods=["GET", "POST"])
def profile():

    if "user_id" not in session:

        return redirect(
            url_for("login")
        )

    if session.get("role") != "trainee":

        return redirect(
            url_for("home")
        )

    connection = None
    cursor = None

    try:

        connection = get_db_connection()

        cursor = connection.cursor(
            dictionary=True
        )

        # ----------------------------------------------------
        # GET PROFILE
        # ----------------------------------------------------

        cursor.execute(
            """
            SELECT

                u.full_name,
                u.email,
                u.phone,

                t.trainee_id,
                t.skilltrack_id,
                t.date_of_birth,
                t.gender,
                t.education_level,
                t.qualification,
                t.address,
                t.career_goal,
                t.experience_years,
                t.employment_status,
                t.profile_completed,
                t.district_id,

                d.district_name

            FROM users u

            INNER JOIN trainees t
                ON u.user_id = t.user_id

            LEFT JOIN districts d
                ON t.district_id = d.district_id

            WHERE u.user_id = %s
            """,
            (session["user_id"],)
        )

        trainee = cursor.fetchone()

        if not trainee:

            return """
            <h2>Trainee profile not found.</h2>
            """

        # ----------------------------------------------------
        # UPDATE PROFILE
        # ----------------------------------------------------

        if request.method == "POST":

            full_name = request.form.get(
                "full_name",
                ""
            ).strip()

            phone = request.form.get(
                "phone",
                ""
            ).strip()

            date_of_birth = (
                request.form.get(
                    "date_of_birth"
                )
                or None
            )

            gender = (
                request.form.get(
                    "gender"
                )
                or None
            )

            education_level = request.form.get(
                "education_level"
            )

            qualification = request.form.get(
                "qualification"
            )

            district_id = (
                request.form.get(
                    "district_id"
                )
                or None
            )

            address = request.form.get(
                "address"
            )

            career_goal = request.form.get(
                "career_goal"
            )

            experience_years = (
                request.form.get(
                    "experience_years"
                )
                or 0
            )

            # ------------------------------------------------
            # UPDATE USERS
            # ------------------------------------------------

            cursor.execute(
                """
                UPDATE users

                SET
                    full_name = %s,
                    phone = %s

                WHERE user_id = %s
                """,
                (
                    full_name,
                    phone,
                    session["user_id"]
                )
            )

            # ------------------------------------------------
            # UPDATE TRAINEES
            # ------------------------------------------------

            cursor.execute(
                """
                UPDATE trainees

                SET

                    date_of_birth = %s,
                    gender = %s,
                    education_level = %s,
                    qualification = %s,
                    district_id = %s,
                    address = %s,
                    career_goal = %s,
                    experience_years = %s,
                    profile_completed = TRUE

                WHERE user_id = %s
                """,
                (
                    date_of_birth,
                    gender,
                    education_level,
                    qualification,
                    district_id,
                    address,
                    career_goal,
                    experience_years,
                    session["user_id"]
                )
            )

            connection.commit()

            session["full_name"] = full_name

            return redirect(
                url_for("profile")
            )

        # ----------------------------------------------------
        # DISTRICTS
        # ----------------------------------------------------

        cursor.execute(
            """
            SELECT

                district_id,
                district_name

            FROM districts

            ORDER BY district_name
            """
        )

        districts = cursor.fetchall()

        # ----------------------------------------------------
        # SKILLS
        # ----------------------------------------------------

        cursor.execute(
            """
            SELECT

                s.skill_id,
                s.skill_name,
                ts.proficiency_level

            FROM trainee_skills ts

            INNER JOIN skills s
                ON ts.skill_id = s.skill_id

            WHERE ts.trainee_id = %s

            ORDER BY s.skill_name
            """,
            (trainee["trainee_id"],)
        )

        skills = cursor.fetchall()

        return render_template(
            "profile.html",

            trainee=trainee,

            districts=districts,

            skills=skills
        )

    except mysql.connector.Error as error:

        if connection:
            connection.rollback()

        return f"""
        <h2>Database Error</h2>
        <p>{error}</p>
        """

    finally:

        if cursor:
            cursor.close()

        if connection:
            connection.close()

@app.route('/skills', methods=['GET', 'POST'])
def skills():

    if 'trainee_id' not in session:
        return redirect(url_for('login'))

    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    # Get trainee
    cursor.execute("""
        SELECT *
        FROM trainees
        WHERE trainee_id = %s
    """, (session['trainee_id'],))

    trainee = cursor.fetchone()

    # Get all available skills
    cursor.execute("""
        SELECT
            skill_id,
            skill_name,
            skill_category
        FROM skills
        ORDER BY skill_name ASC
    """)

    available_skills = cursor.fetchall()

    # Get trainee's current skills
    cursor.execute("""
        SELECT
            ts.trainee_skill_id,
            s.skill_name,
            s.skill_category,
            ts.proficiency_level,
            ts.years_experience,
            ts.is_verified
        FROM trainee_skills ts
        JOIN skills s
            ON ts.skill_id = s.skill_id
        WHERE ts.trainee_id = %s
        ORDER BY s.skill_name ASC
    """, (session['trainee_id'],))

    trainee_skills = cursor.fetchall()

    cursor.close()
    conn.close()

    return render_template(
        'skills.html',
        trainee=trainee,
        available_skills=available_skills,
        trainee_skills=trainee_skills
    )


# ============================================================
# MY TRAININGS
# ============================================================

@app.route("/my-trainings")
def my_trainings():

    # --------------------------------------------------------
    # LOGIN CHECK
    # --------------------------------------------------------

    if "user_id" not in session:

        return redirect(
            url_for("login")
        )

    # --------------------------------------------------------
    # ROLE CHECK
    # --------------------------------------------------------

    if session.get("role") != "trainee":

        return redirect(
            url_for("home")
        )

    connection = None
    cursor = None

    try:

        connection = get_db_connection()

        cursor = connection.cursor(
            dictionary=True
        )

        # ----------------------------------------------------
        # FIND TRAINEE
        # ----------------------------------------------------

        cursor.execute(
            """
            SELECT trainee_id

            FROM trainees

            WHERE user_id = %s
            """,
            (session["user_id"],)
        )

        trainee = cursor.fetchone()

        if not trainee:

            return """
            <div style="
                font-family:Arial;
                text-align:center;
                padding:60px;
            ">

                <h2>Trainee Profile Not Found</h2>

                <p>
                    Your trainee profile does not exist.
                </p>

                <a href="/dashboard">
                    Back to Dashboard
                </a>

            </div>
            """

        trainee_id = trainee["trainee_id"]

        # ----------------------------------------------------
        # GET COMPLETED TRAININGS
        # ----------------------------------------------------

        cursor.execute(
            """
            SELECT

                e.enrollment_id,
                e.course_id,

                e.enrollment_date,
                e.start_date,
                e.completion_date,
                e.enrollment_status,

                e.attendance_percentage,
                e.assessment_score,

                c.course_name,
                c.course_description,
                c.duration_months,
                c.training_mode,
                c.eligibility,

                p.provider_name,

                cert.certificate_id,
                cert.certificate_number,
                cert.issue_date,
                cert.certificate_status

            FROM enrollments e

            INNER JOIN training_courses c
                ON e.course_id = c.course_id

            INNER JOIN training_providers p
                ON c.provider_id = p.provider_id

            LEFT JOIN certificates cert
                ON e.enrollment_id = cert.enrollment_id

            WHERE e.trainee_id = %s

            AND e.enrollment_status = 'completed'

            ORDER BY e.completion_date DESC
            """,
            (trainee_id,)
        )

        trainings = cursor.fetchall()

        # ----------------------------------------------------
        # RENDER PAGE
        # ----------------------------------------------------

        return render_template(
            "my_trainings.html",
            trainings=trainings
        )

    except mysql.connector.Error as error:

        return f"""
        <div style="
            font-family:Arial;
            padding:50px;
        ">

            <h2>Database Error</h2>

            <p>{error}</p>

            <a href="/dashboard">
                Back to Dashboard
            </a>

        </div>
        """

    finally:

        if cursor:
            cursor.close()

        if connection:
            connection.close()


# ============================================================
# STEP 8
# COURSE DETAILS
# ============================================================

@app.route("/training/course/<int:course_id>")
def course_details(course_id):

    # --------------------------------------------------------
    # LOGIN CHECK
    # --------------------------------------------------------

    if "user_id" not in session:

        return redirect(
            url_for("login")
        )

    # --------------------------------------------------------
    # ROLE CHECK
    # --------------------------------------------------------

    if session.get("role") != "trainee":

        return redirect(
            url_for("home")
        )

    connection = None
    cursor = None

    try:

        connection = get_db_connection()

        cursor = connection.cursor(
            dictionary=True
        )

        # ----------------------------------------------------
        # FIND TRAINEE
        # ----------------------------------------------------

        cursor.execute(
            """
            SELECT

                trainee_id,
                skilltrack_id

            FROM trainees

            WHERE user_id = %s
            """,
            (session["user_id"],)
        )

        trainee = cursor.fetchone()

        if not trainee:

            return """
            <div style="
                font-family:Arial;
                text-align:center;
                padding:60px;
            ">

                <h2>Trainee Profile Not Found</h2>

                <p>
                    Your trainee profile has not been created.
                </p>

                <a href="/dashboard">
                    Back to Dashboard
                </a>

            </div>
            """

        trainee_id = trainee["trainee_id"]

        # ----------------------------------------------------
        # GET COMPLETED COURSE
        # ----------------------------------------------------

        cursor.execute(
            """
            SELECT

                e.enrollment_id,
                e.enrollment_date,
                e.start_date,
                e.completion_date,
                e.enrollment_status,
                e.attendance_percentage,
                e.assessment_score,

                c.course_id,
                c.course_name,
                c.course_description,
                c.duration_months,
                c.training_mode,
                c.eligibility,
                c.certification_available,

                p.provider_name,
                p.address AS provider_address,
                p.contact_person,

                cert.certificate_id,
                cert.certificate_number,
                cert.issue_date,
                cert.certificate_url,
                cert.verification_code,
                cert.certificate_status

            FROM enrollments e

            INNER JOIN training_courses c
                ON e.course_id = c.course_id

            INNER JOIN training_providers p
                ON c.provider_id = p.provider_id

            LEFT JOIN certificates cert
                ON e.enrollment_id = cert.enrollment_id

            WHERE e.course_id = %s

            AND e.trainee_id = %s

            AND e.enrollment_status = 'completed'

            LIMIT 1
            """,
            (
                course_id,
                trainee_id
            )
        )

        training = cursor.fetchone()

        # ----------------------------------------------------
        # COURSE NOT FOUND
        # ----------------------------------------------------

        if not training:

            return """
            <div style="
                font-family:Arial;
                text-align:center;
                padding:60px;
            ">

                <h2>Training Not Found</h2>

                <p>
                    This training either does not exist
                    or has not been completed by you.
                </p>

                <a href="/my-trainings">
                    ← Back to My Trainings
                </a>

            </div>
            """

        # ----------------------------------------------------
        # GET SKILLS TAUGHT IN COURSE
        # ----------------------------------------------------

        cursor.execute(
            """
            SELECT

                s.skill_id,
                s.skill_name,
                s.skill_category

            FROM course_skills cs

            INNER JOIN skills s
                ON cs.skill_id = s.skill_id

            WHERE cs.course_id = %s

            ORDER BY s.skill_name
            """,
            (training["course_id"],)
        )

        skills = cursor.fetchall()

        # ----------------------------------------------------
        # RENDER COURSE DETAILS
        # ----------------------------------------------------

        return render_template(
            "course_details.html",

            training=training,

            skills=skills
        )

    except mysql.connector.Error as error:

        return f"""
        <div style="
            font-family:Arial;
            padding:50px;
        ">

            <h2>Database Error</h2>

            <p>{error}</p>

            <a href="/my-trainings">
                Back to My Trainings
            </a>

        </div>
        """

    finally:

        if cursor:
            cursor.close()

        if connection:
            connection.close()


# ============================================================
# LOGOUT
# ============================================================

@app.route("/logout")
def logout():

    session.clear()

    return redirect(
        url_for("login")
    )


# ============================================================
# DATABASE TEST
# ============================================================

@app.route("/test-db")
def test_db():

    connection = None
    cursor = None

    try:

        connection = get_db_connection()

        cursor = connection.cursor()

        cursor.execute(
            "SELECT DATABASE()"
        )

        database = cursor.fetchone()[0]

        return f"""
        <div style="
            font-family:Arial;
            text-align:center;
            padding:60px;
        ">

            <h1>
                Database Connected Successfully!
            </h1>

            <p>
                Database: {database}
            </p>

            <p>
                SkillTrack Flask application is connected
                to MySQL successfully.
            </p>

            <a href="/">
                Go to Home
            </a>

        </div>
        """

    except Exception as error:

        return f"""
        <div style="
            font-family:Arial;
            text-align:center;
            padding:60px;
        ">

            <h1>
                Database Connection Failed
            </h1>

            <p>
                {error}
            </p>

        </div>
        """

    finally:

        if cursor:
            cursor.close()

        if connection:
            connection.close()


# ============================================================
# RUN APPLICATION
# ============================================================

if __name__ == "__main__":

    app.run(
        debug=True
    )