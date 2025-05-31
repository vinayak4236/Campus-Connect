# Campus Connect Portal

A comprehensive web portal for campus events, clubs, and announcements.

## Features

- Events management with detailed event pages
- Clubs directory with membership requests
- Announcements system with categories and priorities
- Admin panel for content management
- Responsive design for all devices

## Installation

### Prerequisites

- XAMPP (or similar local server environment with PHP 7.4+ and MySQL)
- Web browser

### Setup Instructions

1. **Install XAMPP**:
   - Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Start the Apache and MySQL services from the XAMPP Control Panel

2. **Clone or Download the Repository**:
   - Place the project files in the `htdocs` folder of your XAMPP installation
   - The path should be: `C:\xampp\htdocs\campus-connect-portal\`

3. **Import the Database**:
   - Open your web browser and navigate to `http://localhost/phpmyadmin`
   - Create a new database named `campus_connect` (or use the SQL file which will create it for you)
   - Import the SQL file located at `database/campus_connect.sql`

4. **Access the Website**:
   - Frontend: `http://localhost/campus-connect-portal/`
   - Admin Panel: `http://localhost/campus-connect-portal/admin/`

5. **Admin Login Credentials**:
   - Username: `admin`
   - Password: `admin123`

## Project Structure

- `/admin` - Admin panel files
- `/api` - API endpoints for frontend
- `/config` - Configuration files
- `/css` - CSS stylesheets
- `/database` - Database SQL file
- `/img` - Image assets
- `/js` - JavaScript files

## Technologies Used

- PHP 7.4+
- MySQL
- HTML5
- CSS3
- JavaScript
- Bootstrap 5
- Font Awesome

## Features

### Frontend

- **Events**: Browse, search, and filter events. View detailed event information and register for events.
- **Clubs**: Explore campus clubs, filter by category, and submit membership requests.
- **Announcements**: Stay updated with campus announcements, filtered by category and priority.

### Admin Panel

- **Dashboard**: Overview of events, clubs, and announcements.
- **Events Management**: Add, edit, and delete events. Manage event schedules and related events.
- **Clubs Management**: Add, edit, and delete clubs.
- **Announcements Management**: Add, edit, and delete announcements.

## Customization

- Edit the CSS files to change the appearance
- Modify the database structure to add new features
- Extend the admin panel with additional functionality

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgements

- Bootstrap for the responsive framework
- Font Awesome for the icons
- XAMPP for the local development environment