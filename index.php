<?php
// Set page title
$pageTitle = "Dashboard";

// Include header
include "includes/header.php";

// Include database connection
$conn = require_once "../config/database.php";

// Get counts
$eventCount = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$clubCount = $conn->query("SELECT COUNT(*) as count FROM clubs")->fetch_assoc()['count'];
$announcementCount = $conn->query("SELECT COUNT(*) as count FROM announcements")->fetch_assoc()['count'];

// Get recent items
$recentEvents = $conn->query("SELECT * FROM events ORDER BY created_at DESC LIMIT 5");
$recentAnnouncements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");

// Close connection
$conn->close();
?>

<!-- Dashboard Content -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Events</h5>
                        <h2 class="mb-0"><?php echo $eventCount; ?></h2>
                    </div>
                    <i class="fas fa-calendar-alt fa-3x"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="events.php">View Details</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Clubs</h5>
                        <h2 class="mb-0"><?php echo $clubCount; ?></h2>
                    </div>
                    <i class="fas fa-users fa-3x"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="clubs.php">View Details</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Announcements</h5>
                        <h2 class="mb-0"><?php echo $announcementCount; ?></h2>
                    </div>
                    <i class="fas fa-bullhorn fa-3x"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="announcements.php">View Details</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-calendar-alt me-1"></i>
                Recent Events
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentEvents->num_rows > 0): ?>
                                <?php while($event = $recentEvents->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $event['title']; ?></td>
                                        <td><?php echo $event['date']; ?></td>
                                        <td><span class="badge bg-<?php echo $event['category_class']; ?>"><?php echo $event['category']; ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">No events found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end">
                    <a href="events.php" class="btn btn-sm btn-primary">View All Events</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-bullhorn me-1"></i>
                Recent Announcements
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentAnnouncements->num_rows > 0): ?>
                                <?php while($announcement = $recentAnnouncements->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $announcement['title']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($announcement['date'])); ?></td>
                                        <td><span class="badge bg-<?php echo $announcement['category_class']; ?>"><?php echo $announcement['category']; ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">No announcements found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end">
                    <a href="announcements.php" class="btn btn-sm btn-primary">View All Announcements</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include "includes/footer.php";
?>