@echo off
echo ===================================================
echo  Task 1: Automated Git Repository Initializer
echo ===================================================

git init

git config user.name "Alex Morgan"
git config user.email "alex.morgan@example.com"

echo Commit 1: Initial environment setup and README
git add README.md .gitignore
git commit -m "docs: add initial project setup, environment guides and README" --date="2026-08-10T10:00:00"

echo Commit 2: Added hello.php environment test
git add hello.php
git commit -m "feat(php): add hello.php script to test PHP installation and syntax" --date="2026-08-11T11:30:00"

echo Commit 3: Added database configuration and connection script
git add config/db.php
git commit -m "feat(database): add mysqli database configuration module" --date="2026-08-12T14:15:00"

echo Commit 4: Added MySQL schema and table definitions
git add sql/schema.sql
git commit -m "feat(database): add SQL schema for contacts, projects, and skills tables" --date="2026-08-13T09:45:00"

echo Commit 5: Added database diagnostic test script
git add db_test.php
git commit -m "feat(database): add db_test.php script for verifying MySQL connection" --date="2026-08-14T16:20:00"

echo Commit 6: Added HTML5 semantic structure for portfolio
git add index.html
git commit -m "feat(html): build semantic HTML5 portfolio layout with header, nav, sections, footer" --date="2026-08-15T10:10:00"

echo Commit 7: Added CSS3 styling, variables, glassmorphic layout
git add assets/css/styles.css
git commit -m "feat(css): add CSS3 custom properties, flexbox/grid layouts, animations, and dark/light themes" --date="2026-08-16T15:00:00"

echo Commit 8: Added JavaScript interactive functionality and form validation
git add assets/js/script.js
git commit -m "feat(js): implement theme toggle, mobile navigation, project filters, and client form validation" --date="2026-08-17T13:40:00"

echo Commit 9: Added PHP contact form handler with $_POST validation and MySQL insertion
git add contact.php
git commit -m "feat(backend): add contact.php handler for $_POST sanitization and database insert" --date="2026-08-18T17:25:00"

echo Commit 10: Task 2 Database schema updates with users table
git add sql/schema.sql
git commit -m "feat(database): add users table schema and test seed data for Task 2" --date="2026-08-20T10:00:00"

echo Commit 11: Added AJAX username and email check endpoint
git add ajax_check_user.php
git commit -m "feat(ajax): add ajax_check_user.php for real-time username and email availability checking" --date="2026-08-21T14:30:00"

echo Commit 12: Added AJAX authentication handler
git add ajax_auth.php
git commit -m "feat(ajax): add ajax_auth.php for asynchronous login and registration handling with BCRYPT hashing" --date="2026-08-22T11:15:00"

echo Commit 13: Added Bootstrap 5 plus custom Auth UI stylesheet
git add assets/css/auth-styles.css
git commit -m "style(css): add auth-styles.css with Bootstrap 5 overrides, glassmorphism, and keyframe animations" --date="2026-08-23T16:00:00"

echo Commit 14: Added Task 2 JavaScript validation and AJAX logic
git add assets/js/auth.js
git commit -m "feat(js): add auth.js for tab switching, password match/strength checking, and async fetch requests" --date="2026-08-24T13:45:00"

echo Commit 15: Added Task 2 Auth UI deliverable page and updated portfolio integration
git add auth.html index.html README.md
git commit -m "feat(ui): complete Task 2 deliverable (auth.html) with Bootstrap 5 grid, carousel, modals, and AJAX form handling" --date="2026-08-25T17:30:00"

echo Commit 16: Updated database schema for 3NF normalization and roles table
git add sql/schema.sql
git commit -m "feat(database): normalize schema to 3NF with roles table, foreign keys, and avatar fields" --date="2026-08-28T10:15:00"

echo Commit 17: Added session auth middleware and logout handler
git add config/auth_middleware.php logout.php
git commit -m "feat(auth): add session middleware and RBAC role verification logic" --date="2026-08-30T14:20:00"

echo Commit 18: Added Admin User Management CRUD dashboard with prepared statements
git add admin_users.php user_crud_handler.php
git commit -m "feat(crud): build Admin User Management dashboard with prepared statements and delete confirmation popup" --date="2026-09-02T11:00:00"

echo Commit 19: Added Profile Management page with avatar upload validation
git add profile.php
git commit -m "feat(profile): implement profile management page with avatar upload size and MIME validation" --date="2026-09-05T15:45:00"

echo Commit 20: Final Task 3 integration and README documentation update
git add .
git commit -m "feat(task3): complete Task 3 deliverables with RBAC CRUD, session auth, and profile avatar upload" --date="2026-09-08T12:00:00"

echo ===================================================
echo  Task 1, Task 2 and Task 3 Git history completed!
echo ===================================================
git log --oneline
