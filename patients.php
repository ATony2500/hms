<?php
require_once 'config/database.php';
requireLogin();

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    $stmt = $conn->prepare("INSERT INTO patients (name, age, gender, phone, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $name, $age, $gender, $phone, $address);
    
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Patient added successfully!</div>';
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM patients WHERE id = $id");
    $message = '<div class="alert alert-success">Patient deleted successfully!</div>';
}

$pageTitle = 'Patients';
include 'includes/header.php';
?>

<h2>Patients Management (Faizal)</h2>

<?php echo $message; ?>

<h3>Add New Patient</h3>
<form method="POST">
    <div class="form-group">
        <label>Name:</label>
        <input type="text" name="name" required>
    </div>
    <div class="form-group">
        <label>Age:</label>
        <input type="number" name="age" required>
    </div>
    <div class="form-group">
        <label>Gender:</label>
        <select name="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>
    </div>
    <div class="form-group">
        <label>Phone:</label>
        <input type="text" name="phone" required>
    </div>
    <div class="form-group">
        <label>Address:</label>
        <textarea name="address" rows="3"></textarea>
    </div>
    <button type="submit" class="btn btn-success">Add Patient</button>
</form>

<h3 style="margin-top: 30px;">All Patients</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = $conn->query("SELECT * FROM patients ORDER BY id DESC");
        while ($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo $row['age']; ?></td>
            <td><?php echo $row['gender']; ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td>
                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>
