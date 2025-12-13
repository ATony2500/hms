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
    // If update_id is present, perform update; otherwise insert new record
    if (isset($_POST['update_id']) && !empty($_POST['update_id'])) {
        $id = intval($_POST['update_id']);
        $name = $_POST['name'];
        $specialization = $_POST['specialization'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];

        $stmt = $conn->prepare("UPDATE doctors SET name = ?, specialization = ?, phone = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $specialization, $phone, $email, $id);

        if ($stmt->execute()) {
            $_SESSION['message'] = '<div class="alert alert-success">Doctor updated successfully!</div>';
            header("Location: doctors.php");
            exit();
        }
    } else {
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
// get total doctors
$totalRes = $conn->query("SELECT COUNT(*) AS cnt FROM doctors");
$totalDoctors = 0;
if ($totalRes) { $rowCount = $totalRes->fetch_assoc(); $totalDoctors = isset($rowCount['cnt']) ? $rowCount['cnt'] : 0; }
?>

<div class="doctors-container">
    <h2>Doctors Management</h2>

    <?php echo $message; ?>

    <div class="header-actions">
        <div class="stats">
            <div class="stat-card">
                <div class="stat-value"><?php echo $totalDoctors; ?></div>
                <div class="stat-label">Total Doctors</div>
            </div>
        </div>
        <div class="controls">
            <input id="tableSearch" type="search" placeholder="Search doctors by name, specialization, phone, email..." class="search-input" oninput="filterDoctors()" />
            <button class="btn btn-success" onclick="exportTableToCSV('doctors.csv')">Export CSV</button>
            <button class="btn" onclick="location.reload()">Refresh</button>
        </div>
    </div>

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
        <table id="doctorsTable">
            <thead>
                        <tr>
                            <th class="sortable" onclick="sortTable(0)">ID</th>
                            <th class="sortable" onclick="sortTable(1)">Name</th>
                            <th class="sortable" onclick="sortTable(2)">Specialization</th>
                            <th class="sortable" onclick="sortTable(3)">Phone</th>
                            <th class="sortable" onclick="sortTable(4)">Email</th>
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
                    <td>
                        <div class="name-cell">
                            <div class="avatar"><?php echo strtoupper(mb_substr($row['name'], 0, 1)); ?></div>
                            <div class="name-text"><?php echo htmlspecialchars($row['name']); ?></div>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <button type="button" class="btn btn-primary" onclick="openEditModal(<?php echo $row['id']; ?>, <?php echo json_encode($row['name']); ?>, <?php echo json_encode($row['specialization']); ?>, <?php echo json_encode($row['phone']); ?>, <?php echo json_encode($row['email']); ?>)">Edit</button>
                        <button type="button" class="btn btn-danger" onclick="openDeleteModal(<?php echo $row['id']; ?>, <?php echo json_encode($row['name']); ?>)">Delete</button>
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
            <span class="modal-close" onclick="closeDeleteModal()">&times;</span>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete <strong id="doctorNameDisplay"></strong>?</p>
            <p style="color: #dc3545; font-size: 0.9rem; margin-top: 10px;">This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            <a id="confirmDeleteLink" href="#" class="btn btn-danger">Delete</a>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Doctor</h3>
            <span class="modal-close" onclick="closeEditModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="editForm" method="POST">
                <input type="hidden" name="update_id" id="update_id" value="">
                <div class="form-group">
                    <label for="edit_name">Name:</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="edit_specialization">Specialization:</label>
                    <input type="text" id="edit_specialization" name="specialization" required>
                </div>
                <div class="form-group">
                    <label for="edit_phone">Phone:</label>
                    <input type="text" id="edit_phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="edit_email">Email:</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('editForm').submit()">Save</button>
        </div>
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

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #6f8efb 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
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

/* Header actions */
.header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 18px;
}
.stat-card {
    background: white;
    padding: 16px 20px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 12px;
}
.stat-value {
    font-size: 1.6rem;
    font-weight: 700;
    color: #0d6efd;
}
.stat-label {
    color: #6c757d;
    font-size: 0.95rem;
}
.controls {
    display: flex;
    gap: 10px;
    align-items: center;
}
.search-input {
    padding: 10px 12px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    min-width: 260px;
}

/* Name/avatar */
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

/* Sortable header */
.sortable { cursor: pointer; }
.sortable:after { content: '\25B2'; opacity: 0.15; margin-left: 8px; font-size: 0.7rem; }
.sortable.sorted-asc:after { content: '\25B2'; opacity: 0.9; }
.sortable.sorted-desc:after { content: '\25BC'; opacity: 0.9; }

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    animation: fadeIn 0.3s ease;
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

.modal-header {
    padding: 20px;
    background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
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
    
    .modal-content {
        margin: 50% auto;
        max-width: 90%;
    }
}
</style>

<script>
function openDeleteModal(doctorId, doctorName) {
    const modal = document.getElementById('deleteModal');
    const deleteLink = document.getElementById('confirmDeleteLink');
    const doctorNameDisplay = document.getElementById('doctorNameDisplay');
    
    doctorNameDisplay.textContent = doctorName;
    deleteLink.href = '?delete=' + doctorId;
    
    modal.classList.add('show');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('show');
}

function openEditModal(doctorId, name, specialization, phone, email) {
    const modal = document.getElementById('editModal');
    document.getElementById('update_id').value = doctorId;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_specialization').value = specialization;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_email').value = email;

    modal.classList.add('show');
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.remove('show');
}

