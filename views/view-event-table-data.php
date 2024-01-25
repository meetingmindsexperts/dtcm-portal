<?php
include_once '../includes/db.php';
include_once '../includes/header.php';

// Retrieve table name from the query string
$id = isset($_GET['id']) ? $_GET['id'] : '0';

// Fetch data from the specified table

$errors = [];
$data = [];
if ($eventName !== '') {
    $sql = "SELECT event_table_name, event_table_data FROM events_csv WHERE id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
            
        
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        //echo the table name to use for naming the download file
        $event_table_name =  $data[0]['event_table_name'];
        
        // Check if there is any data
        if (!empty($data)) {
            // Decode the JSON string in the 'tableData' field
            $tableData = json_decode($data[0]['event_table_data'], true);

            // Check if decoding was successful
            if (empty($tableData)) {
                echo "No data available for the specified table.";
                $errors[] = "No data available for the specified table.";
            } else {
                echo "<div>
                        <button class='btn btn-success' type='button' id='exportCsv'>Export as CSV</button>
                      </div>";

                // Generate HTML table
                echo '<div class="py-5 my-4"';
                echo '<table id="dynamicTable" class="table table-striped d-block overflow-scroll">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>#</th>';
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
                echo '</thead>';
                echo '<tbody>';

                foreach ($tableData as $index => $row) {
                    echo '<tr>';
                    foreach ($row as $value) {
                        echo '<td>' . $value . '</td>';
                    }
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
                echo '</div>';

                // Add pagination controls
                echo '<div class="d-flex align-items-center justify-content-between"><div id="pagination" class="pagination"></div>';
                echo "<div>
                <label for='itemsPerPage'>Items per page:</label>
                <select id='itemsPerPage' onchange='updatePagination()'>
                    <option value='10'>10</option>
                    <option value='25'>25</option>
                    <option value='50'>50</option>
                    <option value='100'>100</option>
                    <option value='150'>150</option>
                </select>
              </div>";
               
            }
        } else {
            echo "No data available for the specified table.";
            $errors[] = "No data available for the specified table.";
        }
    }
    foreach ($errors as $error) {
        echo '<div class="text-danger">';
        echo $error;
        echo '</div></div>';
    }

}


?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const exportCsv = document.getElementById('exportCsv');
        const table = document.getElementById('dynamicTable');
        const rows = table.querySelectorAll('tbody tr');
        const itemsPerPageDropdown = document.getElementById('itemsPerPage');
        const pagination = document.getElementById('pagination');

        // Initialize pagination
        let currentPage = 1;
        let itemsPerPage = parseInt(itemsPerPageDropdown.value);

        // Display initial page
        showPage(currentPage);

        // Export CSV event listener
        exportCsv.addEventListener('click', () => {
            const currentPageRows = getVisibleRows(currentPage, itemsPerPage);
            const csvContent = [];

            // Get header
            const header = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
            csvContent.push(header.join(','));

            // Get data for the current page
            currentPageRows.forEach(row => {
                const rowData = Array.from(row.children).map(td => td.textContent.trim());
                csvContent.push(rowData.join(','));
            });

            // Create CSV content
            const csv = csvContent.join('\n');

            // Create a Blob and a download link
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = 'eventid_<?php echo $id . '_' . $event_table_name; ?>_page_' + currentPage + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });

        // Items per page dropdown event listener
        itemsPerPageDropdown.addEventListener('change', () => {
            itemsPerPage = parseInt(itemsPerPageDropdown.value);
            showPage(currentPage);
            updatePagination();
        });

        // Pagination controls
        updatePagination();

        // Function to update pagination controls
        function updatePagination() {
            const totalPages = Math.ceil(rows.length / itemsPerPage);
            pagination.innerHTML = '';

            for (let i = 1; i <= totalPages; i++) {
                const pageButton = document.createElement('button');
                pageButton.classList.add('btn');
                pageButton.textContent = i;
                pageButton.addEventListener('click', () => {
                    currentPage = i;
                    showPage(currentPage);
                });
                pagination.appendChild(pageButton);
            }
        }

        // Function to show a specific page
        function showPage(page) {
            rows.forEach(row => row.style.display = 'none');
            const currentPageRows = getVisibleRows(page, itemsPerPage);
            currentPageRows.forEach(row => row.style.display = 'table-row');
        }

        // Function to get the visible rows for a specific page
        function getVisibleRows(page, itemsPerPage) {
            const startIndex = (page - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            return Array.from(rows).slice(startIndex, endIndex);
        }
    });
</script>


<?php
$conn->close();
include_once '../includes/footer.php';
?>