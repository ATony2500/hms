<?php
require_once 'config/database.php';
requireLogin();

$message = '';
// Get statistics
$patients_count = $conn->query("SELECT COUNT(*) as count FROM patients")->fetch_assoc()['count'];
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
   
    
    $stmt = $conn->prepare("INSERT INTO patients (name, age, gender, phone ) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $name, $age, $gender, $phone);
    
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
<div class="Dashboard-cards">
    <div class="Dashboard-card">
        <i style="font-size: 20px;"></i>
        <h3><?php echo $patients_count; ?></h3>
        <p>Total Patients</p>
    </div>
</div>
 <div class="controls">
            <input id="tableSearch" type="search" placeholder="Search patients by name, age, gender, phone..." class="search-input" oninput="filterPatients()" />
            <button class="btn btn-success" onclick="exportTableToCSV('patients.csv')">Export CSV</button>
            <button class="btn" onclick="location.reload()">Refresh</button>
        </div>
<div class="form-card">
    <div class="form-card-header text-center">
        <h3>Add New Patient</h3>
        <p class="form-subtitle">Fill in the patient details below</p>
    </div>
    <form method="POST" class=" patient-container">
        <!-- Personal Information Section -->
        <div class="form-section" class="form-container">
            <h4 class="form-section-title">Personal Information</h4>
            <div class="form-grid-3  ">
                <div class="form-group col-md-4">
                    <label for="name" class="form-label">Full Name </label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="age" class="form-label">Age </label>
                    <input type="number" id="age" name="age" class="form-input" min="0" max="150" placeholder="30" required>
                </div>
                <div class="form-group">
                    <label for="gender" class="form-label">Gender *</label>
                    <select id="gender" name="gender" class="form-input" required>
                        <option value="">-- Select --</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="form-section">
            <h4 class="form-section-title">Contact Information</h4>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-input" placeholder="(123) 456-7890">
                </div>
               
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="form-actions">
            <button type="submit" class="btn btn-submit">
                <span class="btn-icon">➕</span> Add Patient
            </button>
            <button type="reset" class="btn btn-reset">
                <span class="btn-icon">🔄</span> Clear Form
            </button>
        </div>
    </form>
</div>
    

<h3 style="margin-left: 500px">All Patients</h3>
<table style="margin-left: 50px; margin-bottom: 50px;">
    <thead>
        <tr>
            <th class="sortable" onclick="sortTable(0)">ID</th>
            <th class="sortable" onclick="sortTable(1)">Name</th>
            <th class="sortable" onclick="sortTable(2)">Age</th>
            <th class="sortable" onclick="sortTable(3)">Gender</th>
            <th class="sortable" onclick="sortTable(4)">Phone</th>
            
            <th class="sortable" onclick="sortTable(5)">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = $conn->query("SELECT * FROM patients ORDER BY id DESC");
         if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()):
        ?>
        <tr>
                    <td>
                        <div class="name-cell">
                        <div class="avatar"><?php echo strtoupper(mb_substr($row['name'], 0, 1)); ?></div>
                        <div class="name-text"><?php echo htmlspecialchars($row['name']); ?></div>
                        </div>
                    </td>
        
            
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['age']); ?></td>
            <td><?php echo htmlspecialchars($row['gender']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td>
                 
           
           
                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editPatientLabel">  Edit</button>
        </tr>
      <?php endwhile;
                } else {
                    echo '<tr><td colspan="6" style="text-align: center; color: #999; padding: 30px;">No patients found. Add one to get started!</td></tr>';
                }
                ?>
   


<!-- Button trigger modal -->


<!-- Modal 
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editPatientLabel" aria-hidden="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editPatientLabel">Edit Patient</h1>
        <button type="button" class="btn-close" data-bs-dismiss="Modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" class=" patient-container">
        Personal Information Section 
        <div class="form-section" class="form-container">
            <h4 class="form-section-title">Personal Information</h4>
            <div class="form-grid-12  ">
                <div class="form-group ">
                    <label for="name" class="form-label">Full Name </label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="" required>
                </div>
                <div class="form-group ">
                    <label for="age" class="form-label">Age </label>
                    <input type="number" id="age" name="age" class="form-input" min="0" max="150" placeholder="30" required>
                </div>
                <div class="form-group">
                    <label for="gender" class="form-label">Gender *</label>
                    <select id="gender" name="gender" class="form-input" required>
                        <option value="">-- Select --</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                 <div class="form-section">
            <h4 class="form-section-title">Contact Information</h4>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-input" placeholder="(123) 456-7890">
                </div>
               
            </div>
        </div>
            </div>
        </div>
        </form>
      </div>--> 
      <!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
  Launch static backdrop modal
