<?php
session_start();
require_once 'config/database.php';
requireLogin();

$message = '';
// show message from session if set (set after insert)
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // collect and sanitize
    $patient_id = trim($_POST['patient_id']);
    $patient_name = trim($_POST['patient_name']);
    $department = trim($_POST['department']);
    $doctor_name = trim($_POST['doctor_name']);
    $admission_date = trim($_POST['admission_date']);
    $discharge_date = trim($_POST['discharge_date']);
    $service_name = trim($_POST['service_name']);
    $cost = floatval($_POST['cost']);
    $discount = floatval($_POST['discount']);
    $advance_paid = floatval($_POST['advance_paid']);
    $payment_type = trim($_POST['payment_type']);
    $card_check_details = isset($_POST['card_check_details']) ? trim($_POST['card_check_details']) : '';

    $total_due = $cost - $discount - $advance_paid;
    if ($total_due < 0) $total_due = 0;

    // create payments table if not exists
    $createSQL = "CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id VARCHAR(100),
        patient_name VARCHAR(255),
        department VARCHAR(255),
        doctor_name VARCHAR(255),
        admission_date DATE,
        discharge_date DATE,
        service_name VARCHAR(255),
        cost DECIMAL(10,2),
        discount DECIMAL(10,2),
        advance_paid DECIMAL(10,2),
        total_due DECIMAL(10,2),
        payment_type VARCHAR(50),
        card_check_details VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $conn->query($createSQL);
    $insertSQL = "INSERT INTO payments (patient_id, patient_name, department, doctor_name, admission_date, discharge_date, service_name, cost, discount, advance_paid, total_due, payment_type, card_check_details) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSQL);
    if ($stmt) {
        // param types: s=string, d=double
        $types = 'sssssssddddss';
        $stmt->bind_param($types, $patient_id, $patient_name, $department, $doctor_name, $admission_date, $discharge_date, $service_name, $cost, $discount, $advance_paid, $total_due, $payment_type, $card_check_details);
        if ($stmt->execute()) {
            $insert_id = $stmt->insert_id;
            // prepare payment array to return or store in session
            $paymentRow = [
                'id' => $insert_id,
                'patient_id' => $patient_id,
                'patient_name' => $patient_name,
                'department' => $department,
                'doctor_name' => $doctor_name,
                'admission_date' => $admission_date,
                'discharge_date' => $discharge_date,
                'service_name' => $service_name,
                'cost' => number_format($cost,2,'.',''),
                'discount' => number_format($discount,2,'.',''),
                'advance_paid' => number_format($advance_paid,2,'.',''),
                'total_due' => number_format($total_due,2,'.',''),
                'payment_type' => $payment_type,
                'card_check_details' => $card_check_details,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // If AJAX request, return JSON; otherwise use session + redirect
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($_POST['ajax']) && $_POST['ajax'] == '1');
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'payment' => $paymentRow]);
                $stmt->close();
                exit();
            }

            $_SESSION['message'] = '<div class="alert alert-success">Payment recorded successfully! Transaction ID: <strong>#' . $insert_id . '</strong></div>';
            header('Location: payments.php');
            exit();
        } else {
            $message = '<div class="alert alert-danger">Error saving payment: ' . htmlspecialchars($stmt->error) . '</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($conn->error) . '</div>';
    }
}

$pageTitle = 'Payments';
include 'includes/header.php';
?>

<?php
// Summary numbers
$totRes = $conn->query("SELECT COUNT(*) AS cnt, IFNULL(SUM(cost),0) AS total_revenue, IFNULL(SUM(total_due),0) AS total_outstanding FROM payments");
$totals = ['cnt'=>0,'total_revenue'=>0,'total_outstanding'=>0];
if ($totRes) { $totals = $totRes->fetch_assoc(); }
?>

