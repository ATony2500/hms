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

<div class="form-card">
    <div class="form-card-header">
        <h3><i class="bi bi-calendar-plus"></i> <?php echo $editAppointment ? 'Edit Appointment' : 'Schedule New Appointment'; ?></h3>
        <p class="form-subtitle"><?php echo $editAppointment ? 'Update appointment details below' : 'Fill in the details to schedule a new appointment'; ?></p>
    </div>
    <form method="POST" action="" class="form-modern">
        <?php if ($editAppointment): ?>
            <input type="hidden" name="id" value="<?php echo $editAppointment['id']; ?>">
        <?php endif; ?>
        
        <div class="form-section">
            <h4 class="form-section-title">Appointment Details</h4>
            <div class="form-grid-3">
                <div class="form-group">
                    <label class="form-label">Patient *</label>
                    <select name="patient_id" class="form-input" required>
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
                    <label class="form-label">Doctor *</label>
                    <select name="doctor_id" class="form-input" required>
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
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="Scheduled" <?php echo ($editAppointment && $editAppointment['status'] == 'Scheduled') ? 'selected' : ''; ?>>📅 Scheduled</option>
                        <option value="Completed" <?php echo ($editAppointment && $editAppointment['status'] == 'Completed') ? 'selected' : ''; ?>>✅ Completed</option>
                        <option value="Cancelled" <?php echo ($editAppointment && $editAppointment['status'] == 'Cancelled') ? 'selected' : ''; ?>>❌ Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="form-grid-3">
                <div class="form-group">
                    <label class="form-label">Date *</label>
                    <input type="date" name="appointment_date" class="form-input" 
                        value="<?php echo $editAppointment['appointment_date'] ?? ''; ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Time *</label>
                    <input type="time" name="appointment_time" class="form-input" 
                        value="<?php echo $editAppointment['appointment_time'] ?? ''; ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-input textarea-input" rows="3" placeholder="Additional notes..."><?php echo $editAppointment['notes'] ?? ''; ?></textarea>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-submit">
                <i class="bi bi-check-circle"></i> <?php echo $editAppointment ? 'Update Appointment' : 'Schedule Appointment'; ?>
            </button>
            <?php if ($editAppointment): ?>
                <a href="appointments.php" class="btn btn-reset">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-calendar-week"></i> All Appointments</h3>
        <div class="table-stats">
            <span class="badge bg-primary"><?php echo $appointments->num_rows; ?> Total</span>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col"><i class="bi bi-person"></i> Patient</th>
                    <th scope="col"><i class="bi bi-person-badge"></i> Doctor</th>
                    <th scope="col"><i class="bi bi-calendar"></i> Date</th>
                    <th scope="col"><i class="bi bi-clock"></i> Time</th>
                    <th scope="col"><i class="bi bi-flag"></i> Status</th>
                    <th scope="col"><i class="bi bi-chat-left"></i> Notes</th>
                    <th scope="col"><i class="bi bi-gear"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($appointments->num_rows > 0): ?>
                    <?php while ($row = $appointments->fetch_assoc()): ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?php echo $row['id']; ?></span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-circle me-2 text-primary"></i>
                                    <strong><?php echo htmlspecialchars($row['patient_name']); ?></strong>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-hospital me-2 text-info"></i>
                                    <?php echo htmlspecialchars($row['doctor_name']); ?>
                                </div>
                            </td>
                            <td>
                                <span class="date-badge">
                                    <i class="bi bi-calendar-event"></i>
                                    <?php echo date('M d, Y', strtotime($row['appointment_date'])); ?>
                                </span>
                            </td>
                            <td>
                                <span class="time-badge">
                                    <i class="bi bi-clock-fill"></i>
                                    <?php echo date('h:i A', strtotime($row['appointment_time'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $statusClass = '';
                                $statusIcon = '';
                                switch(strtolower($row['status'])) {
                                    case 'scheduled':
                                        $statusClass = 'bg-primary';
                                        $statusIcon = '📅';
                                        break;
                                    case 'completed':
                                        $statusClass = 'bg-success';
                                        $statusIcon = '✅';
                                        break;
                                    case 'cancelled':
                                        $statusClass = 'bg-danger';
                                        $statusIcon = '❌';
                                        break;
                                }
                                ?>
                                <span class="badge <?php echo $statusClass; ?>">
                                    <?php echo $statusIcon . ' ' . $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $notes = htmlspecialchars($row['notes']);
                                if (strlen($notes) > 40) {
                                    echo '<span title="' . $notes . '">' . substr($notes, 0, 40) . '...</span>';
                                } else {
                                    echo $notes ?: '<em class="text-muted">No notes</em>';
                                }
                                ?>
                            </td>
                            <td class="actions">
                                <div class="btn-group" role="group">
                                    <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this appointment?')" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-calendar-x display-1 text-muted"></i>
                                <h4 class="mt-3 text-muted">No appointments found</h4>
                                <p class="text-muted">Schedule your first appointment using the form above.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include 'includes/footer.php';
?>