<?php ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="profile-container" style="max-width: 600px;">
    <h1 style="color: #11306aff">My Profile</h1>
    <table style="width:100%; border-collapse:collapse;">
        <tbody>
            <tr>
                <th style="text-align:left; width:180px;">Name</th>
                <td><?= esc($user['name'] ?? '') ?></td>
            </tr>
            <tr>
                <th style="text-align:left;">Surnames</th>
                <td><?= esc($user['surnames'] ?? '') ?></td>
            </tr>
            <tr>
                <th style="text-align:left;">Email</th>
                <td><?= esc($user['email'] ?? '') ?></td>
            </tr>
            <tr>
                <th style="text-align:left;">Role</th>
                <td><?= esc($user['role_name'] ?? '') ?></td>
            </tr>
            <tr>
                <th style="text-align:left;">Birthdate</th>
                <td><?= esc($user['birthdate'] ?? '') ?></td>
            </tr>
            <tr>
                <th style="text-align:left;">Telephone</th>
                <td><?= esc($user['telephone'] ?? '') ?></td>
            </tr>
            <tr>
                <th style="text-align:left;">Address</th>
                <td><?= esc($user['address'] ?? '') ?></td>
            </tr>
            <tr>
                <th style="text-align:left;">National ID (DNI/NIE)</th>
                <td><?= esc($user['dni_nie'] ?? '') ?></td>
            </tr>
            <tr>
                <th style="text-align:left;">Soft Skills</th>
                <td><?= esc($user['soft_skills'] ?? '') ?></td>
            </tr>
            <tr>
                <th style="text-align:left;">Technical Skills</th>
                <td><?= esc($user['technical_skills'] ?? '') ?></td>
            </tr>
        </tbody>
    </table>
    <div style="margin-top: 2rem;">
        <a href="/logout" class="btn-logout" style="text-decoration:none;">Logout</a>
    </div>
</div>

<style>
.profile-container {
    max-width: 600px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px #ccc;
}
th, td {
    padding: 12px 10px;
    border-bottom: 1px solid #eee;
    color: #11306aff;
}
th {
    background: #f0f0f0;
    font-weight: bold;
}
tr:hover {
    background: #f9f9f9;
}
.btn-logout {
    display: inline-block;
    margin-top: 18px;
    padding: 10px 18px;
    background: #11306aff;
    color: #fff;
    border-radius: 6px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.btn-logout:hover {
    background: #1a418c;
}
</style>

<?= $this->endSection() ?>