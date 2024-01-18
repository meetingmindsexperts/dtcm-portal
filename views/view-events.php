<?php
// Include necessary files

// include_once '../includes/auth.php';
include_once '../includes/db.php';
include_once '../includes/functions.php';
include_once '../includes/header.php';
// Get all events from the database
$events = getEvents();
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center"> 
        <h2>Events</h2>
        <a class="btn btn-success" href="<?php echo $baseUrl; ?>/views/add-event.php">Add Event</a>

    </div>


    <ul class="list-group">
        <?php foreach ($events as $event): ?>
            <li class="list-group-item">
                <strong><?= $event['name']; ?></strong> - <?= $event['date']; ?>
                <p><?= $event['description']; ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php
include_once '../includes/footer.php';
?>