(function () {
  // Uses globally-available csrf() and updateCsrfFromResponse(json)
  // provided by your base layout via <meta csrf-token-name> + <meta csrf-token>.

    function csrf() {
        const name = document.querySelector('meta[name="csrf-token-name"]')?.content;
        const hash = document.querySelector('meta[name="csrf-token"]')?.content;
        return name && hash ? { [name]: hash } : {};
    }
    
    function updateCsrfFromResponse(json) {
        if (json && json.csrf && json.csrf.name && json.csrf.hash) {
            const n = document.querySelector('meta[name="csrf-token-name"]');
            const h = document.querySelector('meta[name="csrf-token"]');
            if (n) n.content = json.csrf.name;
            if (h) h.content = json.csrf.hash;
        }
    }

    document.addEventListener('click', function (e) {
        const deleteButton = e.target.closest('.icon-btn--trash');
        if (!deleteButton) return;

        const userId = deleteButton.getAttribute('data-id');
        if (!userId) return;

        const row = deleteButton.closest('tr');
        const nameCell = row ? row.querySelector('td') : null;
        const label = (nameCell && nameCell.textContent.trim()) || 'this user';

        if (!confirm(`Delete ${label}?`)) return;

        const previousDisabled = deleteButton.disabled;
        deleteButton.disabled = true;

        fetch(`/IAM/Users/delete/${encodeURIComponent(userId)}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(csrf())   // <- EXACTLY like your task delete/state update
        })
        .then(r => r.json().catch(() => ({})))
        .then(json => {
        updateCsrfFromResponse(json);  // <- keep tokens fresh for next actions

        if (json?.ok) {
            //If success: remove row
            if (row && row.parentNode) row.parentNode.removeChild(row);
        } else {
            // show server error (mirrors your task flow)
            alert(json?.error || 'Delete failed');
            deleteButton.disabled = previousDisabled;
        }
        })
        .catch(() => {
        alert('Delete failed catch');
        deleteButton.disabled = previousDisabled;
        });
    });
})();
