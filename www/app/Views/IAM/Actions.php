<?php ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
    <div class="action-container">
        <h1 style="color: #11306aff">Actions</h1>
        <button type="button" class="btn-create-action" onclick="window.location.href='/IAM/Actions/createAction'">
            Create Action
        </button>
        <table id="actionsTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($actions)): ?>
                <tr>
                    <td colspan="2">No actions defined.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($actions as $action): ?>
                <tr class="<?= $action['simulated'] ? 'is-simulated' : '' ?>">
                    <td><?= esc ($action['name']) ?></td>
                    <td><?= esc ($action['description']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <style>
        .btn-create-action {
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
        .btn-create-action:hover {
            background: #1a418c;
        }
        .action-container { 
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