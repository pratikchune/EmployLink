from flask import (
    Flask, render_template, request, redirect,
    url_for, session, flash, abort
)
import mysql.connector
from mysql.connector import Error
from werkzeug.security import check_password_hash
from werkzeug.utils import secure_filename
from collections import Counter
from functools import wraps
from pathlib import Path
from uuid import uuid4
import os

app = Flask(__name__)
app.secret_key = os.environ.get(
    "FLASK_SECRET_KEY",
    "change-this-to-a-long-random-secret"
)

UPLOAD_FOLDER = Path(app.root_path) / "uploads" / "resumes"
ALLOWED_EXTENSIONS = {"pdf"}
app.config["MAX_CONTENT_LENGTH"] = 5 * 1024 * 1024


# ---------------- DATABASE CONNECTION ----------------

def get_db_connection():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password=os.environ.get("DB_PASSWORD", ""),
        database="placement_db",
        port=3306
    )


# ---------------- AUTHENTICATION HELPERS ----------------

def company_required(view):
    @wraps(view)
    def wrapped(*args, **kwargs):
        if "company_id" not in session:
            flash("Please log in to access the company dashboard.", "error")
            return redirect(url_for("company_login"))
        return view(*args, **kwargs)
    return wrapped


def password_matches(stored_password, entered_password):
    """Support Werkzeug password hashes and legacy plain-text records."""
    if not stored_password:
        return False

    try:
        if stored_password.startswith(("pbkdf2:", "scrypt:")):
            return check_password_hash(stored_password, entered_password)
    except (ValueError, TypeError):
        return False

    # Legacy compatibility only. Replace plain-text passwords with hashes.
    return stored_password == entered_password


# ---------------- HOMEPAGE ----------------

@app.route("/")
def index():
    return render_template("index.html")


@app.route("/company-applications")
def company_applications():
    if "company_id" not in session:
        return redirect(url_for("company_login"))

    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    try:
        cursor.execute("""
            SELECT
                a.id,
                a.fullname,
                a.email,
                a.phone,
                a.status,
                a.apply_date,
                a.resume,
                a.experience,
                a.passing_year,
                a.type,
                a.skills,
                j.job_role
            FROM applications AS a
            INNER JOIN jobs AS j ON a.job_id = j.id
            WHERE j.company_id = %s
            ORDER BY a.apply_date DESC
        """, (session["company_id"],))

        applications = cursor.fetchall()

    finally:
        cursor.close()
        conn.close()

    return render_template(
        "applications.html",
        applications=applications
    )

# ---------------- COMPANIES ----------------

@app.route("/companies")
def companies():
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    try:
        cursor.execute("""
            SELECT c.id, c.name, c.hr_name, c.email, c.website,
                   c.location, COUNT(j.id) AS job_count
            FROM companies c
            LEFT JOIN jobs j ON j.company_id = c.id
            GROUP BY c.id, c.name, c.hr_name, c.email,
                     c.website, c.location
            ORDER BY c.name
        """)
        company_list = cursor.fetchall()
        return render_template("companies.html", companies=company_list)
    finally:
        cursor.close()
        conn.close()


# ---------------- JOB LIST ----------------

@app.route("/jobs")
def jobs():
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    try:
        cursor.execute("""
            SELECT j.id, j.company_id, j.job_role, j.description,
                   j.skills, j.package, j.location,
                   c.name AS company_name
            FROM jobs j
            LEFT JOIN companies c ON c.id = j.company_id
            ORDER BY j.id DESC
        """)
        job_list = cursor.fetchall()
        return render_template("jobs.html", jobs=job_list)
    finally:
        cursor.close()
        conn.close()


# ---------------- COMPANY LOGIN ----------------

@app.route("/company-login", methods=["GET", "POST"])
def company_login():
    if "company_id" in session:
        return redirect(url_for("company_dashboard"))

    if request.method == "POST":
        email = request.form.get("email", "").strip().lower()
        password = request.form.get("password", "")

        if not email or not password:
            return render_template(
                "company_login.html",
                error="Enter both your email and password."
            )

        conn = None
        cursor = None

        try:
            conn = get_db_connection()
            cursor = conn.cursor(dictionary=True)

            cursor.execute("""
                SELECT id, name, email, password
                FROM companies
                WHERE LOWER(email) = %s
                LIMIT 1
            """, (email,))

            company = cursor.fetchone()

            if company and password_matches(company["password"], password):
                session.clear()
                session["company_id"] = company["id"]
                session["company_name"] = company["name"]
                session["company_email"] = company["email"]

                return redirect(url_for("company_dashboard"))

            return render_template(
                "company_login.html",
                error="Invalid email or password."
            )

        except Error:
            app.logger.exception("Company login database error")
            return render_template(
                "company_login.html",
                error="Could not connect to the database. Check your settings."
            )
        finally:
            if cursor:
                cursor.close()
            if conn and conn.is_connected():
                conn.close()

    return render_template("company_login.html", error=None)


