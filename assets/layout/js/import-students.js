$(document).ready(function () {
    $("#triggerImportOptions").click(function () {
        var selectedImportOption = $("#importOptions").val();
        
        if (selectedImportOption === "0") {
            $("#showImportOptions").html(`
                <div class="container d-flex justify-content-center align-items-center vh-100">
                    <img src="assets/images/siblogomain.png" alt="Centered Image" class="img-fluid opacity-25">
                </div>
            `);
        }
        else if (selectedImportOption === "3") {
            $("#showImportOptions_sidebar").html(`
                <div class="w-100">
                    <p>Enter Student Table Name: </p>
                    <input type="text" id="userTextInput" placeholder="Type something here...">
    
                    <p class="mt-2">warning import file .csv format</p>
                    <div class="file-upload">
                        <input type="file" id="csvFileInput" accept=".csv">
                    </div>
    
                    <button id="importBtn" style="display: none;">Import</button>
    
                    <div id="selectors" style="display: none;">
                        <label for="selectAll">
                            <input type="radio" id="selectAll" name="viewOption" checked> Select All
                        </label>
                        <label for="selectColumnOnly">
                            <input type="radio" id="selectColumnOnly" name="viewOption"> Select Specific Column
                        </label>
                        <br>
                        <label for="colSelect">Select Column:</label>
                        <select id="colSelect"></select>
                        <br>
                        <button id="addColumnBtn">Add Column</button>
                        <button id="removeColumnBtn">Remove Column</button>
                    </div>
                </div>
            `);
            $("#showImportOptions").html(`
                <div id="csv-content"></div>
                <div class="array-output" id="arrayOutput"></div>
            `);
        }
    });

    let csvRows = [];
    let headers = [];
    let selectedColumns = [];

    // Handle the file input change event
    $(document).on('change', '#csvFileInput', function(event) {
        const file = event.target.files[0];

        if (file && file.name.endsWith('.csv')) {
            const reader = new FileReader();
            
            // Read the file as text
            reader.onload = function(e) {
                const csvData = e.target.result;
                csvRows = csvData.split('\n').map(row => row.split(','));
                headers = csvRows[0]; // First row is the header
                csvRows.shift(); // Remove the first row (header)
                displayCSV(csvRows, headers);
                populateSelectors(csvRows, headers);
                $('#importBtn').show();  // Ensure button is visible
            };
            
            reader.readAsText(file);
        } else {
            alert('Please select a valid CSV file.');
        }
    });

    // Function to display the CSV content in the container
    function displayCSV(csvRows, headers) {
        let htmlContent = '<div class="table-responsive"><table class="table table-primary table-bordered align-middle"><thead class="table-success text-center"><tr>';
        
        headers.forEach(header => {
            htmlContent += `<th>${header.trim()}</th>`;
        });
        htmlContent += '</tr></thead>';

        csvRows.forEach(cells => {
            htmlContent += '<tbody class="table-group-dividerx"><tr>';
            cells.forEach(cell => {
                htmlContent += `<td>${cell.trim()}</td>`;
            });
            htmlContent += '</tr>';
        });
        htmlContent += '</tbody></table>';

        $('#csv-content').html(htmlContent);
    }

    // Function to populate column selectors
    function populateSelectors(csvRows, headers) {
        const colSelect = $('#colSelect');
        colSelect.empty(); // Clear existing options

        headers.forEach((header, index) => {
            colSelect.append(`<option value="${index}">${header.trim()}</option>`);
        });

        $('#selectors').show();

        $('#addColumnBtn').off('click').on('click', addColumn);
        $('#removeColumnBtn').off('click').on('click', removeColumn);
    }

    // Add column to the selected list
    function addColumn() {
        const selectedCol = $('#colSelect').val();
        if (!selectedColumns.includes(selectedCol)) {
            selectedColumns.push(selectedCol);
        }
        updateDisplay();
    }

    // Remove column from the selected list
    function removeColumn() {
        const selectedCol = $('#colSelect').val();
        const index = selectedColumns.indexOf(selectedCol);
        if (index > -1) {
            selectedColumns.splice(index, 1);
        }
        updateDisplay();
    }

    // Update display based on selected columns or "Select All"
    function updateDisplay() {
        const isSelectAll = $('#selectAll').is(':checked');
        const isSelectColumnOnly = $('#selectColumnOnly').is(':checked');

        if (isSelectAll) {
            selectedColumns = [];
            displayCSV(csvRows, headers); // Show all columns with highlights
            highlightAllRowsAndColumns(); // Highlight all rows and columns
        } else if (isSelectColumnOnly) {
            let htmlContent = '<div class="table-responsive"><table class="table table-primary table-bordered align-middle"><thead class="table-success text-center"><tr>';
            headers.forEach((header, index) => {
                const isHighlighted = selectedColumns.includes(String(index)) ? ' class="table-warning text-dark"' : '';
                htmlContent += `<th${isHighlighted}>${header.trim()}</th>`;
            });
            htmlContent += '</tr></thead>';

            csvRows.forEach(cells => {
                htmlContent += '<tr>';
                cells.forEach((cell, index) => {
                    const isHighlighted = selectedColumns.includes(String(index)) ? ' class="bg-success text-light"' : '';
                    htmlContent += `<td${isHighlighted}>${cell.trim()}</td>`;
                });
                htmlContent += '</tr>';
            });
            htmlContent += '</table></div>';

            $('#csv-content').html(htmlContent);
        }
    }

    // Highlight all rows and columns when "Select All" is clicked
    function highlightAllRowsAndColumns() {
        // Add highlight class to all header and table cells
        $('th').addClass('highlight');
        $('td').addClass('highlight');
    }
/*
// Handle the import button click
$(document).on('click', '#importBtn', function() {
    let selectedData = [];

    // If "Select All" is checked, select all columns
    if ($('#selectAll').is(':checked')) {
        headers.forEach((header, index) => {
            const columnData = {
                header: header,
                data: csvRows.map(row => row[index].trim())
            };
            selectedData.push(columnData);
        });
    } else if ($('#selectColumnOnly').is(':checked')) {
        // Send only the selected columns
        selectedColumns.forEach(colIndex => {
            const columnData = {
                header: headers[colIndex],
                data: csvRows.map(row => row[colIndex].trim())
            };
            selectedData.push(columnData);
        });
    }

    const userText = $('#userTextInput').val();
    const postData = {
        text: userText,
        csvData: selectedData
    };

    console.log("Data to send via POST:", postData);

    fetch('process_csv.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(postData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Success:', data);
        
        // Check for errors in the response
        if (data.error) {
            // Display a popup with the error message, including the table name
            Swal.fire({
                title: 'Error!',
                text: data.error,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else {
            displayTable(data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while importing data.');
    });
});
*/
$(document).on('click', '#importBtn', function() {
    // ... (Your existing data gathering code) ...
    let selectedData = [];

    // If "Select All" is checked, select all columns
    if ($('#selectAll').is(':checked')) {
      headers.forEach((header, index) => {
        const columnData = {
          header: header,
          data: csvRows.map(row => row[index].trim())
        };
        selectedData.push(columnData);
      });
    } else if ($('#selectColumnOnly').is(':checked')) {
      // Send only the selected columns
      selectedColumns.forEach(colIndex => {
        const columnData = {
          header: headers[colIndex],
          data: csvRows.map(row => row[colIndex].trim())
        };
        selectedData.push(columnData);
      });
    }
  
    const userText = $('#userTextInput').val();
    const postData = {
      text: userText,
      csvData: selectedData
    };
    console.log("Data to send via POST:", postData); // Keep this for checking data

    fetch('process_csv.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(postData)
    })
    .then(response => {
        console.log("Raw Response:", response); // Log the raw response object
        if (!response.ok) {
            return response.text().then(text => { // Get the error text from the response
                throw new Error(`HTTP error ${response.status}: ${text}`); // Throw a more detailed error
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Success:', data);
        if (data.error) {
            alert(data.error);
        } else {
            alert('successfully create');
            // displayTable(data);
        }
    })
    .catch(error => {
        // console.error('Fetch Error:', error); // Log the full error object
        alert('An error occurred while importing data. Check the browser console for details.');
    });
});
    /* 0.1 error
    // Handle the import button click
    $(document).on('click', '#importBtn', function() {
        let selectedData = [];

        // If "Select All" is checked, select all columns
        if ($('#selectAll').is(':checked')) {
            headers.forEach((header, index) => {
                const columnData = {
                    header: header,
                    data: csvRows.map(row => row[index].trim())
                };
                selectedData.push(columnData);
            });
        } else if ($('#selectColumnOnly').is(':checked')) {
            // Send only the selected columns
            selectedColumns.forEach(colIndex => {
                const columnData = {
                    header: headers[colIndex],
                    data: csvRows.map(row => row[colIndex].trim())
                };
                selectedData.push(columnData);
            });
        }

        const userText = $('#userTextInput').val();
        const postData = {
            text: userText,
            csvData: selectedData
        };

        console.log("Data to send via POST:", postData);

        fetch('process_csv.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(postData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Success:', data);
            if (data.error) {
                alert(data.error);
            } else {
                displayTable(data);
            }
        })
        .then(data => {
            console.log('message:', data);
            if (data.error) {
                alert(data.error);
            } else {
                displayTable(data);
            }
        })

        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while importing data.');
        });
    });
    */

    // Function to display the received data as a table
    function displayTable(data) {
        const arrayOutput = $('#arrayOutput');
        arrayOutput.empty(); // Clear any existing content

        let table = `<h3>Table Name <span> ${data.receivedText}</span></h3>`;
        table += '<table border="1"><thead><tr>';

        data.receivedCsvData.forEach(column => {
            table += `<th>${column.header}</th>`;
        });
        table += '</tr></thead><tbody>';

        const rowCount = data.receivedCsvData[0].data.length;

        for (let i = 0; i < rowCount; i++) {
            table += '<tr>';
            data.receivedCsvData.forEach(column => {
                table += `<td>${column.data[i]}</td>`;
            });
            table += '</tr>';
        }

        table += '</tbody></table>';
        arrayOutput.html(table);
    }
});
