<?php
// Include necessary files
include_once 'includes/auth.php';
include_once 'includes/header.php';
?>

<div class="row py-2">
    <div class="col-md-12">
        <h2 class="mb-4">Welcome <?php echo ucwords($_SESSION['username']); ?>!</h2>
        <p>Go to Events page</p>
        <p>Things you can do here </p>
        <ul>
            <li>Upload CSV files </li>
            <li>View CSV files </li>
            <li>Generate Barcode  for events </li>
            <li>Donwload Barcodes in a CSV file</li>
        </ul>
        
    </div>
</div>

<?php
// Include necessary files
include_once 'includes/footer.php';
?>
