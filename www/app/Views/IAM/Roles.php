<?php ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
    <div class="role-container">
        <h1 style="color: #11306aff">Roles</h1>
        <button type="button" class="btn-create-role" onclick="window.location.href='/IAM/Roles/createRole'">
            Create Role
        </button>
        <table id="rolesTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Skills</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($roles)): ?>
                <tr>
                    <td colspan="3">No roles defined.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($roles as $role): ?>
                <tr class="<?= $role['simulated'] ? 'is-simulated' : '' ?>">
                    <td><?= esc ($role['name']) ?></td>
                    <td><?= esc ($role['description']) ?></td>
                    <td><?= esc ($role['skills']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <style>
        .btn-create-role {
            display: inline-block;
            margin-bottom: 18px;
            padding: 10px 18px;
            background: #11306aff;
            color: #fff;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-create-role:hover {
            background: #1a418c;
        }
        .role-container { 
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