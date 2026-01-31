<?php
include 'db.php';
if (isset($_POST['submit'])) {
    $patient_name = $_POST['patient_name'];
    $room_id = $_POST['room_id'];
    $date = $_POST['allotment_date'];

    // Insert allotment
    $sql = "INSERT INTO allotments (patient_name, room_id, allotment_date) VALUES ('$patient_name', '$room_id', '$date')";
    
    // Update room status to occupied
    $updateRoom = "UPDATE rooms SET status='Occupied' WHERE room_id='$room_id'";

    if (mysqli_query($conn, $sql) && mysqli_query($conn, $updateRoom)) {
        echo "Room allotted successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

