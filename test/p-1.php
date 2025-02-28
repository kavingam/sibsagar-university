<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Room Setup</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
  <h2>Room Setup</h2>

  <div class="mb-3">
    <label for="totalRooms" class="form-label">Total Rooms</label>
    <input type="number" id="totalRooms" class="form-control" placeholder="Enter total number of rooms" />
  </div>

  <button id="generateTable" class="btn btn-primary">Generate Room Tables</button>

  <div id="roomTable" class="mt-3"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('generateTable').addEventListener('click', function() {
    const totalRooms = document.getElementById('totalRooms').value;
    const roomTableContainer = document.getElementById('roomTable');
    roomTableContainer.innerHTML = '';  // Clear the previous table if any

    if (totalRooms && totalRooms > 0) {
      let tableHTML = '';

      // Loop to generate the structure for each room
      for (let i = 1; i <= totalRooms; i++) {
        tableHTML += `
          <div class="container mt-5">
            <h2>room - ${i}</h2>
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>left side room</th>
                  <th>right side room</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <table class="table table-bordered">
                      <tbody>
                        <tr>
                          <td></td>
                          <td></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  <td>
                    <table class="table table-bordered">
                      <tbody>
                        <tr>
                          <td></td>
                          <td></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        `;
      }

      roomTableContainer.innerHTML = tableHTML;
    } else {
      alert('Please enter a valid number of rooms.');
    }
  });
</script>

</body>
</html>
