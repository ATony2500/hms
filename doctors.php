<?php
session_start();
require_once 'config/database.php';
requireLogin();

$message = '';

// Display message from session if it exists
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    
    $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, phone, email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $specialization, $phone, $email);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = '<div class="alert alert-success">Doctor added successfully!</div>';
        header("Location: doctors.php");
        exit();
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM doctors WHERE id = $id");
    $_SESSION['message'] = '<div class="alert alert-success">Doctor deleted successfully!</div>';
    header("Location: doctors.php");
    exit();
}

$pageTitle = 'Doctors';
include 'includes/header.php';
?>

<div class="doctors-container">
    <h2>Doctors Management (ola)</h2>

    <?php echo $message; ?>

    <div class="form-container">
        <h3>Add New Doctor</h3>
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" placeholder="Enter doctor's full name" required>
                </div>
                <div class="form-group">
                    <label>Specialization:</label>
                    <input type="text" name="specialization" placeholder="e.g., Cardiology, Neurology" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="text" name="phone" placeholder="Enter phone number" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" placeholder="Enter email address" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Add Doctor</button>
        </form>
    </div>

    <div class="table-container">
        <h3>All Doctors</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Specialization</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM doctors ORDER BY id DESC");
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><strong><?php echo $row['id']; ?></strong></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this doctor?')">Delete</a>
                    </td>
                </tr>
                <?php 
                    endwhile;
                } else {
                    echo '<tr><td colspan="6" style="text-align: center; color: #999; padding: 30px;">No doctors found. Add one to get started!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<style>
.doctors-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.form-container {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    border-left: 4px solid #0d6efd;
}

.form-container h3 {
    color: #0d6efd;
    margin-top: 0;
    margin-bottom: 20px;
    font-weight: 600;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #495057;
    font-weight: 500;
    font-size: 0.95rem;
}

.form-group input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    font-size: 0.95rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

.table-container {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow-x: auto;
}

.table-container h3 {
    color: #0d6efd;
    margin-top: 0;
    margin-bottom: 20px;
    font-weight: 600;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 0;
}

table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    font-size: 0.95rem;
}

table td {
    padding: 12px 15px;
    border-bottom: 1px solid #dee2e6;
    color: #495057;
}

table tbody tr {
    transition: background-color 0.2s ease;
}

table tbody tr:hover {
    background-color: #f8f9fa;
}

table tbody tr:last-child td {
    border-bottom: none;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
}

.btn-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(17, 153, 142, 0.3);
}

.btn-danger {
    background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
    color: white;
    text-decoration: none;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(238, 9, 121, 0.3);
}

.alert {
    padding: 15px 20px;
    border-radius: 5px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert-success {
    background-color: #d1e7dd;
    color: #0f5132;
    border-left: 4px solid #198754;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .doctors-container {
        padding: 10px;
    }
    
    .form-container,
    .table-container {
        padding: 15px;
    }
    
    table {
        font-size: 0.85rem;
    }
    
    table th,
    table td {
        padding: 8px 10px;
    }
}
</style>

