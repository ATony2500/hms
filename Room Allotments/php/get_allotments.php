<?php
header('Content-Type: application/json');

include 'db.php';

// Fetch all allotments from the database
$sql = "SELECT allotment_id, room_id, allotment_date, patient_name FROM allotments";
$result = mysqli_query($conn, $sql);

$allotments = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $allotments[] = $row;
    }
}

// Debug: Log what we're returning
error_log("Returning allotments: " . json_encode($allotments));

echo json_encode($allotments);
?>
