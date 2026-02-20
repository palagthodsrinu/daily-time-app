# ⏱️ Laravel Timesheet Application

A comprehensive, role-based timesheet and project management application built with PHP Laravel. This system is designed to streamline time tracking between Employees and Supervisors, organized cleanly by Clients and Projects. 

### ✨ Key Features
* **Role-Based Access Control:** Distinct dashboards and permissions for Admins, Supervisors, and Employees.
* **Client & Project Management:** Easily capture and toggle active/inactive statuses for clients and their associated projects.
* **Granular Assignments:** Assign employees to specific projects under designated supervisors.
* **Timesheet Workflow:** Employees can save drafts, edit, and submit timesheets (Date, Hours, Task Description).
* **Approval State Machine:** Timesheets transition smoothly through `Draft` ➔ `Pending` ➔ `Approved` or `Rejected`.
* **Supervisor Dashboard:** Supervisors get a filtered view to manage only the timesheets relevant to their assigned teams.
* **PDF Exporting:** Generate clean, downloadable PDF reports of approved timesheets.

### 🛠️ Tech Stack
* **Framework:** Laravel (PHP)
* **Database:** MySQL / PostgreSQL
* **Document Generation:** DOMPDF