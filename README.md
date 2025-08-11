# robot-arm-control-backend-tester

# Robot Arm Control API + Tester — Full README (No code)

## 1) Overview
Backend API (PHP + MySQL on XAMPP) to save and fetch robot arm poses, plus a browser-based tester page.  
What you can do:
  Save servo angles (motor1..motor4) to DB
  Get latest pose (JSON)
  Get all poses (JSON list)
  Reset run status flag
  Test everything from a simple tester page

Deliverables (included in this repo):
  /robot_api folder with all PHP scripts (you will attach the actual files)
  This README explaining setup, wiring, and testing

-----------------------------------
## 2) Environment
  Windows + XAMPP (Apache + MySQL)
  phpMyAdmin reachable at: http://localhost/phpmyadmin
  MySQL default port: 3306 (if different, update it in db.php)
  Project root on local server: C:\xampp\htdocs\robot_api\

------------------------------------
## 3) Folder Structure (what to place where)
C:\xampp\htdocs\robot_api\
  db.php               → central DB connection (shared by all scripts)
  save_pose.php        → inserts a new pose (expects POST: motor1..motor4)
  get_run_pose.php     → returns the most recent pose (JSON)
  get_all_poses.php    → returns all poses (JSON, newest first)
  update_status.php    → ensures arm_status row exists, sets run_status = 0
  tester.php           → form to send angles and see responses

No code is pasted here — place your actual files in this structure

------------------------------------
## 4) Database Setup (no SQL pasted)
Create a MySQL database named: **robot_arm**

Create these tables:

A) **poses**
  id (INT, PK, AUTO_INCREMENT)
  motor1 (INT, required)
  motor2 (INT, required)
  motor3 (INT, required)
  motor4 (INT, required)
  created_at (TIMESTAMP, default CURRENT_TIMESTAMP)

B) **arm_status**
  id (INT, PK) → ensure there is a row with id=1
  run_status (TINYINT, default 0)

Notes:

  If you previously had different column names (e.g., m1..m4) rename them to motor1..motor4.
  Ensure a single row exists in arm_status with id=1. The script also self-heals if missing.
------------------------------------
## 5) How everything is wired (flow)
1) **tester.php → save_pose.php**  
     The tester sends a POST request with motor1..motor4.
     save_pose.php validates & inserts into poses via db.php.

2) **get_run_pose.php**  
   Reads the latest row from poses and returns JSON (id, motor1..motor4, created_at).

3) **get_all_poses.php**  
   Reads all rows from poses ordered by id DESC and returns JSON array.

4) **update_status.php**  
   Guarantees arm_status(id=1) exists, then sets run_status=0 (reset).

5) **db.php**  
     Centralized MySQL connection: host 127.0.0.1, user root, empty password (XAMPP default), DB robot_arm, port 3306 (update if you changed MySQL port).
     All other scripts include/require db.php instead of duplicating connection code.
------------------------------------
## 6) Run & Test (step-by-step, no code)
1) Open XAMPP Control Panel → Start **Apache** and **MySQL** (both must be Running).
2) Ensure DB exists (Section 4) and tables have the correct columns.
3) Place all files in **C:\xampp\htdocs\robot_api\** (exact names as in Section 3).
4) Open in browser:
     Tester page: **http://localhost/robot_api/tester.php**
     Enter values for motor1..motor4 → click “Save Pose”.
     You should see a JSON success response.

5) Verify endpoints:
     Latest pose: **http://localhost/robot_api/get_run_pose.php** (returns single JSON object)
     All poses: **http://localhost/robot_api/get_all_poses.php** (returns JSON array, newest first)
     Reset status: **http://localhost/robot_api/update_status.php** (plain text: “Status updated to 0”)

6) If using a phone on the same Wi-Fi:
     Replace `localhost` with your PC’s IPv4 (from `ipconfig`, e.g., 192.168.1.10) inside tester/configs or the mobile app.
     Allow Apache in Windows Firewall.
------------------------------------
## 7) What we fixed during setup (important notes)

  **MySQL connection refused**: confirmed MySQL is running on 3306, ensured db.php uses 127.0.0.1 and correct port.
  **Unknown column 'motor1'**: normalized table columns to motor1..motor4 in poses.
  **Unknown column 'run_status'**: added run_status to arm_status; ensured row with id=1 exists.
  **Character set**: all scripts use utf8mb4 to avoid encoding issues.
  **Centralized connection**: moved to db.php so all scripts share the same connection and port settings.
------------------------------------
## 8) Optional integrations (no code)
A) **Flutter app**  
Add an HTTP client dependency.  
  Base URL:
  Android emulator → http://10.0.2.2/robot_api/
    Real device → http://<PC-IP>/robot_api/
  App should hit these endpoints:
    POST **save_pose.php** with motor1..motor4
    GET **get_run_pose.php**
    GET **get_all_poses.php**
    GET **update_status.php**
  Android manifest: allow INTERNET and cleartext traffic if needed.

B) **Python/Colab**

  Poll **get_run_pose.php** every second and forward angles to the robot controller.  
  Colab cannot call localhost; use your PC IP (same network) or expose via a tunnel if remote.
------------------------------------
## 9) Final checklist (ready to submit)
       [ ] XAMPP: Apache + MySQL are Running
       [ ] Database **robot_arm** exists
       [ ] Tables:
       poses(id, motor1, motor2, motor3, motor4, created_at)
       arm_status(id, run_status) with row id=1 present
       [ ] Files placed in **C:\xampp\htdocs\robot_api\**:
       db.php, save_pose.php, get_run_pose.php, get_all_poses.php, update_status.php, tester.php
       [ ] Tester works:
      Save Pose returns success
      Latest pose endpoint returns the same values
      All poses lists entries (newest first)
      Reset status prints “Status updated to 0”
      [ ] (Optional) Mobile or Python client can reach the endpoints using PC IP

------------------------------------
## Notes for reviewers
  All scripts share one DB config via db.php; change DB host/port/credentials in one place.
  If MySQL runs on a non-default port, edit db.php accordingly.
  If endpoints return “No poses found”, it just means nothing was saved yet—use tester.php to insert.
  If accessing from a phone, ensure both devices are on the same LAN and firewall isn’t blocking Apache.