# ---------------- COMPANY DASHBOARD ----------------

@app.route("/company-dashboard")
@company_required
def company_dashboard():
    company_id = session["company_id"]
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    try:
        cursor.execute("""
            SELECT id, job_role, description, skills, package, location
            FROM jobs
            WHERE company_id = %s
            ORDER BY id DESC
        """, (company_id,))
        company_jobs = cursor.fetchall()

        cursor.execute("""
            SELECT COUNT(*) AS total
            FROM applications
            WHERE company_name = %s
        """, (session.get("company_name", ""),))
        application_count = cursor.fetchone()["total"]

        return render_template(
            "company_dashboard.html",
            company_name=session.get("company_name", "Company"),
            company_email=session.get("company_email", ""),
            jobs=company_jobs,
            total_jobs=len(company_jobs),
            total_applications=application_count
        )
    finally:
        cursor.close()
        conn.close()


# ---------------- COMPANY LOGOUT ----------------

@app.route("/company-logout", methods=["POST"])
def company_logout():
    session.clear()
    flash("You have been logged out.", "success")
    return redirect(url_for("company_login"))


# ---------------- ANALYTICS ----------------

@app.route("/analytics")
def analytics():
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    try:
        cursor.execute("SELECT COUNT(*) AS total FROM companies")
        total_companies = cursor.fetchone()["total"]

        cursor.execute("SELECT COUNT(*) AS total FROM jobs")
        total_jobs = cursor.fetchone()["total"]

        cursor.execute("""
            SELECT COUNT(*) AS total FROM users
            WHERE LOWER(role) = 'student'
        """)
        total_students = cursor.fetchone()["total"]

        cursor.execute("SELECT COUNT(*) AS total FROM applications")
        total_applications = cursor.fetchone()["total"]

        cursor.execute("""
            SELECT status, COUNT(*) AS total
            FROM applications
            GROUP BY status
        """)
        status_rows = cursor.fetchall()

        cursor.execute("""
            SELECT c.name AS company_name, COUNT(j.id) AS total
            FROM companies c
            LEFT JOIN jobs j ON j.company_id = c.id
            GROUP BY c.id, c.name
            ORDER BY total DESC
            LIMIT 10
        """)
        company_rows = cursor.fetchall()

        cursor.execute("""
            SELECT DATE_FORMAT(apply_date, '%Y-%m') AS month,
                   COUNT(*) AS total
            FROM applications
            WHERE apply_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(apply_date, '%Y-%m')
            ORDER BY month
        """)
        month_rows = cursor.fetchall()

        cursor.execute("SELECT skills FROM jobs WHERE skills IS NOT NULL")
        skill_rows = cursor.fetchall()

        skill_counts = Counter()
        for row in skill_rows:
            # Count each skill at most once per job.
            skills = {
                part.strip().lower()
                for part in row["skills"].replace("|", ",")
                    .replace(";", ",").replace("\n", ",").split(",")
                if part.strip()
            }
            skill_counts.update(skills)

        top_skills = skill_counts.most_common(8)

        return render_template(
            "analytics.html",
            total_companies=total_companies,
            total_jobs=total_jobs,
            total_students=total_students,
            total_applications=total_applications,
            status_labels=[r["status"] or "Unknown" for r in status_rows],
            status_values=[r["total"] for r in status_rows],
            company_labels=[r["company_name"] for r in company_rows],
            company_values=[r["total"] for r in company_rows],
            month_labels=[r["month"] for r in month_rows],
            month_values=[r["total"] for r in month_rows],
            skill_labels=[s[0] for s in top_skills],
            skill_values=[s[1] for s in top_skills]
        )
    finally:
        cursor.close()
        conn.close()

@app.route("/company-profile", methods=["GET", "POST"])
def company_profile():
    if "company_id" not in session:
        return redirect(url_for("company_login"))

    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    cursor.execute(
        "SELECT * FROM companies WHERE id = %s",
        (session["company_id"],)
    )
    company = cursor.fetchone()

    cursor.close()
    conn.close()

    if not company:
        return "Company not found", 404

    return render_template(
        "company_profile.html",
        company=company
    )
