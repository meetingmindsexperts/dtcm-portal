<?php
include_once '../includes/db.php';
include_once '../includes/functions.php';
include_once '../includes/header.php';

// Pagination settings
$eventsPerPage = 4;
$currentpage = isset($_GET['page']) ? $_GET['page'] : 1;

// Get total number of events
$totalEvents = countEvents();

// Calculate the total number of pages
$totalPages = ceil($totalEvents / $eventsPerPage);

// Calculate the starting event for the current page
$offset = ($currentpage - 1) * $eventsPerPage;

// Get events for the current page
$events = getEventsPaginated($offset, $eventsPerPage);

?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Events</h2>
        <a class="btn btn-success" href="<?php echo $baseUrl; ?>/views/add-event.php">Add Event</a>
    </div>

    <ul class="list-group">
        <?php foreach ($events as $event): ?>
            <li class="list-group-item">
                <strong><?= $event['event_name']; ?></strong> - <?= $event['date_added']; ?>
                <p><?= $event['performance_code']; ?></p>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Pagination links -->
    <div class="mt-3">
        <?php for ($page = 1; $page <= $totalPages; $page++): ?>
            <a href="?page=<?= $page ?>" class="btn btn-outline-primary <?= ($page == $currentpage) ? 'active' : ''; ?>"><?= $page ?></a>
        <?php endfor; ?>
    </div>
</div>

<?php
include_once '../includes/footer.php';
?>
