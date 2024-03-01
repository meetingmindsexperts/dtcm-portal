<?php
include_once '../includes/auth.php';
include_once '../includes/db.php';
include_once '../includes/header.php';

// Initialize an array to store messages and errors
$messages = [];
$errors = $_SESSION['errors'] = [];

// Get the ID from the URL
$id = isset($_GET['id']) ? $_GET['id'] : '';

if ($id === '') {
    $errors[] = "Invalid or missing ID parameter";
} else {
    $sql = "SELECT * FROM events_csv WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $eventName = $row['event_name'];
        $performanceCode = $row['performance_code'];
        $csvFile = $row['csv_file']; 

        //for sending the eventname to view-barcode.php
        $tableNameEvent = strtolower(str_replace(' ', '', $eventName));

        //actual path where the csv file is located below
        $csvFilePath = $csvFile;
        if (file_exists($csvFilePath)) {
            $csvData = file_get_contents($csvFilePath);
            // Continue with parsing and processing
        } else {
            $errors[] = "File does not exist: " . $csvFilePath;
        }
        

        // Parse CSV data
        $csvRows = explode("\n", $csvData);
        
    } else {
        // Display an error message and investigate the issue
        $errors[] = "No rows found in the events_csv table";
    }

    //if (isset($_SESSION['errors'])) {
    
    //};
    echo "<div class='px-lg-5 mt-5'>
            <div class='d-flex flex-wrap flex-lg-nowrap align-items-center justify-content-between'>
                <h1 class='mb-5'>View CSV Data</h1>
                <div>
                    <a href='" . $baseUrl . "/views/csv-upload.php' class='d-inline-block btn btn-warning'>Back to CSV</a>
                    <button class='d-inline-block btn btn-primary d-none' id='exportCsv'>Export CSV</button>
                </div>
            </div>";

    // display errors
    foreach ($errors as $error) {
        echo '<div class="d-block opacity-100 toast mb-4" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body">
                '.$error.'</div>
            </div>' ;   
    }
    echo "<div class='d-flex flex-wrap flex-lg-nowrap align-items-center justify-content-between'>
            <div>
                <h2 class='mb-4'>Event Name: " . $eventName . "</h2>
                <h6 class='mb-4'>Performance code: " . $performanceCode . "</h6>
            </div>
            <div class='d-flex'>
                <div class='generate_barcode m-2'>
                    <button class='btn btn-lg btn-success' id='generateBarcode'>Generate Barcode</button>
                </div>
                <div class='upload_csv m-2'>
                    <button class='btn btn-lg btn-success d-none' id='uploadBarcode'>Upload Barcode</button>
                </div>
            </div>
        </div>";

    echo "<div class='errors' id='errorMessage'></div>";
    echo "<table class='py-5 table table-striped d-block overflow-scroll' id='dynamicTable'>";
    echo "<thead><tr class='header-rows'><th>#</th>";

    // Assuming the first row in the CSV contains headers
    $headers = array_shift($csvRows);
    $headerColumns = str_getcsv($headers);

    foreach ($headerColumns as $header) {
        echo "<th class='header-cols'>$header</th>";
    }


    echo "<th class='header-cols'>Basket ID</th>";
    echo "<th class='header-cols'>Customer ID</th>";
    echo "</tr></thead><tbody>";

    foreach ($csvRows as $index => $csvRow) {
        if (empty($csvRow)) {
            continue; // Skip empty rows
        }

        $rowData = str_getcsv($csvRow);

        echo "<tr class='value-rows'><td>$index</td>";

        // Display CSV data
        foreach ($rowData as $value) {
            echo "<td class='cols'>$value</td>";
        }

        // Display additional columns (empty for now, as we'll generate the barcode in the next part)
        echo "<td class='cols basketid'></td>";
        echo "<td class='cols customerid'></td>";
        echo "</tr>";
    }

    echo "</tbody></table></div>";


}

?>

