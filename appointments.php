<?php
require_once 'config/database.php';
requireLogin();

$pageTitle = 'Appointments Management';
$message = '';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = '<div class="alert success">Appointment deleted successfully!</div>';
    } else {
        $message = '<div class="alert error">Error deleting appointment.</div>';
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $status = $_POST['status'] ?? 'Scheduled';
    $notes = $_POST['notes'] ?? '';
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update existing appointment
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE appointments SET patient_id=?, doctor_id=?, appointment_date=?, appointment_time=?, status=?, notes=? WHERE id=?");
        $stmt->bind_param("iissssi", $patient_id, $doctor_id, $appointment_date, $appointment_time, $status, $notes, $id);
        if ($stmt->execute()) {
            $message = '<div class="alert success">Appointment updated successfully!</div>';
        } else {
            $message = '<div class="alert error">Error updating appointment.</div>';
        }
    } else {
        // Insert new appointment
        $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $patient_id, $doctor_id, $appointment_date, $appointment_time, $status, $notes);
        if ($stmt->execute()) {
            $message = '<div class="alert success">Appointment created successfully!</div>';
        } else {
            $message = '<div class="alert error">Error creating appointment.</div>';
        }
    }
    $stmt->close();
}

// Get appointment for editing
$editAppointment = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM appointments WHERE id = $id");
    $editAppointment = $result->fetch_assoc();
}

// Fetch all appointments with patient and doctor names
$appointments = $conn->query("
    SELECT a.*, p.name as patient_name, d.name as doctor_name 
    FROM appointments a 
    LEFT JOIN patients p ON a.patient_id = p.id 
    LEFT JOIN doctors d ON a.doctor_id = d.id 
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");

// Fetch patients and doctors for dropdown
$patients = $conn->query("SELECT id, name FROM patients ORDER BY name");
$doctors = $conn->query("SELECT id, name, specialization FROM doctors ORDER BY name");

include 'includes/header.php';
?>

<h2>Appointments Management</h2>

<?php echo $message; ?>

<div class="form-container">
    <h3><?php echo $editAppointment ? 'Edit Appointment' : 'Schedule Appointment'; ?></h3>
    <form method="POST" action="">
        <?php if ($editAppointment): ?>
            <input type="hidden" name="id" value="<?php echo $editAppointment['id']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Patient:</label>
            <select name="patient_id" required>
                <option value="">Select Patient</option>
                
                <?php 
                $patients->data_seek(0);
                while ($patient = $patients->fetch_assoc()): 
                ?>
                    <option value="<?php echo $patient['id']; ?>" 
                        <?php echo ($editAppointment && $editAppointment['patient_id'] == $patient['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($patient['name']); ?>
                    </option>
                <?php endwhile; ?>
                </select>
        </div>

        <div class="form-group">
            <label>Doctor:</label>
            <select name="doctor_id" required>
                <option value="">Select Doctor</option>
                <?php 
                $doctors->data_seek(0);
                while ($doctor = $doctors->fetch_assoc()): 
                ?>
                    <option value="<?php echo $doctor['id']; ?>" 
                        <?php echo ($editAppointment && $editAppointment['doctor_id'] == $doctor['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($doctor['name']) . ' - ' . htmlspecialchars($doctor['specialization']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Date:</label>
            <input type="date" name="appointment_date" 
                value="<?php echo $editAppointment['appointment_date'] ?? ''; ?>" required>
        </div>

        <div class="form-group">
            <label>Time:</label>
            <input type="time" name="appointment_time" 
                value="<?php echo $editAppointment['appointment_time'] ?? ''; ?>" required>
        </div>

        <div class="form-group">
            <label>Status:</label>
            <select name="status">
                <option value="Scheduled" <?php echo ($editAppointment && $editAppointment['status'] == 'Scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                <option value="Completed" <?php echo ($editAppointment && $editAppointment['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                <option value="Cancelled" <?php echo ($editAppointment && $editAppointment['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
            </select>
        </div>

        <div class="form-group">
            <label>Notes:</label>
            <textarea name="notes" rows="3"><?php echo $editAppointment['notes'] ?? ''; ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            <?php echo $editAppointment ? 'Update Appointment' : 'Add Appointment'; ?>
        </button>
        <?php if ($editAppointment): ?>
            <a href="appointments.php" class="btn btn-secondary">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-container">
    <h3>All Appointments</h3>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Patient</th>
                <th scope="col">Doctor</th>
                <th scope="col">Date</th>
                <th scope="col">Time</th>
                <th scope="col">Status</th>
                <th scope="col">Notes</th>
                <th scope="col">Actions</th>
    </tr>
        </thead>
        <tbody>
            <?php if ($appointments->num_rows > 0): ?>
                <?php while ($row = $appointments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['appointment_date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($row['appointment_time'])); ?></td>
                        <td><span class="status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
                        <td><?php echo htmlspecialchars(substr($row['notes'], 0, 50)) . (strlen($row['notes']) > 50 ? '...' : ''); ?></td>
                        <td class="actions">
                            <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-edit">Edit</a>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this appointment?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center;">No appointments found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
include 'includes/footer.php';
?>