<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $allotment_id = $_POST['allotment_id'];
    $patient_name = $_POST['patient_name'];
    $room_id = $_POST['room_id'];
    $allotment_date = $_POST['allotment_date'];

    // Update the allotment
    $sql = "UPDATE allotments SET patient_name='$patient_name', room_id='$room_id', allotment_date='$allotment_date' WHERE allotment_id='$allotment_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?success=Allotment updated successfully!");
    } else {
        header("Location: index.php?error=Error updating allotment: " . mysqli_error($conn));
    }
    exit();
}
?>