<div class="doctors-container">
    <div class="page-header">
        <h2>Payments</h2>
        <div class="header-cards">
            <div class="stat-card">
                <div class="stat-label">Total Payments</div>
                <div class="stat-value" id="stat_total_payments"><?php echo number_format($totals['cnt']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value" id="stat_total_revenue"><?php echo number_format($totals['total_revenue'],2); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Outstanding</div>
                <div class="stat-value" id="stat_outstanding"><?php echo number_format($totals['total_outstanding'],2); ?></div>
            </div>
        </div>
    </div>

    <?php if (!empty($message)) echo $message; ?>

    <div class="form-container">
        <h3>Record a Payment</h3>
        <form id="paymentForm" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Patient ID</label>
                    <input type="text" name="patient_id" id="patient_id" placeholder="Patient ID" required>
                </div>
                <div class="form-group">
                    <label>Patient Name</label>
                    <input type="text" name="patient_name" id="patient_name" placeholder="Full name" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Department</label>
                    <input type="text" name="department" id="department" placeholder="Department e.g., Cardiology" required>
                </div>
                <div class="form-group">
                    <label>Doctor Name</label>
                    <input type="text" name="doctor_name" id="doctor_name" placeholder="Doctor" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Admission Date</label>
                    <input type="date" name="admission_date" id="admission_date">
                </div>
                <div class="form-group">
                    <label>Discharge Date</label>
                    <input type="date" name="discharge_date" id="discharge_date">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Service Name</label>
                    <input type="text" name="service_name" id="service_name" placeholder="e.g., Surgery" required>
                </div>
                <div class="form-group">
                    <label>Cost of Treatment</label>
                    <input type="number" step="0.01" name="cost" id="cost" placeholder="0.00" required oninput="recalc()">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Discount</label>
                    <input type="number" step="0.01" name="discount" id="discount" value="0.00" oninput="recalc()">
                </div>
                <div class="form-group">
                    <label>Advance Paid</label>
                    <input type="number" step="0.01" name="advance_paid" id="advance_paid" value="0.00" oninput="recalc()">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Payment Type</label>
                    <select name="payment_type" id="payment_type" onchange="toggleCardField()">
                        <option value="Cash">Cash</option>
                        <option value="Card">Card</option>
                        <option value="Check">Check</option>
                    </select>
                </div>
                <div class="form-group" id="card_check_group" style="display:none;">
                    <label>Card / Check Details</label>
                    <input type="text" name="card_check_details" id="card_check_details" placeholder="Card last 4 / Check #">
                </div>
            </div>
            <input type="hidden" name="ajax" value="0" id="ajax_flag">
            <div class="form-row">
                <div class="form-group">
                    <label>Total Due</label>
                    <input type="text" id="total_due_display" readonly>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Submit Payment</button>
        </form>
    </div>
    
            <!-- Recent payments -->
            <div class="table-container" style="margin-top:20px;">
                <h3>Recent Payments</h3>
                <table id="paymentsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Doctor / Dept</th>
                            <th>Service</th>
                            <th>Cost</th>
                            <th>Total Due</th>
                            <th>Payment Type</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $pRes = $conn->query("SELECT * FROM payments ORDER BY id DESC LIMIT 10");
                        if ($pRes && $pRes->num_rows > 0) {
                            while ($p = $pRes->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $p['id']; ?></td>
                            <td><?php echo htmlspecialchars($p['patient_id'] . ' — ' . $p['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($p['doctor_name'] . ' / ' . $p['department']); ?></td>
                            <td><?php echo htmlspecialchars($p['service_name']); ?></td>
                            <td><?php echo number_format($p['cost'],2); ?></td>
                            <td><?php echo number_format($p['total_due'],2); ?></td>
                            <td><?php echo htmlspecialchars($p['payment_type']); ?></td>
                            <td><?php echo htmlspecialchars($p['created_at']); ?></td>
                            <td><button class="btn" onclick='openPaymentView(<?php echo json_encode($p); ?>)'>View</button></td>
                        </tr>
                        <?php
                            endwhile;
                        } else {
                            echo '<tr><td colspan="9" style="text-align:center;color:#777;padding:20px;">No payments yet.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
</div>

<style>
/* Layout and form */
.doctors-container { max-width: 1100px; margin: 0 auto; padding-bottom: 40px; }
.page-header { display:flex; align-items:center; justify-content:space-between; gap: 20px; margin-bottom: 14px; }
.page-header h2 { margin:0; color:#0d6efd; }
.header-cards { display:flex; gap:12px; }
.stat-card { background: white; padding:12px 18px; border-radius:10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); min-width:140px; }
.stat-label { color:#6c757d; font-size:0.9rem; }
.stat-value { font-size:1.25rem; font-weight:700; color:#111; }

.form-container { background: white; padding: 22px; border-radius:10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.form-group label{ display:block; margin-bottom:6px; font-weight:600; }
.form-group input, .form-group select { width:100%; padding:10px 12px; border:1px solid #e6e9ef; border-radius:8px; }
.btn-success{ background: linear-gradient(135deg,#11998e 0%,#38ef7d 100%); color:white; padding:10px 14px; border-radius:8px; border:none; }

/* payments table */
.table-container { background: white; padding: 18px; border-radius:10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
#paymentsTable { width:100%; border-collapse: collapse; }
#paymentsTable th, #paymentsTable td { padding:10px 12px; border-bottom:1px solid #eee; text-align:left; }
#paymentsTable thead { background:#f5f7fb; }
#paymentsTable tr:hover { background:#fbfbfd; }

/* modal (view) */
.modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); }
.modal.show { display:block; }
.modal-content { background: white; margin: 8% auto; padding: 0; border-radius:10px; max-width:560px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
.modal-header { padding:14px 18px; background: linear-gradient(90deg,#667eea,#764ba2); color:white; display:flex; justify-content:space-between; align-items:center; border-radius:10px 10px 0 0; }
.modal-body { padding:18px; color:#333; }
.modal-footer { padding:12px 18px; display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #eee; }
.modal-close { cursor:pointer; font-size:20px; }

/* toast */
.toast { background:#0d6efd; color:white; padding:10px 14px; border-radius:6px; margin-top:8px; box-shadow: 0 6px 20px rgba(13,110,253,0.15); transition:opacity .4s ease; }
.toast-error { background:#dc3545; }

/* receipt lines */
.receipt-lines { font-family: monospace; white-space: pre-line; }

@media (max-width: 768px) {
    .header-cards { flex-direction:column; }
    .page-header { flex-direction:column; align-items:flex-start; gap:8px; }
    .modal-content { margin: 20% auto; width: 90%; }
}
</style>

<script>
function recalc(){
    const cost = parseFloat(document.getElementById('cost').value) || 0;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const advance = parseFloat(document.getElementById('advance_paid').value) || 0;
    let total = cost - discount - advance;
    if (total < 0) total = 0;
    document.getElementById('total_due_display').value = total.toFixed(2);
}
function toggleCardField(){
    const type = document.getElementById('payment_type').value;
    document.getElementById('card_check_group').style.display = (type === 'Card' || type === 'Check') ? 'block' : 'none';
}
// init
recalc();

// validate form before submit
function validatePaymentForm(){
    const cost = parseFloat(document.getElementById('cost').value) || 0;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const advance = parseFloat(document.getElementById('advance_paid').value) || 0;
    const type = document.getElementById('payment_type').value;
    const cardDetails = document.getElementById('card_check_details').value;

    if (cost <= 0) { alert('Please enter a valid cost greater than 0'); return false; }
    if (discount < 0 || advance < 0) { alert('Discount and advance must be 0 or positive'); return false; }
    if ((discount + advance) > cost) { if (!confirm('Discount + advance exceed cost. Continue?')) return false; }
    if ((type === 'Card' || type === 'Check') && cardDetails.trim() === '') { alert('Please provide card/check details'); return false; }
    return true;
}

// Payment view modal (shows details and allows printing)
function openPaymentView(payment){
    // ensure object
    if (typeof payment === 'string') payment = JSON.parse(payment);
    let modal = document.getElementById('paymentViewModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'paymentViewModal';
        modal.className = 'modal show';
        modal.innerHTML = '\n            <div class="modal-content">\n                <div class="modal-header"><h3>Payment Receipt</h3><span class="modal-close" onclick="closePaymentView()">&times;</span></div>\n                <div class="modal-body view-modal-body"> </div>\n                <div class="modal-footer"><button class="btn" onclick="closePaymentView()">Close</button><button class="btn btn-success" onclick="printPaymentView()">Print</button></div>\n            </div>';
        document.body.appendChild(modal);
    } else {
        modal.classList.add('show');
    }
    const body = modal.querySelector('.modal-body');
    const lines = [];
    lines.push('Transaction ID: #' + payment.id);
    lines.push('Patient: ' + (payment.patient_id || '') + ' - ' + (payment.patient_name || ''));
    lines.push('Department: ' + (payment.department || ''));
    lines.push('Doctor: ' + (payment.doctor_name || ''));
    lines.push('Admission: ' + (payment.admission_date || '') + '  Discharge: ' + (payment.discharge_date || ''));
    lines.push('Service: ' + (payment.service_name || ''));
    lines.push('Cost: ' + (payment.cost || '0.00'));
    lines.push('Discount: ' + (payment.discount || '0.00'));
    lines.push('Advance Paid: ' + (payment.advance_paid || '0.00'));
    lines.push('Total Due: ' + (payment.total_due || '0.00'));
    lines.push('Payment Type: ' + (payment.payment_type || ''));
    lines.push('Card/Check: ' + (payment.card_check_details || ''));
    lines.push('Recorded: ' + (payment.created_at || ''));
    body.innerHTML = '<div class="receipt-lines">' + lines.join('\n') + '</div>';
}

// Toast helper
function showToast(message, type = 'success'){
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = 2000;
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = 'toast ' + (type === 'error' ? 'toast-error' : 'toast-success');
    toast.innerText = message;
    container.appendChild(toast);
    setTimeout(()=>{ toast.style.opacity = '0'; setTimeout(()=>toast.remove(),400); }, 3500);
}

// handle AJAX submit
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('paymentForm');
    if (form) {
        form.addEventListener('submit', function(e){
            e.preventDefault();
            if (!validatePaymentForm()) return;
            const fd = new FormData(form);
            fd.set('ajax','1');
            // send
            fetch('payments.php', {
                method: 'POST',
                body: fd,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(r => r.json()).then(data => {
                if (data && data.success) {
                    showToast('Payment recorded — Transaction #' + data.payment.id);
                    prependPaymentRow(data.payment);
                    // update stats
                    const tp = document.getElementById('stat_total_payments');
                    const tr = document.getElementById('stat_total_revenue');
                    const to = document.getElementById('stat_outstanding');
                    if (tp) tp.innerText = Number(tp.innerText.replace(/,/g,'')) + 1;
                    if (tr) tr.innerText = (Number(tr.innerText.replace(/,/g,'')) + Number(data.payment.cost)).toFixed(2);
                    if (to) to.innerText = (Number(to.innerText.replace(/,/g,'')) + Number(data.payment.total_due)).toFixed(2);
                    form.reset(); recalc(); toggleCardField();
                } else {
                    showToast(data && data.error ? data.error : 'Failed to save payment', 'error');
                }
            }).catch(err => { console.error(err); showToast('Error submitting payment', 'error'); });
        });
    }
});

function prependPaymentRow(p){
    try {
        const tbody = document.querySelector('#paymentsTable tbody');
        if (!tbody) return;
        const tr = document.createElement('tr');
        const paymentObj = (typeof p === 'string') ? JSON.parse(p) : p;

        const makeCell = (val) => { const td=document.createElement('td'); td.innerText = (val===null||val===undefined)?'':val; return td; };
        tr.appendChild(makeCell(paymentObj.id));
        tr.appendChild(makeCell((paymentObj.patient_id||'') + ' — ' + (paymentObj.patient_name||'')));
        tr.appendChild(makeCell((paymentObj.doctor_name||'') + ' / ' + (paymentObj.department||'')));
        tr.appendChild(makeCell(paymentObj.service_name));
        tr.appendChild(makeCell(Number(paymentObj.cost).toFixed(2)));
        tr.appendChild(makeCell(Number(paymentObj.total_due).toFixed(2)));
        tr.appendChild(makeCell(paymentObj.payment_type));
        tr.appendChild(makeCell(paymentObj.created_at));
        const actionTd = document.createElement('td');
        const btn = document.createElement('button'); btn.className='btn'; btn.type='button'; btn.innerText='View';
        btn.addEventListener('click', function(){ openPaymentView(paymentObj); });
        actionTd.appendChild(btn);
        tr.appendChild(actionTd);
        if (tbody.firstChild) tbody.insertBefore(tr, tbody.firstChild);
    } catch(e){ console.error(e); }
}

function closePaymentView(){
    const modal = document.getElementById('paymentViewModal');
    if (modal) modal.classList.remove('show');
}

function printPaymentView(){
    const modal = document.getElementById('paymentViewModal');
    if (!modal) return;
    const content = modal.querySelector('.modal-body').innerHTML;
    const w = window.open('', '_blank', 'width=700,height=800');
    w.document.write('<html><head><title>Receipt</title>');
    w.document.write('<style>body{font-family: Arial, sans-serif; padding:20px;} .receipt-lines{white-space:pre-line; font-family: monospace;}</style>');
    w.document.write('</head><body>');
    w.document.write(content);
    w.document.write('</body></html>');
    w.document.close();
    w.print();
}
</script>
