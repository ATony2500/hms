<?php
require_once 'config/database.php';
requireLogin();

// Get statistics
$patients_count = $conn->query("SELECT COUNT(*) as count FROM patients")->fetch_assoc()['count'];
$doctors_count = $conn->query("SELECT COUNT(*) as count FROM doctors")->fetch_assoc()['count'];
$appointments_count = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'Scheduled'")->fetch_assoc()['count'];

$pageTitle = 'Dashboard';
include 'includes/header.php';
?>

<h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
<div class="alert alert-success" role="alert">
    <i class="bi bi-person-circle"></i> Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!
</div>

<div class="dashboard-cards">
    <div class="dashboard-card">
        <i class="bi bi-people" style="font-size: 2rem;"></i>
        <h3><?php echo $patients_count; ?></h3>
        <p>Total Patients</p>
    </div>
    <div class="dashboard-card card-success">
        <i class="bi bi-person-badge" style="font-size: 2rem;"></i>
        <h3><?php echo $doctors_count; ?></h3>
        <p>Total Doctors</p>
    </div>
    <div class="dashboard-card card-danger">
        <i class="bi bi-calendar-check" style="font-size: 2rem;"></i>
        <h3><?php echo $appointments_count; ?></h3>
        <p>Scheduled Appointments</p>
    </div>
</div>

<div class="table-container">
    <h3><i class="bi bi-clock-history"></i> Recent Appointments</h3>
    <table class="table table-hover">
        <thead class="table-primary">
            <tr>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT a.*, p.name as patient_name, d.name as doctor_name 
                      FROM appointments a 
                      JOIN patients p ON a.patient_id = p.id 
                      JOIN doctors d ON a.doctor_id = d.id 
                      ORDER BY a.appointment_date DESC, a.appointment_time DESC 
                      LIMIT 5";
            $result = $conn->query($query);
            
            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><i class="bi bi-person"></i> <?php echo htmlspecialchars($row['patient_name']); ?></td>
                    <td><i class="bi bi-person-badge"></i> <?php echo htmlspecialchars($row['doctor_name']); ?></td>
                    <td><i class="bi bi-calendar"></i> <?php echo date('M d, Y', strtotime($row['appointment_date'])); ?></td>
                    <td><i class="bi bi-clock"></i> <?php echo date('h:i A', strtotime($row['appointment_time'])); ?></td>
                    <td><span class="status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
                </tr>
            <?php 
                endwhile;
            else:
            ?>
                <tr><td colspan="5" class="text-center text-muted">No appointments found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