<script type="text/javascript">
    window.onbeforeunload = function() { return "Your work will be lost."; };
    document.addEventListener('DOMContentLoaded', function() {
        // Your script here
        window.onbeforeunload = function() {
            return "Your work will be lost.";
        };
    });

    document.addEventListener('DOMContentLoaded', function () {
        const exportCsv = document.getElementById('exportCsv');

        const uploadDataButton = document.getElementById('uploadBarcode');
        const generateBarcodeButton = document.getElementById('generateBarcode');
        const id = <?php echo json_encode($id); ?>;
        const performanceCode = "<?php echo $performanceCode; ?>";
        const eventTableName = "<?php echo $tableNameEvent; ?>";
        generateBarcodeButton.addEventListener('click', async function () {

            //alert('I am clicked');
            console.log('Generate Barcode button is clicked');
            const valueRows = document.querySelectorAll('.value-rows');

            for (let i = 0; i < valueRows.length; i++) {
                const rowData = valueRows[i].querySelectorAll('.cols');
                const basketData = {
                    "area": rowData[8].innerText,
                    "pricetypecode": rowData[9].innerText
                };
                const customerData = {
                    "eventsairid": rowData[0].innerText,
                    "salutation": rowData[1].innerText,
                    "firstname": rowData[2].innerText,
                    "lastname": rowData[3].innerText,
                    "nationality": rowData[4].innerText,
                    "email": rowData[5].innerText,
                    "phonenumber": rowData[6].innerText,
                    "countrycode": rowData[7].innerText
                };

                // Make an asynchronous request to generate the barcode
                const response = await fetch('generate-barcode.php?id=' +id, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ eventTableName: eventTableName, performance_code: performanceCode, basketData, customerData }),
                });


                if (response.ok) {
                    barcodeDetails = await response.json();
                    //console.log("barcodeDetails json repsone:", barcodeDetails);

                    // Update the corresponding cells in the table with the generated barcode details
                    rowData[10].innerText = barcodeDetails.orderId;
                    rowData[11].innerText = barcodeDetails.barcode;
                    rowData[12].innerText = barcodeDetails.basketId;
                    rowData[13].innerText = barcodeDetails.customerId;

                    // Display the "Upload Data" button
                    uploadDataButton.classList.remove('d-none');
                    exportCsv.classList.remove('d-none');
                    // Check if there are any errors in the response
                    if (barcodeDetails.errors) {
                        // Handle and display errors on the page
                        console.error('Error:', barcodeDetails.errors);
                        // Update the UI to show the errors to the user
                        document.getElementById('errorMessage').innerText = barcodeDetails.errors.join('\n');
                    } else {
                        // Process the successful response and update UI as needed
                        console.log('Success:', barcodeDetails);
                    }
                    // Check if there are any errors in the response
                    if (barcodeDetails.messages) {
                        // Handle and display errors on the page
                        console.error('Error:', barcodeDetails.messages);
                        // Update the UI to show the errors to the user
                        document.getElementById('errorMessage').innerText = barcodeDetails.messages.join('\n');
                    }

                } else {
                    // Hide the "Upload Data" button in case of an error
                    uploadDataButton.classList.add('d-none');
                    exportCsv.classList.add('d-none');

                    console.error('Error:', response.status, response.statusText);
                    const errorText = await response.text();
                    console.error('Error Response:', errorText);
                }
            }
        });

        // Handle the "Upload Data" button click using AJAX
        uploadDataButton.addEventListener('click', async function () {
            // Extract data from the dynamically created table
            const dynamicTable = document.getElementById('dynamicTable');
            const rows = dynamicTable.getElementsByTagName('tr');
            const id = <?php echo json_encode($id); ?>;
            const eventName = <?php echo json_encode($eventName); ?>;

            const tableData = [];

            for (let i = 1; i < rows.length; i++) { // Start from 1 to skip header row
                const cols = rows[i].getElementsByTagName('td');
                const rowData = [];

                for (let j = 0; j < cols.length; j++) {
                    rowData.push(cols[j].innerText);
                }

                tableData.push(rowData);
            }
            console.log(tableData);

            // Make an asynchronous request to upload-barcode.php with tableData
            const uploadResponse = await fetch('upload-barcode.php?id=' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id, tableData, eventName: eventName }),
            });
            
            if (uploadResponse.ok) {
                uploadedDetails = await uploadResponse.json();
                const tableName = uploadedDetails.tableName;
                console.log('Table Name:', tableName);
                //console.log(uploadedDetails);
                // Redirect or handle success as needed
                alert('Data Uploaded successfully');
                console.log('Data uploaded successfully');
                setTimeout(function () {
                    window.location.href = 'view-barcodes.php?eventName='+tableName+'&id=<?php echo $id; ?>';
                }, 4000);

                if (uploadedDetails.errors) {
                    // Handle and display errors on the page
                    console.error('Error:', uploadedDetails.errors);
                    // Update the UI to show the errors to the user
                    document.getElementById('errorMessage').innerText = uploadedDetails.errors.join('\n');
                } else {
                    // Process the successful response and update UI as needed
                    console.log('Success:', uploadedDetails);
                }
            } else {
                console.error('Error uploading data:', uploadResponse.status, uploadResponse.statusText);
                const uploadErrorText = await uploadResponse.text();
                console.error('Upload Error Response:', uploadErrorText);
            }
        });

        exportCsv.addEventListener('click', () => {
            const table = document.getElementById('dynamicTable');
            const rows = table.querySelectorAll('tbody tr');
            const csvContent = [];

            // Get header
            const header = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
            csvContent.push(header.join(','));

            // Get data
            rows.forEach(row => {
                const rowData = Array.from(row.querySelectorAll('td')).map(td => td.textContent.trim());
                csvContent.push(rowData.join(','));
            });

            // Create CSV content
            const csv = csvContent.join('\n');
            
            // Create a Blob and a download link
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = "<?php echo $eventName; ?>" + "_table_data.csv";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });
    });
</script>

<?php include_once '../includes/footer.php'; ?>
