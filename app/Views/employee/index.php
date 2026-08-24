<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm">
<div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
    <h5 class="fw-bold mb-0">Employee Records</h5>
    <div>
        <button type="button" class="btn btn-outline-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-excel"></i> Import from Excel
        </button>
        <button type="button" class="btn btn-outline-danger btn-sm px-3" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
            <i class="fas fa-trash-alt"></i> Delete All
        </button>
        <a href="/employee/create" class="btn btn-primary btn-sm px-3">+ Add New Employee</a>
    </div>
</div>
        <div class="card-body">
            <style>
            .employee-table thead th {
                background-color: #0d2d27 !important;
                color: #e6f4f1 !important;
            }

            .employee-table th,
            .employee-table td {
                border: 1px solid #dee2e6 !important;
            }

            .employee-table tbody tr:hover {
                background-color: #f5faf6 !important;
            }

            .employee-table .row-selected {
                background-color: #d4edda !important;
                border-left: 3px solid #0d5c4e !important;
            }

            .employee-table .row-selected td {
                background-color: #d4edda !important;
                color: #1e293b !important;
            }

            .employee-table .row-selected td small,
            .employee-table .row-selected td .text-muted {
                color: #4a635f !important;
            }

            .employee-table .row-selected td .badge {
                color: #ffffff !important;
                background-color: rgba(13, 92, 78, 0.85) !important;
            }

            .employee-table .row-selected td .btn {
                color: #ffffff !important;
                background-color: #0d5c4e !important;
                border-color: #0d5c4e !important;
            }
            </style>
            <!-- Filter Section -->
            <form action="/employee" method="get" class="row g-2 mb-4">
                <div class="col-md-4">
                    <select name="office_id" class="form-select border-light bg-light" onchange="this.form.submit()">
                        <option value="">All Offices (Filter by Unit)</option>
                        <?php foreach($offices as $office): ?>
                            <option value="<?= $office['id'] ?>" <?= (isset($office_id) && $office_id == $office['id']) ? 'selected' : '' ?>><?= $office['office_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control border-light bg-light" placeholder="Search ID or Name..." value="<?= esc($search ?? '') ?>">
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover bg-white rounded shadow-sm align-middle employee-table">
                <thead class="text-muted small fw-bold">
    <tr>
        <th class="text-center" style="vertical-align: middle; width: 40px;">NO.</th>
        <th style="vertical-align: middle;">FULL NAME</th>
        <th style="vertical-align: middle;">OFFICE</th>
        <th style="vertical-align: middle;">DESIGNATION</th>
        <th>CONTACT NUMBER</th>
        <th>STATUS</th>
        <th class="text-end" style="vertical-align: middle;">ACTIONS</th>
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
    <td class="align-middle"><span class="badge rounded-pill bg-success">Active</span></td>
    <td class="text-end">
        <a href="/employee/edit/<?= $emp['id'] ?>" class="btn btn-sm btn-outline-secondary border-0"><i class="fas fa-edit"></i></a>
        <a href="/deduction/manage/<?= $emp['id'] ?>" class="btn btn-sm btn-outline-info border-0"><i class="fas fa-file-invoice"></i></a>
    </td>
</tr>
<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
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
        <p class="text-muted small">Upload the payroll Excel file. Each sheet is treated as an office; sheets with "rata" in the name are skipped.</p>
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