</button>

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Modal title</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Understood</button>
      </div>
    </div>
  </div>
</div>
      
    </div>
  </div>
</div>
 </tbody>
</table>
<?php include 'includes/footer.php'; ?>

<style>
.patient-container {
    max-width: 8500px;
    
    margin: 0 auto;
    padding: 20px;
  
    border-radius: 10px;
    background: #ffffffff;
    padding-left: 50px;
    padding-right: 50px;
    
}

.form-container {
    background: white;
    padding: 20px;
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


.form-group input:focus {
    outline: none;
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
     transition: border-color 0.3s ease, box-shadow 0.3s ease;
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
    width: 80%;
    border-collapse: collapse;
    margin-top: 0;
    background-color: #ffffff;
}

table thead {
    background: linear-gradient(135deg, #66d6eaff 0%, #a0a24bff 100%);
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
    border-bottom: 1px solid #1082f5ff;
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
.controls {
    display: flex;
    gap: 10px;
    align-items: right;
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
.sortable { cursor: pointer; }
.sortable:after { content: '\25B2'; opacity: 0.15; margin-left: 8px; font-size: 0.7rem; }
.sortable.sorted-asc:after { content: '\25B2'; opacity: 0.9; }
.sortable.sorted-desc:after { content: '\25BC'; opacity: 0.9; }
.name-cell { display: flex; align-items: center; gap: 12px; }
.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg,#667eea 0%,#764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}
.name-text { font-weight: 600; color: #343a40; }
/* Modal Styles */
.modal {
    display: fixed;
    position: fixed;
    z-index: 1000;
    left: 700px;
    top: 0;
    width: 500px;
    height: 00px;
    overflow: none;
   
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 0;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    max-width: 400px;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
.Dashboard-card {
    background: linear-gradient(135deg, #667eea 3%, #a24b4bff 100%);
    color: white;
    padding-top: 5px;
    padding-bottom: 0px;
    padding-left: 10px;
    padding-right: 10px;
    border-radius: 100px;
   text-align: center;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;  
    width: 200px;
    height: 115px;
    float:center;
}
.Dashboard-cards {
   
    gap: 10px;
    margin-top: 20px;
    margin-bottom: 50px;
    width: 100px;
    height: 100px;
    
}
.modal-header {
    padding: 20px;
    background: linear-gradient(135deg, #ee0909ff 0%, #ff6a00 100%);
    color: white;
    border-radius: 10px 10px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.3rem;
}

.modal-close {
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.modal-close:hover {
    transform: scale(1.2);
}

.modal-body {
    padding: 20px;
    color: #495057;
}

.modal-body p {
    margin: 0 0 10px 0;
    line-height: 1.5;
}

.modal-footer {
    padding: 20px;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    border-top: 1px solid #dee2e6;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}

.modal.show {
    display: block;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .patients-container {
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
    
    .modal-content {
        margin: 50% auto;
        max-width: 90%;
    }
}
</style>

<script>
function filterPatients() {
    const input = document.getElementById('tableSearch');
    const filter = input.value.toLowerCase();
    const table = document.querySelector('table tbody');
    const rows = table.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let match = false;

        for (let j = 0; j < cells.length - 1; j++) { // Exclude actions column
            if (cells[j]) {
                const cellText = cells[j].textContent || cells[j].innerText;
                if (cellText.toLowerCase().indexOf(filter) > -1) {
                    match = true;
                    break;
                }
            }
        }

        rows[i].style.display = match ? '' : 'none';
    }
}
function openEditModal( name, age, gender, phone) {
    const modal = document.getElementById('editModal');
    document.getElementById('update_id').value = patientId;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_age').value = age;
    document.getElementById('edit_gender').value = gender;
    document.getElementById('edit_phone').value = phone;

    modal.classList.add('show');
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.remove('show');
}
</script>