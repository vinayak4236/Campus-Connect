<?php
// Installation script for Campus Connect Portal

// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "campus_connect";

// Function to check if database exists
function databaseExists($host, $username, $password, $database) {
    $conn = new mysqli($host, $username, $password);
    if ($conn->connect_error) {
        return false;
    }
    
    $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database'");
    $exists = $result->num_rows > 0;
    
    $conn->close();
    return $exists;
}

// Function to import SQL file
function importSQLFile($host, $username, $password, $database, $sqlFile) {
    $conn = new mysqli($host, $username, $password);
    if ($conn->connect_error) {
        return false;
    }
    
    // Read SQL file
    $sql = file_get_contents($sqlFile);
    
    // Execute SQL
    $result = $conn->multi_query($sql);
    
    // Wait for all queries to finish
    while ($conn->more_results() && $conn->next_result()) {
        // Consume all results
    }
    
    $conn->close();
    return $result;
}

// Check if form is submitted
$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if database exists
    $dbExists = databaseExists($host, $username, $password, $database);
    
    if ($dbExists && !isset($_POST["overwrite"])) {
        $message = "Database '$database' already exists. Check the box to overwrite it.";
    } else {
        // Import SQL file
        $sqlFile = __DIR__ . "/database/campus_connect.sql";
        
        if (file_exists($sqlFile)) {
            $result = importSQLFile($host, $username, $password, $database, $sqlFile);
            
            if ($result) {
                $success = true;
                $message = "Installation completed successfully! You can now access the website.";
            } else {
                $message = "Error importing SQL file. Please check your database configuration.";
            }
        } else {
            $message = "SQL file not found. Please make sure the file exists at: $sqlFile";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Campus Connect Portal</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .install-container {
            max-width: 700px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .install-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .install-steps {
            margin-bottom: 30px;
        }
        .step {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
        }
        .step-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            background-color: #0d6efd;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            margin-right: 10px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="install-container">
            <div class="install-header">
                <h1>Campus Connect Portal Installation</h1>
                <p class="text-muted">Follow the steps below to install the Campus Connect Portal</p>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="<?php echo $success ? 'success-message' : 'error-message'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="text-center mb-4">
                    <a href="index.html" class="btn btn-primary me-2">Go to Website</a>
                    <a href="admin/login.php" class="btn btn-secondary">Go to Admin Panel</a>
                </div>
                <div class="alert alert-info">
                    <strong>Admin Login Credentials:</strong><br>
                    Username: <code>admin</code><br>
                    Password: <code>admin123</code>
                </div>
            <?php else: ?>
                <div class="install-steps">
                    <div class="step">
                        <span class="step-number">1</span>
                        <strong>Check Requirements</strong>
                        <div class="mt-2">
                            <ul class="list-unstyled">
                                <li>
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    PHP Version: <?php echo phpversion(); ?>
                                </li>
                                <li>
                                    <i class="fas <?php echo extension_loaded('mysqli') ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'; ?> me-2"></i>
                                    MySQLi Extension: <?php echo extension_loaded('mysqli') ? 'Installed' : 'Not Installed'; ?>
                                </li>
                                <li>
                                    <i class="fas <?php echo is_writable(__DIR__) ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'; ?> me-2"></i>
                                    Directory Writable: <?php echo is_writable(__DIR__) ? 'Yes' : 'No'; ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step">
                        <span class="step-number">2</span>
                        <strong>Database Configuration</strong>
                        <div class="mt-2">
                            <ul class="list-unstyled">
                                <li><strong>Host:</strong> <?php echo $host; ?></li>
                                <li><strong>Username:</strong> <?php echo $username; ?></li>
                                <li><strong>Database:</strong> <?php echo $database; ?></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="step">
                        <span class="step-number">3</span>
                        <strong>Install Database</strong>
                        <div class="mt-2">
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="overwrite" name="overwrite" value="1">
                                    <label class="form-check-label" for="overwrite">
                                        Overwrite existing database (if exists)
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary">Install Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="text-center text-muted mt-4">
                <small>Campus Connect Portal &copy; <?php echo date('Y'); ?></small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>