<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">System Users</h4>
        <button class="btn btn-primary btn-sm px-4" data-bs-toggle="modal" data-bs-target="#addUserModal">
            Register New User
        </button>
    </div>

    <div class="row">
        <?php foreach($users as $user): ?>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100 p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-soft-primary text-primary rounded p-3 me-3">
                        <i class="fas fa-user-shield fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0"><?= $user['name'] ?></h6>
                        <small class="text-muted">@<?= $user['username'] ?></small>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge <?= $user['role'] == 'admin' ? 'bg-danger' : 'bg-primary' ?> rounded-pill px-3">
                        <?= strtoupper($user['role']) ?>
                    </span>
                    <a href="#" class="text-muted small text-decoration-none">Edit Permissions</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Structure for Adding Users -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content border-0 shadow" action="/user/register" method="post">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">New User Registration</h5>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">FULL NAME</label>
                    <input type="text" name="name" class="form-control bg-light border-0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">USERNAME</label>
                    <input type="text" name="username" class="form-control bg-light border-0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">ROLE</label>
                    <select name="role" class="form-select bg-light border-0">
                        <option value="staff">Payroll Staff</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">PASSWORD</label>
                    <input type="password" name="password" class="form-control bg-light border-0" required>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save User</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>