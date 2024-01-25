<?php

// Include the database connection file
include_once 'db.php';

// Function to add an event to the database
// Update the addEvent function to handle arrays for new fields
// Update the addEvent function to handle arrays for new fields
function addEvent($eventid, $ddi_api, $register_link, $performance_code, $event_logo, $eventdate, $eventvenue, $eventtype, $areacode, $pricetype, $price)
{
    global $conn;

    // Convert arrays to JSON format to store in the database
    $eventtype = json_encode($eventtype);
    $areacode = json_encode($areacode);
    $pricetype = json_encode($pricetype);
    $price = json_encode($price);

    $sql = "INSERT INTO events (eventid, ddi_api, register_link, performance_code, event_logo, eventdate, eventvenue, eventtype, areacode, pricetype, price) 
            VALUES ('$eventid', '$ddi_api', '$register_link', '$performance_code', '$event_logo', '$eventdate', '$eventvenue', '$eventtype', '$areacode', '$pricetype', '$price')";

    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}



// Function to get all events from the database
// function getEvents() {
//     global $conn;

//     $sql = "SELECT * FROM events_csv";
//     $result = $conn->query($sql);

//     $events = [];

//     if ($result->num_rows > 0) {
//         while ($row = $result->fetch_assoc()) {
//             $events[] = $row;
//         }
//     }

//     return $events;
// }

// Function to get a subset of events based on pagination
function getEventsPaginated($offset, $limit) {
    global $conn;

    $sql = "SELECT * FROM events_csv LIMIT $offset, $limit";
    $result = $conn->query($sql);

    $events = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
    }

    return $events;
}

function countEvents() {
    global $conn;

    // Your SQL query to count events
    $sql = "SELECT COUNT(*) as id FROM events_csv";

    // Execute the query
    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result) {
        // Fetch the result as an associative array
        $row = $result->fetch_assoc();

        // Return the total number of events
        return $row['id'];
    } else {
        // If the query failed, return 0 or handle the error accordingly
        return 0;
    }
}

