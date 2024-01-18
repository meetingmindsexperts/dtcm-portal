<?php
include_once '../includes/db.php';
include_once '../includes/header.php';

// Retrieve table name from the query string
$eventName = isset($_GET['eventName']) ? mysqli_real_escape_string($conn, $_GET['eventName']) : '';
//  $eventName = 'testevent165a8ee68bc1c0';

// Fetch data from the specified table
$data = [];
if ($eventName !== '') {
    $sql = "SELECT table_data FROM $eventName";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        // Check if there is any data
        if (!empty($data)) {
            // Decode the JSON string in the 'tableData' field
            $tableData = json_decode($data[0]['table_data'], true);

            // Check if decoding was successful
            if (empty($tableData)) {
                echo "No data available for the specified table.";
            } else {


                echo "<div>
                <button class='btn btn-success' type='button' id='exportCsv'>Export as CSV</button>
                </div>
                ";

                // Generate HTML table
                echo '<table  id="exportTable" class="table table-striped">';
                echo '<tr>';
                //echo '<th>#</th>';
                echo '<th>ID</th>';
                echo '<th>Title</th>';
                echo '<th>First Name</th>';
                echo '<th>Last Name</th>';
                echo '<th>Country</th>';
                echo '<th>Email</th>';
                echo '<th>Phone</th>';
                echo '<th>Country Code</th>';
                echo '<th>Registration Type</th>';
                echo '<th>Price Type</th>';
                echo '<th>Order ID</th>';
                echo '<th>Barcode</th>';
                echo '<th>Basket ID</th>';
                echo '<th>Customer ID</th>';
                echo '</tr>';

                foreach ($tableData as $index => $row) {
                    echo '<tr>';
                    //echo '<td>' . ($index + 1) . '</td>'; // Index starts from 0, so adding 1
                    foreach ($row as $value) {
                        echo '<td>' . $value . '</td>';
                    }
                    echo '</tr>';
                }

                echo '</table>';
            }
        } else {
            echo "No data available for the specified table.";
        }
    }
}

?>
<script>
    const exportCsv = document.getElementById(exportCsv);
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
            a.download = 'final-' + <?php echo $eventName; ?> + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });

        </script>
<?
include_once '../includes/footer.php';
?>
