# Doctor Management System

---

## Project Proposal

---

## 1. Introduction

Managing doctor information manually can be time-consuming and inefficient for clinics and hospitals. Updating doctor details, removing outdated records, and displaying available doctors often require manual effort.


---

## 2. Problem Statement

Many small clinics do not have a digital system to manage doctor records. This creates problems such as:

* Difficulty updating doctor information
* Risk of data loss
* Time-consuming manual record management
* Inconsistent information

A simple database-driven system can solve these problems efficiently.

---

## 3. Objectives

The main objectives of this project are:

1. Develop a web-based Doctor Management System
2. Implement CRUD operations using PHP
3. Store doctor data in a MySQL database
4. Automatically update displayed information after changes
5. Create a simple and user-friendly interface

---

## 4. System Overview

The system will have two main sections:

---

### 4.1 Home Page (index.php)

The Home Page will:

* Display all doctors from the database
* Show the following information:

  * Doctor Name
  * Specialization
  * Contact Number
  * Email
  * Experience
  * Available Days

The page will automatically update whenever the admin modifies data.

---

### 4.2 Admin Page (admin.php)

The Admin Page will allow the administrator to:

* Add new doctor
* Edit existing doctor information
* Delete doctor records

All changes will be stored instantly in the MySQL database.

---

## 5. Technology Stack



---

## 6. Database Design



### Table Structure

| Field Name     | Data Type                         |
| -------------- | --------------------------------- |
| id             | INT (Primary Key, Auto Increment) |
| name           | VARCHAR(100)                      |
| specialization | VARCHAR(100)                      |
| phone          | VARCHAR(20)                       |
| email          | VARCHAR(100)                      |
| experience     | INT                               |
| available_days | VARCHAR(100)                      |

---

## 7. Methodology

### Step 1: Database Setup

* Create MySQL database

---

### Step 2: Backend Development

* Use PHP to handle form submissions
* Write SQL queries for:



---

### Step 3: Frontend Development

* Design simple HTML forms
* Display doctor list in table format
* Provide Edit and Delete buttons

---

### Step 4: Testing

* Test adding, updating, and deleting data
* Verify that the homepage updates correctly
* Ensure proper validation and error handling

---

## 8. Expected Outcome

At the end of 4 weeks, the system will:

* Successfully perform all CRUD operations
* Store doctor information securely in MySQL
* Display updated doctor information automatically
* Provide a clean and simple user interface

---

## 9. 4-Week Project Timeline

### Week 1 – Planning & Database

* Design system structure
* Install XAMPP
* Create database and table
* Connect PHP with MySQL

---

### Week 2 – Basic CRUD Implementation

* Create Add Doctor functionality
* Display doctors on homepage
* Test database operations

---

### Week 3 – Update & Delete Features (spl setup in Xamp)

* Implement Edit doctor feature
* Implement Delete functionality
* Improve form validation

---

### Week 4 – Testing & Finalization


---

## 10. Conclusion

The Doctor Management System is a simple web-based CRUD application developed using PHP and MySQL. It allows administrators to efficiently manage doctor information in a structured and digital way.


