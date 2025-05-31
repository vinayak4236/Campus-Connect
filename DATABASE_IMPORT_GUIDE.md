# Campus Connect Database Import Guide

This guide provides instructions on how to import the Campus Connect database into your XAMPP environment.

## Prerequisites

- XAMPP installed and running
- Apache and MySQL services started

## Import Methods

### Method 1: Using the Web Interface (Recommended)

1. Start your XAMPP Apache and MySQL services
2. Open your browser and navigate to: http://localhost/campus-connect/import_database.php
3. Check the confirmation box and click "Import Database"
4. The script will create the database and populate it with sample data
5. After successful import, you can navigate to the website or admin panel

### Method 2: Using phpMyAdmin

1. Start your XAMPP Apache and MySQL services
2. Open phpMyAdmin: http://localhost/phpmyadmin
3. Click on "New" in the left sidebar to create a new database
4. Name it "campus_connect" and select "utf8mb4_general_ci" as the collation
5. Click "Create"
6. Select the "campus_connect" database from the left sidebar
7. Click on the "Import" tab
8. Click "Choose File" and select the `database/campus_connect.sql` file
9. Click "Go" to import the database

### Method 3: Using the Reset Script

If you need to reset the database at any point:

1. Navigate to: http://localhost/campus-connect/reset_database.php
2. Check the confirmation box and click "Reset and Rebuild Database"
3. The script will reset the database and populate it with fresh sample data

## Default Admin Credentials

After importing the database, you can log in to the admin panel with:

- **URL**: http://localhost/campus-connect/admin/login.php
- **Username**: `admin`
- **Password**: `admin123`

## Troubleshooting

- **Database Connection Error**: Make sure MySQL service is running in XAMPP
- **Import Fails**: Check that you have proper permissions in your MySQL setup
- **PHP Errors**: Ensure PHP is properly configured in your XAMPP installation

If you encounter any issues, please check the XAMPP error logs for more details.