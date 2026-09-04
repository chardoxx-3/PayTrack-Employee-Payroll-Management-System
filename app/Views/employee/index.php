<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
.employee-table thead th {
    background-color: var(--navy-800) !important;
    color: #fff !important;
}

.employee-table th,
.employee-table td {
    border: 1px solid #dee2e6 !important;
}

.employee-table tbody tr:hover {
    background-color: var(--navy-subtle) !important;
}

.employee-table .row-selected {
    background-color: var(--navy-subtle) !important;
    border-left: 3px solid var(--navy-600) !important;
}

.employee-table .row-selected td {
    background-color: var(--navy-subtle) !important;
    color: #1e293b !important;
}

.employee-table .row-selected td small,
.employee-table .row-selected td .text-muted {
    color: #4a5f7a !important;
}

.employee-table .row-selected td .badge {
    color: #ffffff !important;
    background-color: var(--navy-600) !important;
}

.employee-table .row-selected td .btn {
    color: #ffffff !important;
    background-color: var(--navy-600) !important;
    border-color: var(--navy-600) !important;
}
</style>
<?php
function peso($value) {
    return $value > 0 ? '₱' . number_format($value, 2) : '';
}
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Employee Records</h4>
            <p class="text-muted small mb-0"><?= ($office_id ? ($offices[0]['office_name'] ?? '') : 'All Offices') ?> — <?= count($employees) ?> employee(s)</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-excel"></i> Import from Excel
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
                <i class="fas fa-trash-alt"></i> Delete All
            </button>
            <a href="/employee/create" class="btn btn-primary btn-sm px-3">+ Add New Employee</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-3 mb-4 bg-light">
        <form action="/employee" method="get" class="row g-2 align-items-center">
            <div class="col-md-4">
                <select name="office_id" class="form-select border-0 shadow-sm" onchange="this.form.submit()">
                    <option value="">All Offices (Filter by Unit)</option>
                    <?php foreach($offices as $office): ?>
                        <option value="<?= $office['id'] ?>" <?= (isset($office_id) && $office_id == $office['id']) ? 'selected' : '' ?>><?= $office['office_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0" placeholder="Search ID or Name..." value="<?= esc($search ?? '') ?>">
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover bg-white rounded shadow-sm align-middle employee-table">
            <thead class="text-muted small fw-bold">
                <tr>
                    <th class="text-center" style="vertical-align: middle; width: 40px;">NO.</th>
                    <th style="vertical-align: middle;">FULL NAME</th>
                    <th style="vertical-align: middle;">OFFICE</th>
                    <th style="vertical-align: middle;">DESIGNATION</th>
                    <th style="vertical-align: middle;">CONTACT NUMBER</th>
                    <th class="text-center" style="vertical-align: middle;">STATUS</th>
                    <th class="text-center" style="vertical-align: middle;">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($employees as $emp): ?>
                <tr>
                    <td class="align-middle text-center" style="width: 40px;"><?= $no++ ?></td>
                    <td class="align-middle fw-bold text-primary"><?= esc($emp['full_name']) ?></td>
                    <td class="align-middle"><span class="text-muted small"><?= esc($emp['office_name'] ?? '—') ?></span></td>
                    <td class="align-middle"><span class="text-muted small"><?= esc($emp['position'] ?? '—') ?></span></td>
                    <td class="align-middle"><span class="text-muted small"><?= esc($emp['contact_number'] ?? '—') ?></span></td>
                    <td class="align-middle text-center"><span class="badge rounded-pill bg-success">Active</span></td>
                    <td class="text-center">
                        <a href="/employee/edit/<?= $emp['id'] ?>" class="btn btn-sm btn-outline-secondary border-0"><i class="fas fa-edit"></i></a>
                        <a href="/deduction/manage/<?= $emp['id'] ?>" class="btn btn-sm btn-outline-info border-0"><i class="fas fa-file-invoice"></i></a>
                        <a href="/employee/delete/<?= $emp['id'] ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Delete this employee? This cannot be undone.')"><i class="fas fa-trash-alt"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($employees)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No employees found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="/employee/import" method="post" enctype="multipart/form-data" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title">Import Employees from Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Upload the payroll Excel file (.xls or .xlsx). Each sheet is automatically imported as its assigned office unit (including RATA vouchers).</p>
                <input type="file" name="payroll_file" accept=".xls,.xlsx" class="form-control" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Import</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="deleteAllModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="/employee/deleteAll" method="post" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">Delete All Employees</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">This will permanently remove <strong>all employees</strong>, their deductions, and payroll records. This action cannot be undone.</p>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Type <span class="text-danger">DELETE</span> to confirm:</label>
                    <input type="text" class="form-control" id="deleteConfirmInput" name="confirm" autocomplete="off" required>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger" id="deleteAllBtn" disabled>Delete All</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('deleteConfirmInput').addEventListener('input', function() {
    document.getElementById('deleteAllBtn').disabled = (this.value !== 'DELETE');
});

let selectedRow = null;

function selectRow(row) {
    if (selectedRow && selectedRow !== row) {
        selectedRow.classList.remove('row-selected');
        selectedRow.setAttribute('aria-selected', 'false');
    }
    row.classList.add('row-selected');
    row.setAttribute('aria-selected', 'true');
    selectedRow = row;
}

function deselectRow(row) {
    row.classList.remove('row-selected');
    row.setAttribute('aria-selected', 'false');
    if (selectedRow === row) selectedRow = null;
}

document.querySelectorAll('.employee-table tbody tr').forEach(function(row, index) {
    const firstCell = row.querySelector('td');
    if (!firstCell) return;
    firstCell.style.cursor = 'pointer';
    firstCell.setAttribute('tabindex', '0');
    firstCell.setAttribute('role', 'button');
    firstCell.setAttribute('aria-label', 'Select row ' + (index + 1));

    firstCell.addEventListener('click', function(e) {
        const currentRow = this.closest('tr');
        if (currentRow.classList.contains('row-selected')) {
            deselectRow(currentRow);
        } else {
            selectRow(currentRow);
        }
    });

    firstCell.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            const currentRow = this.closest('tr');
            if (currentRow.classList.contains('row-selected')) {
                deselectRow(currentRow);
            } else {
                selectRow(currentRow);
            }
        }
    });
});

document.querySelectorAll('.employee-table tbody tr').forEach(function(row) {
    row.addEventListener('dblclick', function(e) {
        if (this.classList.contains('row-selected')) {
            deselectRow(this);
        } else {
            selectRow(this);
        }
    });
});
</script>

<?= $this->endSection() ?>
