<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Account Settings</h5>
                    
                    <form action="/user/update_password" method="post">
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">CURRENT PASSWORD</label>
                            <input type="password" name="old_password" class="form-control border-light shadow-sm">
                        </div>
                        <hr class="text-muted">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">NEW PASSWORD</label>
                            <input type="password" name="new_password" class="form-control border-light shadow-sm">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">CONFIRM NEW PASSWORD</label>
                            <input type="password" name="confirm_password" class="form-control border-light shadow-sm">
                        </div>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Update Account Security</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>