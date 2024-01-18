<?php
include '..includes/db.php';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve main event details
    $event_name = $_POST["event_name"];
    $event_date = $_POST["event_date"];
    $event_location = $_POST["event_location"];

    // Retrieve dynamic fields as arrays
    $event_type = $_POST["eventtype"];
    $area_code = $_POST["areacode"];
    $price_type = $_POST["pricetype"];
    $price = $_POST["price"];

    // Convert arrays to JSON format for database storage
    $event_type = json_encode($event_type);
    $area_code = json_encode($area_code);
    $price_type = json_encode($price_type);
    $price = json_encode($price);

    // Insert data into the database
    $sql = "INSERT INTO events (event_name, event_date, event_location, event_type, area_code, price_type, price) 
            VALUES ('$event_name', '$event_date', '$event_location', '$event_type', '$area_code', '$price_type', '$price')";

    if ($conn->query($sql) === TRUE) {
        echo "Event added successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