# ---------------- APPLY FOR A JOB ----------------

@app.route("/apply/<int:job_id>", methods=["GET", "POST"])
def apply_job(job_id):
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    try:
        cursor.execute("""
            SELECT j.id, j.job_role, j.description, j.skills,
                   j.package, j.location, c.name AS company_name
            FROM jobs j
            LEFT JOIN companies c ON c.id = j.company_id
            WHERE j.id = %s
        """, (job_id,))
        job = cursor.fetchone()

        if not job:
            abort(404)

        if request.method == "GET":
            return render_template("apply.html", job=job)

        fullname = request.form.get("fullname", "").strip()
        email = request.form.get("email", "").strip()
        phone = request.form.get("phone", "").strip()
        experience = request.form.get("experience", "").strip()
        passing_year = request.form.get("passing_year", "").strip()
        skills = request.form.get("skills", "").strip()
        resume = request.files.get("resume")

        if not all([fullname, email, phone, passing_year, skills]):
            return render_template(
                "apply.html", job=job,
                error="Please fill in all required fields."
            )

        if not passing_year.isdigit() or not 1900 <= int(passing_year) <= 2100:
            return render_template(
                "apply.html", job=job,
                error="Enter a valid passing year."
            )

        if not resume or not resume.filename:
            return render_template(
                "apply.html", job=job, error="Please upload your PDF resume."
            )

        if Path(secure_filename(resume.filename)).suffix.lower() != ".pdf":
            return render_template(
                "apply.html", job=job, error="Only PDF resumes are accepted."
            )

        file_start = resume.stream.read(5)
        resume.stream.seek(0)
        if file_start != b"%PDF-":
            return render_template(
                "apply.html", job=job,
                error="The uploaded file does not appear to be a valid PDF."
            )

        UPLOAD_FOLDER.mkdir(parents=True, exist_ok=True)
        resume_filename = f"{uuid4().hex}.pdf"
        resume_path = UPLOAD_FOLDER / resume_filename

        resume.save(resume_path)

        try:
            cursor.execute("""
                INSERT INTO applications
                (job_id, student_email, status, apply_date, fullname,
                 email, phone, company_name, resume, experience,
                 passing_year, type, skills)
                VALUES (%s, %s, %s, NOW(), %s, %s, %s, %s, %s, %s, %s, %s, %s)
            """, (
                job_id, email, "Pending", fullname, email, phone,
                job["company_name"], resume_filename,
                experience or None, int(passing_year), "Job", skills
            ))
            conn.commit()
        except Exception:
            conn.rollback()
            if resume_path.exists():
                resume_path.unlink()
            raise

        return redirect(url_for("application_success"))

    finally:
        cursor.close()
        conn.close()


@app.route("/post-job", methods=["GET", "POST"])
def post_job():
    # Check whether the company is logged in
    if "company_id" not in session:
        return redirect(url_for("company_login"))

    if request.method == "POST":
        # Get data from the job posting form
        job_role = request.form.get("job_role", "").strip()
        description = request.form.get("description", "").strip()
        skills = request.form.get("skills", "").strip()
        package = request.form.get("package", "").strip()
        location = request.form.get("location", "").strip()

        # Validate required fields
        if not job_role or not description:
            flash("Please enter the job role and description.", "error")
            return redirect(url_for("post_job"))

        conn = None
        cursor = None

        try:
            conn = get_db_connection()
            cursor = conn.cursor()

            # Insert the new job into the database
            sql = """
                INSERT INTO jobs
                (company_id, job_role, description, skills, package, location)
                VALUES (%s, %s, %s, %s, %s, %s)
            """

            values = (
                session["company_id"],
                job_role,
                description,
                skills,
                package,
                location
            )

            cursor.execute(sql, values)
            conn.commit()

            flash("Job posted successfully!", "success")
            return redirect(url_for("company_dashboard"))

        except Exception as e:
            if conn:
                conn.rollback()

            print("Error posting job:", e)
            flash("Unable to post the job. Please check your database and try again.", "error")

        finally:
            if cursor:
                cursor.close()
            if conn:
                conn.close()

    return render_template("post_job.html")

@app.route("/application-success")
def application_success():
    return render_template("application_success.html")


@app.errorhandler(413)
def file_too_large(error):
    return "Resume is too large. The maximum allowed size is 5 MB.", 413


if __name__ == "__main__":
    app.run(debug=True)