<?php $isSim = (int)($user['simulated'] ?? 0) === 1; 
$role  = $user['role'] ?? null;
?>

<tr class="user-row <?= $isSim ? 'is-simulated' : '' ?>"
    data-user-id="<?= esc($user['id_user']) ?>"
    data-name="<?= esc($user['name']) ?>"
    data-surnames="<?= esc($user['surnames']) ?>"
    data-email="<?= esc($user['email'] ?? ($user['email'] ?? '-')) ?>"
    data-tasks="<?= esc(json_encode($user['tasks'] ?? [], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP)) ?>"
    data-simulated="<?= $isSim ? '1' : '0' ?>"
    >
    <td><?= esc ($user['name']) ?></td>
    <td><?= esc ($user['surnames']) ?></td>
    <td><?= esc ($user['email']) ?></td>
    <td class="role-label">
        <?php
            $role = $user['role'] ?? null;
            echo $roleLabels[$role] ?? esc ($role ?? '—');
        ?>
    </td> 
    <?php if (!empty($canDeleteUsers)): ?>
        <td>
            <button
                type="button"
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


<style>
.btn-create-user {
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
.btn-create-user:hover {
background: #1a418c;
}
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
