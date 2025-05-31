# Campus Connect Portal - Setup Guide

This guide will help you set up the Campus Connect Portal on your local machine using XAMPP.

## Prerequisites

- XAMPP (or similar local server environment with PHP 7.4+ and MySQL)
- Web browser

## Installation Steps

1. **Install XAMPP**:
   - Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Start the Apache and MySQL services from the XAMPP Control Panel

2. **Set Up the Database**:
   - Open your web browser and navigate to `http://localhost/phpmyadmin`
   - You can set up the database in one of two ways:
     
     **Option 1: Using the SQL File**
     - Create a new database named `campus_connect` (or use the SQL file which will create it for you)
     - Import the SQL file located at `database/campus_connect.sql`
     
     **Option 2: Using the Installation Script**
     - Navigate to `http://localhost/campus-connect-portal/install.php`
     - Follow the on-screen instructions to set up the database

3. **Update JavaScript References** (Optional):
   - If you encounter any issues with JavaScript files not loading correctly, run the update script:
   - Navigate to `http://localhost/campus-connect-portal/update-scripts.php`
   - This will update all HTML files to use the correct JavaScript references

4. **Access the Website**:
   - Frontend: `http://localhost/campus-connect-portal/`
   - Admin Panel: `http://localhost/campus-connect-portal/admin/`

5. **Admin Login Credentials**:
   - Username: `admin`
   - Password: `admin123`

## Troubleshooting

### Database Connection Issues
- Make sure MySQL is running in XAMPP Control Panel
- Check that the database name, username, and password in `config/database.php` match your MySQL settings
- Default settings are:
  - Host: `localhost`
  - Username: `root`
  - Password: `` (empty)
  - Database: `campus_connect`

### File Permission Issues
- Make sure the `IMG` directory has write permissions for file uploads
- On Windows, this is usually not an issue
- On Linux/Mac, you may need to run: `chmod 755 -R IMG/`

### JavaScript Not Loading
- Run the update script mentioned in step 3
- Clear your browser cache
- Check the browser console for any errors

### PHP Errors
- Check the Apache error log in XAMPP
- Enable error reporting in PHP by adding these lines at the top of PHP files:
  ```php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  ```

## Additional Information

- The admin panel allows you to manage events, clubs, and announcements
- The frontend fetches data from the API endpoints in the `/api` directory
- If the API fails, it falls back to static data in the JavaScript files

For more details, see the main README.md file.