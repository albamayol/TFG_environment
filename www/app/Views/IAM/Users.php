<?php ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
    <h1>Users in App</h1>
        <div class="user-container">
        <h1 style="color: #11306aff">Users in App</h1>
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
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['surnames']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td class="role-label">
                        <?php
                            $role = $user['role'] ?? null;
                            echo $roleLabels[$role] ?? htmlspecialchars($role ?? '—');
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <style>
        .user-container { 
            max-width: 900px; 
            margin: 40px auto; 
            background: #fff; 
            padding: 30px; 
            border-radius: 8px;
            box-shadow: 0 2px 8px #ccc; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }

        th, td { 
            padding: 12px 10px; 
            border-bottom: 1px solid #eee; 
            text-align: left; 
            color: #11306aff;
        }
        th { 
            background: #f0f0f0; 
        }
        tr:hover { 
            background: #f9f9f9; 
        }
        .role-label { 
            font-weight: bold; 
        }
    </style>

    <script>
        // Example: simple search/filter functionality
        // Add an input box above the table for searching by name/email
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.container');
            const input = document.createElement('input');
            input.type = 'text';
            input.placeholder = 'Search by name or email...';
            input.style.marginBottom = '15px';
            input.style.width = '100%';
            input.style.padding = '8px';
            container.insertBefore(input, container.querySelector('table'));

            input.addEventListener('input', function() {
                const filter = input.value.toLowerCase();
                const rows = document.querySelectorAll('#usersTable tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            });
        });
    </script>

<?= $this->endSection() ?>