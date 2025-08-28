<?php ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
    <div class="user-container">
        <h1 style="color: #11306aff">Users</h1>
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
                    <td><?= esc ($user['name']) ?></td>
                    <td><?= esc ($user['surnames']) ?></td>
                    <td><?= esc ($user['email']) ?></td>
                    <td class="role-label">
                        <?php
                            $role = $user['role'] ?? null;
                            echo $roleLabels[$role] ?? esc ($role ?? '—');
                        ?>
                    </td>
                    <?php if ($canDeleteUsers): ?>
                        <td>
                            <button
                                type="button"
                                id="deleteTaskBtn"
                                class="icon-btn icon-btn--trash js-delete-user"
                                title="Delete User"
                                aria-label="Delete User"
                                data-id="<?= esc($user['id_user']) ?>"
                                style="flex:0 0 auto"
                                >
                                <img src="/assets/media/icons/trash_bin.svg" alt="Delete" width="22" height="22" loading="eager" decoding="async">
                            </button>
                        </td>
                    <?php endif; ?>
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

        // Delete user functionality
        /*document.addEventListener('click', function(e) {    
            if (e.target.closest('.icon-btn--trash')) {
                const deleteButton = e.target.closest('.icon-btn--trash');
                const userId = deleteButton.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this user?')) {
                    fetch(`/IAM/Users/delete/${userId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.ok) {
                            // Remove the user row from the table
                            button.closest('tr').remove();
                        } else {
                            alert('Error deleting user: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        alert('Error deleting user: ' + error.message);
                    });
                }
            }
        });*/
    </script>

<script src="/assets/js/users.js"></script>

<?= $this->endSection() ?>