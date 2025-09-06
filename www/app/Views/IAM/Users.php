<?php ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="user-container">
    <h1 style="color: #11306aff">Users</h1>
    <?php if ($canCreateUsers): ?>
        <button type="button" class="btn-create-user" onclick="window.location.href='/IAM/Users/createUser'">
            Create User
        </button>
    <?php endif; ?>
    <table id="usersTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Surnames</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr>
                <td colspan="4">No users defined.</td>
            </tr>
        <?php endif; ?>
        <?php foreach ($users as $user): ?>
            <?= view('partials/user_row', [
                'user' => $user,
                'roleLabels' => $roleLabels ?? [],
                'canDeleteUsers' => $canDeleteUsers ?? false,
            ]) ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="userModal" class="modal" style="display:none;">
    <div class="modal-content modal--scroll">
        <button id="closeModalBtn" class="modal-close" aria-label="Close">&times;</button>
        <div id="userModalBody" class="modal-body"></div>
    </div>
</div>

<script src="<?= base_url('assets/js/users.js') ?>" defer></script>
<script src="<?= base_url('assets/js/user-modal.js') ?>" defer></script>

<?= $this->endSection() ?>