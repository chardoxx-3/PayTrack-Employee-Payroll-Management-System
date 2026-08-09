<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold">Update Employee Record</h5>
                    <span class="badge bg-soft-danger text-danger">ID: <?= $employee['employee_id'] ?></span>
                </div>
                <form action="/employee/update/<?= $employee['id'] ?>" method="post">
                    <div class="row g-3">
                        <!-- Prefilled data -->
                        <div class="col-md-12">
                            <label class="form-label text-muted small fw-bold">FULL NAME</label>
                            <input type="text" name="full_name" class="form-control" value="<?= $employee['full_name'] ?>" required>
                        </div>
<div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">DESIGNATION</label>
                            <input type="text" name="position" class="form-control" value="<?= $employee['position'] ?? '' ?>" required>
                        </div>
<?php
    $currentOfficeName = '';
    foreach ($offices as $o) {
        if ($o['id'] == $employee['office_id']) { $currentOfficeName = $o['office_name']; break; }
    }
?>
<div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">OFFICE ASSIGNMENT</label>
                            <input type="text" id="office_display" class="form-control" value="<?= esc($currentOfficeName) ?>" placeholder="Click to select office" readonly required style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#officeModal">
                            <input type="hidden" name="office_id" id="office_id" value="<?= $employee['office_id'] ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">CONTACT NUMBER</label>
                            <input type="text" name="contact_number" class="form-control" value="<?= $employee['contact_number'] ?? '' ?>">
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="button" class="btn btn-outline-danger btn-sm float-start px-3" onclick="confirmDelete()">Deactivate Employee</button>
                            <a href="/employee" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete() {
        if(confirm("Are you sure you want to remove this employee record?")) {
            window.location.href = "/employee/delete/<?= $employee['id'] ?>";
        }
    }
</script>

<!-- Office Selector Modal -->
<div class="modal fade" id="officeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Select Office</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
<div class="list-group mb-3" id="officeList">
                    <?php foreach($offices as $office): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center" id="office-row-<?= $office['id'] ?>">
                            <span style="cursor:pointer; flex:1;" onclick="selectOffice('<?= $office['id'] ?>', '<?= esc($office['office_name']) ?>')">
                                <?= esc($office['office_name']) ?>
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="deleteOffice('<?= $office['id'] ?>')" title="Delete office">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <hr>
                <label class="form-label text-muted small fw-bold">ADD NEW OFFICE</label>
                <div class="input-group">
                    <input type="text" id="new_office_name" class="form-control" placeholder="e.g. Legal Office">
                    <button type="button" class="btn btn-primary" onclick="addOffice()">Add</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function selectOffice(id, name) {
        document.getElementById('office_id').value = id;
        document.getElementById('office_display').value = name;
        bootstrap.Modal.getInstance(document.getElementById('officeModal')).hide();
    }

function addOffice() {
        const name = document.getElementById('new_office_name').value.trim();
        if (!name) return;

        fetch('/office/store', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'office_name=' + encodeURIComponent(name)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const list = document.getElementById('officeList');
                const row = document.createElement('div');
                row.className = 'list-group-item d-flex justify-content-between align-items-center';
                row.id = 'office-row-' + data.office.id;
                row.innerHTML = `
                    <span style="cursor:pointer; flex:1;" onclick="selectOffice('${data.office.id}', '${data.office.office_name}')">${data.office.office_name}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="deleteOffice('${data.office.id}')" title="Delete office">
                        <i class="fas fa-trash"></i>
                    </button>`;
                list.appendChild(row);

                document.getElementById('new_office_name').value = '';
                selectOffice(data.office.id, data.office.office_name);
            } else {
                alert(data.message || 'Failed to add office.');
            }
        });
    }

function deleteOffice(id) {
        fetch('/office/delete/' + id, { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('office-row-' + id).remove();
                // If the deleted office was the currently selected one, clear the field
                if (document.getElementById('office_id').value == id) {
                    document.getElementById('office_id').value = '';
                    document.getElementById('office_display').value = '';
                }
            } else {
                alert(data.message || 'Failed to delete office.');
            }
        });
    }
</script>

<?= $this->endSection() ?>