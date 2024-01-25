
<?php
// Start the session
session_start();

include_once 'includes/db.php';

// Initialize an error array
$errors = $_SESSION['errors'] =[];
$successMessage = '';

// Validate form data
$eventName = isset($_POST['eventName']) ? mysqli_real_escape_string($conn, trim($_POST['eventName'])) : '';
$performanceCode = isset($_POST['performanceCode']) ? mysqli_real_escape_string($conn, trim($_POST['performanceCode'])) : '';

// Validate file upload
if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] === UPLOAD_ERR_OK) {
    $csvFileName = $_FILES['csvFile']['name'];
    $csvTmpName = $_FILES['csvFile']['tmp_name'];

    // Move uploaded file to a designated folder with a unique filename
    $uploadDirectory = 'csv-uploads/';
    $uploadedFilePath = $uploadDirectory . $eventName . '_' . date('Y-m-d') . '_' . uniqid() . '.csv';

    if (move_uploaded_file($csvTmpName, $uploadedFilePath)) {
        // Escape user input for SQL query
        $eventName = mysqli_real_escape_string($conn, $eventName);
        $performanceCode = mysqli_real_escape_string($conn, $performanceCode);

        // Insert data into the database
        $sql = "INSERT INTO events_csv (event_name, performance_code, csv_file, event_table_name, date_added, date_modified) VALUES ('$eventName', '$performanceCode', '$uploadedFilePath', '', NOW(), NOW())";
        
        if ($conn->query($sql) === TRUE) {
            $successMessage = "Data inserted successfully!";
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
    } else {
        // Print more information about the file upload failure
        $errors[] = "File upload failed. Error code: " . $_FILES['csvFile']['error'];
        $errors[] = "Uploaded file name: " . $csvFileName;
        $errors[] = "Uploaded file temporary name: " . $csvTmpName;
        $errors[] = "Destination file path: " . $uploadedFilePath;
        $errors[] = "Upload directory: " . $uploadDirectory;
    }
} else {
    // Handle file upload errors
    $errors[] = "File upload failed with error code: " . $_FILES['csvFile']['error'];
}

// Close the database connection
$conn->close();

// Save errors in the session
$_SESSION['errors'] = $errors;
$_SESSION['successMessage'] = $successMessage;

// Redirect back to csv-upload.php
header("Location: views/csv-upload.php");
exit();





// // Specify the path to the CSV uploads folder
// $uploadsFolder = 'csv-uploads/';

// // Initialize the error variable
// $error = '';

// // Check if the form was submitted
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Check if a file was selected
//     if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] === UPLOAD_ERR_OK) {
//         // Generate a unique filename
//         $uploadFileName = $uploadsFolder . 'csv_upload_' . date('Y-m-d') . '_' . uniqid() . '.csv';

//         // Move the uploaded file to the specified folder
//         if (move_uploaded_file($_FILES['csvFile']['tmp_name'], $uploadFileName)) {
//             try {
//                 // Get other form values
//                 $event_name = $_POST['event_name'];
//                 $performanceCode = $_POST['performance_code'];

//                 // Process the uploaded CSV file and insert additional data
//                 processCSV($uploadFileName, $event_name, $performanceCode, $conn);

//                 // After processing the CSV file, set the session variable
//                 $_SESSION['csv_data'] = true;

//                 // Redirect to view-csv-uploaded-data.php
//                 header('Location: views/csv-upload.php');
//                 exit();
//             } catch (Exception $e) {
//                 // Capture the error message
//                 $error = 'Error: ' . $e->getMessage();
//             }
//         } else {
//             // Capture the error message
//             $error = 'Error: Unable to move the uploaded file.';
//         }
//     } else {
//         // Capture the error message
//         $error = 'Error: Please select a valid CSV file.';
//     }
// } else {
//     // Redirect back to the form if accessed directly
//     header('Location: views/csv-upload.php');
//     exit();
// }

// function processCSV($csvFilePath, $event_name, $performanceCode, $conn)
// {
//     $file = fopen($csvFilePath, 'r');

//     // Check if the file was opened successfully
//     if (!$file) {
//         throw new Exception('Unable to open the CSV file.');
//     }

//     // Begin a transaction
//     mysqli_begin_transaction($conn);

//     try {
//         // Skip the header row
//         $header = fgetcsv($file);

//         // Build the SQL query for insertion
//         $insertQuery = "INSERT INTO events_csv (event_name, performance_code, csv_file_path, date_modifed) 
//                         VALUES ('$event_name', '$performanceCode', '$csvFilePath', NOW())";

//         // Execute the query to insert into the new table
//         $conn->query($insertQuery);

//         // Commit the transaction
//         mysqli_commit($conn);

//         fclose($file);
//     } catch (Exception $e) {
//         // Rollback the transaction on exception
//         mysqli_rollback($conn);

//         // Rethrow the exception to be caught in the outer try-catch block
//         throw $e;
//     }
// }






// Function to process CSV file and create customers
// function processCSV($csvFilePath, $conn)
// {
//     $file = fopen($csvFilePath, 'r');

//     // Check if the file was opened successfully
//     if (!$file) {
//         throw new Exception('Unable to open the CSV file.');
//     }

//     // Begin a transaction
//     mysqli_begin_transaction($conn);

//     try {
//         // Skip the header row
//         $header = fgetcsv($file);

//         // Determine the position of the eventsair_id column
//         $event_nameColumnIndex = array_search('eventsair_id', $header);

//         if ($event_nameColumnIndex === false) {
//             throw new Exception('"eventsair_id" column not found in the CSV file header.');
//         }

//         // Execute queries for each row
//         while (($row = fgetcsv($file)) !== false) {
//             // Escape and sanitize each value
//             $escapedValues = array_map(
//                 function ($value) use ($conn) {
//                     return mysqli_real_escape_string($conn, $value);
//                 },
//                 $row
//             );

//             // Extract the eventsair_id value
//             $event_name = $escapedValues[$eventsairIdColumnIndex];

//             // Check if the eventsair_id already exists
//             $checkExistingQuery = "SELECT * FROM csv_upload_data WHERE eventsair_id = '$eventsairId'";
//             $existingResult = mysqli_query($conn, $checkExistingQuery);

//             if ($existingResult === false) {
//                 throw new Exception('Error checking existing eventsair_id: ' . mysqli_error($conn));
//             }

//             if (mysqli_num_rows($existingResult) > 0) {
//                 // eventsair_id already exists, update the existing row with the new data
//                 $updateQuery = "UPDATE csv_upload_data SET 
//                                 eventsair_id = '$escapedValues[0]',
//                                 salutation = '$escapedValues[1]', 
//                                 firstname = '$escapedValues[2]', 
//                                 lastname = '$escapedValues[3]', 
//                                 nationality = '$escapedValues[4]', 
//                                 email = '$escapedValues[5]', 
//                                 dateofbirth = '$escapedValues[6]', 
//                                 phonenumber = '$escapedValues[7]', 
//                                 city = '$escapedValues[8]', 
//                                 state_ = '$escapedValues[9]', 
//                                 countrycode = '$escapedValues[10]', 
//                                 timestamp_col = NOW() 
//                                 WHERE eventsair_id = '$eventsairId'";

//                 $updateResult = mysqli_query($conn, $updateQuery);

//                 if ($updateResult === false) {
//                     throw new Exception('Error updating row: ' . mysqli_error($conn));
//                 }

//                 // Log or display a message indicating that the row was updated
//             } else {
//                 // Build the SQL query for insertion
//                 $insertQuery = "INSERT INTO csv_upload_data (eventsair_id, salutation, firstname, lastname, nationality, email, dateofbirth, phonenumber, city, state_, countrycode, timestamp_col) 
//                                 VALUES ('$eventsairId', '" . implode("','", array_slice($escapedValues, 1)) . "', NOW())";

//                 $insertResult = mysqli_query($conn, $insertQuery);

//                 if ($insertResult === false) {
//                     throw new Exception('Error inserting row: ' . mysqli_error($conn));
//                 }

//                 // Log or display a message indicating that the row was inserted
//             }
//         }

//         // Commit the transaction
//         mysqli_commit($conn);

//         fclose($file);
//     } catch (Exception $e) {
//         // Rollback the transaction on exception
//         mysqli_rollback($conn);

//         // Rethrow the exception to be caught in the outer try-catch block
//         throw $e;
//     }
// }

?>