// Close modals when clicking outside of them
window.onclick = function(event) {
    const deleteModal = document.getElementById('deleteModal');
    const editModal = document.getElementById('editModal');
    if (event.target === deleteModal) {
        deleteModal.classList.remove('show');
    }
    if (event.target === editModal) {
        editModal.classList.remove('show');
    }
}

// Filter table rows based on search input
function filterDoctors() {
    const q = document.getElementById('tableSearch').value.toLowerCase().trim();
    const tbody = document.querySelector('#doctorsTable tbody');
    if (!tbody) return;
    Array.from(tbody.rows).forEach(row => {
        const cells = row.cells;
        // columns: 0 id, 1 name, 2 specialization, 3 phone, 4 email
        const text = (
            (cells[1] && cells[1].innerText) + ' ' +
            (cells[2] && cells[2].innerText) + ' ' +
            (cells[3] && cells[3].innerText) + ' ' +
            (cells[4] && cells[4].innerText)
        ).toLowerCase();
        row.style.display = q === '' || text.indexOf(q) !== -1 ? '' : 'none';
    });
}

// Sort table by column index (0-based)
function sortTable(colIndex) {
    const table = document.getElementById('doctorsTable');
    const tbody = table.tBodies[0];
    const rows = Array.from(tbody.rows);
    const header = table.tHead.rows[0].cells[colIndex];

    // determine sort direction
    let dir = 'asc';
    if (header.classList.contains('sorted-asc')) dir = 'desc';

    // remove sorted classes
    Array.from(table.tHead.querySelectorAll('.sortable')).forEach(h => h.classList.remove('sorted-asc','sorted-desc'));
    header.classList.add(dir === 'asc' ? 'sorted-asc' : 'sorted-desc');

    rows.sort((a,b) => {
        const A = (a.cells[colIndex] ? a.cells[colIndex].innerText.trim() : '').toLowerCase();
        const B = (b.cells[colIndex] ? b.cells[colIndex].innerText.trim() : '').toLowerCase();
        if (colIndex === 0) { // numeric id
            return dir === 'asc' ? (Number(A) - Number(B)) : (Number(B) - Number(A));
        }
        if (A < B) return dir === 'asc' ? -1 : 1;
        if (A > B) return dir === 'asc' ? 1 : -1;
        return 0;
    });

    // append in new order
    rows.forEach(r => tbody.appendChild(r));
}

// Export visible rows to CSV
function exportTableToCSV(filename = 'export.csv') {
    const table = document.getElementById('doctorsTable');
    const rows = [];
    // headers
    const headers = Array.from(table.tHead.rows[0].cells).map(h => h.innerText.trim());
    rows.push(headers.join(','));

    Array.from(table.tBodies[0].rows).forEach(r => {
        if (r.style.display === 'none') return; // skip hidden rows
        const cols = Array.from(r.cells).slice(0,5).map(c => '"' + c.innerText.replace(/"/g,'""') + '"');
        rows.push(cols.join(','));
    });

    downloadCSV(rows.join('\n'), filename);
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    if (navigator.msSaveBlob) { // IE 10+
        navigator.msSaveBlob(blob, filename);
    } else {
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}
</script>

