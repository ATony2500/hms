<?php
require_once 'config/database.php';
requireLogin();

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    
    $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, phone, email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $specialization, $phone, $email);
    
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Doctor added successfully!</div>';
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM doctors WHERE id = $id");
    $message = '<div class="alert alert-success">Doctor deleted successfully!</div>';
}

$pageTitle = 'Doctors';
include 'includes/header.php';
?>

<h2>Doctors Management(ola)</h2>

<?php echo $message; ?>

<h3>Add New Doctor</h3>
<form method="POST">
    <div class="form-group">
        <label>Name:</label>
        <input type="text" name="name" required>
    </div>
    <div class="form-group">
        <label>Specialization:</label>
        <input type="text" name="specialization" required>
    </div>
    <div class="form-group">
        <label>Phone:</label>
        <input type="text" name="phone" required>
    </div>
    <div class="form-group">
        <label>Email:</label>
        <input type="email" name="email" required>
    </div>
    <button type="submit" class="btn btn-success">Add Doctor</button>
</form>

<h3 style="margin-top: 30px;">All Doctors</h3>
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
        while ($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['specialization']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td>
                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>
