
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Room Allotments</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Site CSS -->
        <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="has-fixed-navbar">
        <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="../../dashboard.php">
                    <i class="bi bi-hospital"></i> Hospital Management System
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="../../dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="../../patients.php"><i class="bi bi-people"></i> Patients</a></li>
                        <li class="nav-item"><a class="nav-link" href="../../doctors.php"><i class="bi bi-person-badge"></i> Doctors</a></li>
                        <li class="nav-item"><a class="nav-link" href="../../appointments.php"><i class="bi bi-calendar-check"></i> Appointments</a></li>
                        <li class="nav-item"><a class="nav-link" href="../index.html"><i class="bi bi-house-door"></i> Room Allotments</a></li>
                        <li class="nav-item"><a class="nav-link" href="../../payments.php"><i class="bi bi-currency-dollar"></i> Payments</a></li>
                        <li class="nav-item"><a class="nav-link" href="../../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <a href="../index.html" class="back-link">← Back to Home</a>
    <form action="allot.php" method="POST">
    <label>Patient Name:</label>
    <input type="text" name="patient_name" required>
    <label>Select Room:</label>
    <select name="room_id">
        <option value="">-- Select a Room --</option>
        <optgroup label="Ground Floor (100's)">
            <option value="101">Room 101 (Single)</option>
            <option value="102">Room 102 (Double)</option>
            <option value="103">Room 103 (Single)</option>
            <option value="104">Room 104 (Double)</option>
            <option value="105">Room 105 (ICU)</option>
            <option value="106">Room 106 (Single)</option>
            <option value="107">Room 107 (Double)</option>
            <option value="108">Room 108 (Single)</option>
        </optgroup>
        <optgroup label="First Floor (200's)">
            <option value="201">Room 201 (Single)</option>
            <option value="202">Room 202 (Double)</option>
            <option value="203">Room 203 (ICU)</option>
            <option value="204">Room 204 (Single)</option>
            <option value="205">Room 205 (Double)</option>
            <option value="206">Room 206 (Single)</option>
            <option value="207">Room 207 (ICU)</option>
            <option value="208">Room 208 (Double)</option>
        </optgroup>
        <optgroup label="Second Floor (300's)">
            <option value="301">Room 301 (Single)</option>
            <option value="302">Room 302 (Double)</option>
            <option value="303">Room 303 (Single)</option>
            <option value="304">Room 304 (ICU)</option>
            <option value="305">Room 305 (Double)</option>
            <option value="306">Room 306 (Single)</option>
            <option value="307">Room 307 (Double)</option>
            <option value="308">Room 308 (ICU)</option>
        </optgroup>
    </select>

    <label>Allotment Date:</label>
    <input type="date" name="allotment_date" required>
    
    <button type="submit" name="submit">Allot Room</button>
