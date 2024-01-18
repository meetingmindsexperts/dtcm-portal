<?php
// Include necessary files
// include_once '../includes/auth.php';

include_once '../includes/functions.php';
include_once '../includes/header.php';
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $evenet_name = $_POST['evenet_name'];
    $ddi_api = $_POST['ddi_api'];
    $register_link = $_POST['register_link'];
    $performance_code = $_POST['performance_code'];
    $event_logo = $_POST['event_logo'];

    // Add event to the database in functions.php
if (addEvent($eventid, $ddi_api, $register_link, $performance_code, $event_logo, $eventdate, $eventvenue, $eventtype, $areacode, $pricetype, $price)) {
        echo 'Event added successfully!';
    } else {
        echo 'Error adding event.';
    }
}
?>

<div class="container mt-5">
    <h2>Add Event</h2>
    <form  class="p-md-5" method="post">
        <div class="mb-3">
            <label for="eventname" class="form-label">Event Name</label>
            <input type="text" class="form-control" id="eventname" name="eventname" required>
        </div>
        <div class="mb-3">
            <label for="ddi_api" class="form-label">DDI API</label>
            <input type="text" class="form-control" id="ddi_api" name="ddi_api" required>
        </div>
        <div class="mb-3">
            <label for="register_link" class="form-label">Register Link</label>
            <input type="text" class="form-control" id="register_link" name="register_link" required>
        </div>
        <div class="mb-3">
            <label for="performance_code" class="form-label">Performance Code</label>
            <input type="text" class="form-control" id="performance_code" name="performance_code" required>
        </div>
        <div class="mb-3">
            <label for="event_logo" class="form-label">Event Logo</label>
            <input type="file"  accept="jpg, jpeg, png, webp, svg"  ="form-control" id="event_logo" name="event_logo" required>
        </div>
        <!-- Add these fields to your add_event.php form -->
        <div class="d-flex align-items-center flex-wrap">
            <div class="m-3">
                <label for="eventtype" class="form-label">Event Type</label>
                <input type="text" class="form-control" id="eventtype" name="eventtype[]" required>
            </div>
            <div class="m-3">
                <label for="areacode" class="form-label">Area Code</label>
                <input type="text" class="form-control" id="areacode" name="areacode[]" required>
            </div>
            <div class="m-3">
                <label for="pricetype" class="form-label">Price Type</label>
                <input type="text" class="form-control" id="pricetype" name="pricetype[]" required>
            </div>
            <div class="m-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" class="form-control" id="price" name="price[]" required>
            </div>
            <button type="button" class="btn btn-success mt-4" id="addFields">Add More</button>

        </div>

        <!-- Add a button to dynamically add more fields -->

        <!-- Container to hold dynamically added fields -->
        <div id="dynamicFieldsContainer" class="d-flex flex-wrap align-items-center"></div>

        <button type="submit" class="btn btn-primary px-5 ">Add Event</button>
    </form>
</div>
<?php include_once '../includes/footer.php'; ?>
<script>
$(document).ready(function() {
    // Counter to track dynamically added fields
    var fieldCounter = 0;

    // Event listener for the "Add More Fields" button
    $("#addFields").click(function() {
        // Increment the counter for unique field IDs
        fieldCounter++;

        // Create new field elements
        var newFields = '<div class="d-flex flex-wrap align-items-center"><div class="m-2">' +
                            '<label for="eventtype_' + fieldCounter + '" class="form-label">Event Type</label>' +
                            '<input type="text" class="form-control" id="eventtype_' + fieldCounter + '" name="eventtype[]" required>' +
                        '</div>' +
                        '<div class="m-2">' +
                            '<label for="areacode_' + fieldCounter + '" class="form-label">Area Code</label>' +
                            '<input type="text" class="form-control" id="areacode_' + fieldCounter + '" name="areacode[]" required>' +
                        '</div>' +
                        '<div class="m-2">' +
                            '<label for="pricetype_' + fieldCounter + '" class="form-label">Price Type</label>' +
                            '<input type="text" class="form-control" id="pricetype_' + fieldCounter + '" name="pricetype[]" required>' +
                        '</div>' +
                        '<div class="m-2">' +
                            '<label for="price_' + fieldCounter + '" class="form-label">Price</label>' +
                            '<input type="text" class="form-control" id="price_' + fieldCounter + '" name="price[]" required>' +
                        '</div></div>';

        // Append the new fields to the container
        $("#dynamicFieldsContainer").append(newFields);
    });

    // Event listener for dynamically removing fields
    $(document).on("click", ".removeFields", function() {
        // Remove the parent div containing the fields
        $(this).closest(".dynamicFieldsGroup").remove();
    });
});
</script>


