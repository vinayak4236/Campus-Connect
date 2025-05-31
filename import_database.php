<?php
// Database import script for Campus Connect Portal

// Set maximum execution time to 5 minutes
ini_set('max_execution_time', 300);

// Database configuration
$host = "localhost";
$username = "root";
$password = ""; // Change this if you have a password set
$sqlFile = __DIR__ . "/database/reset_and_import.sql";

// Function to execute SQL file
function executeSQLFile($host, $username, $password, $sqlFile) {
    // Read SQL file
    if (!file_exists($sqlFile)) {
        return "SQL file not found: $sqlFile";
    }
    
    $sql = file_get_contents($sqlFile);
    if (!$sql) {
        return "Could not read SQL file: $sqlFile";
    }
    
    // Connect to MySQL (not to a specific database)
    $conn = new mysqli($host, $username, $password);
    if ($conn->connect_error) {
        return "Connection failed: " . $conn->connect_error;
    }
    
    // Execute SQL commands
    $result = $conn->multi_query($sql);
    if (!$result) {
        return "Error executing SQL: " . $conn->error;
    }
    
    // Wait for all queries to finish
    while ($conn->more_results() && $conn->next_result()) {
        // Consume all results
    }
    
    // Check for errors
    if ($conn->error) {
        return "Error after execution: " . $conn->error;
    }
    
    $conn->close();
    return true;
}

// Process form submission
$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["import"])) {
    $result = executeSQLFile($host, $username, $password, $sqlFile);
    
    if ($result === true) {
        $success = true;
        $message = "Database import completed successfully!";
    } else {
        $message = "Error: " . $result;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Campus Connect Database</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
        <div class="header">
            <h1>Import Campus Connect Database</h1>
            <p class="text-muted">This tool will import the Campus Connect database</p>
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
            <div class="info">
                <h4>ℹ️ Information</h4>
                <p>This action will create the Campus Connect database and populate it with sample data.</p>
                <p>If the database already exists, it will be dropped and recreated.</p>
            </div>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="confirm" name="confirm" required>
                    <label class="form-check-label" for="confirm">
                        I understand that this will create or replace the existing database
                    </label>
                </div>
                <div class="d-grid">
                    <button type="submit" name="import" class="btn btn-primary">Import Database</button>
                </div>
            </form>
            
            <div class="mt-4">
                <a href="index.html" class="btn btn-outline-secondary">Cancel and Return to Website</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>