</form>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
       background-color: #fdfdfd;
        margin: 0;
        padding: 0;
    }
    .back-link {
        display: inline-block;
        margin: 80px;
        padding: 10px 20px;
        background-color: #1f314a;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: all 0.3s;
        font-weight: 600;
    }
    .back-link:hover {
        background-color: #222533;
        transform: translateX(-3px);
        color: white;
    }
    form {
        max-width: 500px;
        margin: 30px auto;
        padding: 30px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    form label {
        display: block;
        margin-top: 20px;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }
    form input, form select {
        display: block;
        width: 100%;
        padding: 12px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }
    form input:focus, form select:focus {
        outline: none;
        border-color: #28a745;
        box-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
    }
    form button {
        width: 100%;
        padding: 12px;
        margin-top: 20px;
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    form button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    }
    form button:active {
        transform: translateY(0);
    }
    .table-container {
        min-height: 400px;
        padding: 30px 0;
    }
    table {
        width: 100%;
        max-width: 1500px;
        margin: 30px auto;
        border-collapse: collapse;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }
    table thead {
        background-color: #007bff;
        color: white;
    }
    table th {
        padding: 15px;
        text-align: left;
        font-weight: bold;
        border-bottom: 2px solid #ddd;
    }
    table td {
        padding: 12px 15px;
        border-bottom: 1px solid #ddd;
        background-color: #fff;
    }
    
    
    table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    h2 {
        text-align: center;
        color: #333;
        margin-top: 40px;
    }
    .delete-btn {
        padding: 6px 12px;
        background-color: #dc3545;
        color: white;
        border: none;
        width: 100px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }
    .delete-btn:hover {
        background-color: #c82333;
    }
    .edit-btn {
        padding: 6px 12px;
        background-color: #007bff;
        color: white;
        border: none;
         width: 100px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        margin-right: 5px;
    }
    .edit-btn:hover {
        background-color: #0056b3;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
    }
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        border-radius: 5px;
    }
    .modal-header {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 15px;
    }
    .close-modal {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    .close-modal:hover {
        color: black;
    }
    .modal input, .modal select {
        width: 100%;
        padding: 8px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .modal button {
        width: 100%;
        padding: 10px;
        background-color: #009c03;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
    }
    .modal button:hover {
        background-color: #ffffff;
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-weight: bold;
        text-align: center;
    }
    .status-available {
        background-color: #28a745;
        color: white;
    }
    .status-occupied {
        background-color: #dc3545;
        color: white;
    }
    .search-container {
        max-width: 500px;
        margin: 20px auto;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .search-container input {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        color: #333;
        background-color: white;
    }
    .search-container button {
        padding: 10px 15px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        white-space: nowrap;
        flex-shrink: 0;
        width: 200px;
    }
    .search-container button:hover {
        background-color: #0056b3;
    }
    </style>
    <script>

    </script>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeEditModal()">&times;</span>
        <div class="modal-header">Edit Allotment</div>
        <form action="update_allotment.php" method="POST">
            <input type="hidden" id="editAllotmentId" name="allotment_id">
            
            <label>Patient Name:</label>
            <input type="text" id="editPatientName" name="patient_name" required>
            
            <label>Room ID:</label>
            <select id="editRoomId" name="room_id" required>
                <optgroup label="Ground Floor (100's)">
                    <option value="101">Room 101 (Single)</option>
                    <option value="102">Room 102 (Double)</option>
                    <option value="103">Room 103 (Single)</option>
                    <option value="104">Room 104 (Double)</option>
                    <option value="105">Room 105 (ICU)</option>
                    <option value="106">Room 106 (Single)</option>
                    <option value="107">Room 107 (Double)</option>
                    <option value="108">Room 108 (Single)</option>
                </optgroup>
                <optgroup label="First Floor (200's)">
                    <option value="201">Room 201 (Single)</option>
                    <option value="202">Room 202 (Double)</option>
                    <option value="203">Room 203 (ICU)</option>
                    <option value="204">Room 204 (Single)</option>
                    <option value="205">Room 205 (Double)</option>
                    <option value="206">Room 206 (Single)</option>
                    <option value="207">Room 207 (ICU)</option>
                    <option value="208">Room 208 (Double)</option>
                </optgroup>
                <optgroup label="Second Floor (300's)">
                    <option value="301">Room 301 (Single)</option>
                    <option value="302">Room 302 (Double)</option>
                    <option value="303">Room 303 (Single)</option>
                    <option value="304">Room 304 (ICU)</option>
                    <option value="305">Room 305 (Double)</option>
                    <option value="306">Room 306 (Single)</option>
                    <option value="307">Room 307 (Double)</option>
                    <option value="308">Room 308 (ICU)</option>
                </optgroup>
            </select>
            
            <label>Allotment Date:</label>
            <input type="date" id="editAllotmentDate" name="allotment_date" required>
            
            <button type="submit">Update Allotment</button>
        </form>
    </div>
</div>

<script>
function openEditModal(allotmentId, patientName, roomId, allotmentDate) {
    document.getElementById('editAllotmentId').value = allotmentId;
    document.getElementById('editPatientName').value = patientName;
    document.getElementById('editRoomId').value = roomId;
    document.getElementById('editAllotmentDate').value = allotmentDate;
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

window.onclick = function(event) {
    var modal = document.getElementById('editModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

function filterTable() {
    var input = document.getElementById('searchInput');
    var filter = input.value.toUpperCase();
    var table = document.querySelector('table');
    var rows = table.getElementsByTagName('tr');
    
    for (var i = 1; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName('td');
        var patientName = cells[1] ? cells[1].textContent.toUpperCase() : '';
        var roomId = cells[2] ? cells[2].textContent.toUpperCase() : '';
        
        if (patientName.indexOf(filter) > -1 || roomId.indexOf(filter) > -1) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}

function resetSearch() {
    document.getElementById('searchInput').value = '';
    filterTable();
}
</script>

<h2>All Allotments</h2>

<!-- Search Box -->
<div class="search-container">
    <input type="text" id="searchInput" placeholder="Search by patient name or room ID..." onkeyup="filterTable()">
    <button onclick="resetSearch()">Reset</button>
</div>

<div class="table-container">
<table border="1" cellpadding="10" cellspacing="0">
    <thead>
        <tr>
            <th>Allotment ID</th>
            <th>Patient Name</th>
            <th>Room ID</th>
            <th>Allotment Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        include 'db.php';
        
        // Handle delete request
        if (isset($_GET['delete'])) {
            $allotment_id = $_GET['delete'];
            $delete_sql = "DELETE FROM allotments WHERE allotment_id='$allotment_id'";
            if (mysqli_query($conn, $delete_sql)) {
                echo "<div style='color: white; text-align: center; margin: 10px;'>Allotment deleted successfully!</div>";
            } else {
                echo "<div style='color: pink; text-align: center; margin: 10px;'>Error deleting allotment: " . mysqli_error($conn) . "</div>";
            }
        }
        
        $result = mysqli_query($conn, "SELECT * FROM allotments");
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Check if allotment date is today
                $today = date('Y-m-d');
                $allotment_date = $row['allotment_date'];
                $status = ($allotment_date == $today) ? 'Occupied' : 'Available';
                $status_class = ($status == 'Available') ? 'status-available' : 'status-occupied';
                
                echo "<tr>";
                echo "<td>" . $row['allotment_id'] . "</td>";
                echo "<td>" . $row['patient_name'] . "</td>";
                echo "<td>" . $row['room_id'] . "</td>";
                echo "<td>" . $row['allotment_date'] . "</td>";
                echo "<td><span class='status-badge $status_class'>" . $status . "</span></td>";
                echo "<td><button class='edit-btn' onclick='openEditModal(" . $row['allotment_id'] . ", \"" . $row['patient_name'] . "\", " . $row['room_id'] . ", \"" . $row['allotment_date'] . "\")'>Edit</button> <br>
                <a href='index.php?delete=" . $row['allotment_id'] . "' onclick='return confirm(\"Are you sure you want to delete this allotment?\");'><button class='delete-btn'>Delete</button></a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No allotments found</td></tr>";
        }
        ?>
    </tbody>
</table>
</div>

    </tbody>
</table>

</div>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>