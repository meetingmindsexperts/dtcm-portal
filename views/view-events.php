<?php
include_once '../includes/db.php';
include_once '../includes/functions.php';
include_once '../includes/header.php';

// Pagination settings
$eventsPerPage = 5;
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
<style>
.event_data {
    padding: 20px;
    border-bottom:  2px solid #dadada;
    border-radius: 10px;
    margin-bottom: 10px;
}
.event_data:nth-of-type(even) {
    background: #f8f8f8;
    
}
.event_data:nth-of-type(odd) {
    background: #fff;
}
</style>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Events</h2>
        <a class="btn btn-success" href="<?php echo $baseUrl; ?>/views/add-event.php">Add Event</a>
    </div>

    <div class="pt-5">
        <?php 
        foreach ($events as $event): 
            $id = $event['id'];
            ?>
            <div class="row event_data justify-content-between">
                <div class="col-12 col-md-8">
                    <strong><?= $event['event_name']; ?></strong> - <?= $event['date_added']; ?>
                    <p><?= $event['performance_code']; ?></p>

                </div>
                <div class="col-md-4 text-end">
                    <a class='btn btn-success' href='view-event-table-data.php?id=<?php echo $id; ?>'>View Data</a>
                </div>
            </div>
        <?php endforeach; ?>
        </div>

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
