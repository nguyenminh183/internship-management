# Contents of `README.md`

# Internship Management System

## Introduction
This project is an Internship Management System built using PHP and MySQL. It allows lecturers to manage internship courses and students to register for internships. The system supports importing student data from Excel files and provides a user-friendly interface for both lecturers and students.

## Features
- **User Authentication**: Login and password management for lecturers and students.
- **Course Management**: Create, update, and view internship courses.
- **Student Management**: Import student data from Excel files and manage student information.
- **Database Management**: Automatically creates necessary tables for the application.

## Setup Instructions
1. **Clone the Repository**:
   ```bash
   git clone <repository-url>
   cd internship-management
   ```

2. **Install Dependencies**:
   Make sure you have Composer installed, then run:
   ```bash
   composer install
   ```

3. **Configure Database**:
   Update the database connection settings in `src/config/database.php` with your MySQL credentials.

4. **Create Database Tables**:
   Run the SQL commands in `src/migrations/create_tables.sql` to set up the database tables.

5. **Run the Application**:
   Start your local server and navigate to `index.php` to access the application.

## Usage
- **Login**: Use the login page to access the system.
- **Manage Courses**: Lecturers can create and manage courses through the course management interface.
- **Import Students**: Use the import feature to upload student data from Excel files.

## License
This project is licensed under the MIT License. See the LICENSE file for more details.