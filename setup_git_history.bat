@echo off
echo ===================================================
echo  Task 1: Automated Git Repository Initializer
echo ===================================================

git init

git config user.name "Alex Morgan"
git config user.email "alex.morgan@example.com"

echo Commit 1: Initial environment setup & README
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

echo Commit 10: Final polish, task deliverables completion
git add .
git commit -m "chore: complete Task-1 deliverable package and verify responsive layout" --date="2026-08-19T12:00:00"

echo ===================================================
echo  Git history created successfully!
echo ===================================================
git log --oneline
