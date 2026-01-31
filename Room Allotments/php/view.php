<?php
include 'db.php';
$result = mysqli_query($conn, "SELECT a.patient_name, r.room_number, a.allotment_date FROM allotments a JOIN rooms r ON a.room_id = r.room_id");
echo "<table border='1'><tr><th>Patient</th><th>Room</th><th>Date</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr><td>".$row['patient_name']."</td><td>".$row['room_number']."</td><td>".$row['allotment_date']."</td></tr>";
}
echo "</table>";
?>
