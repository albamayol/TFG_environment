(function initTaskModal() {

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

  const modal = document.getElementById('taskModal');
  if (!modal) return;

  if (modal.parentNode !== document.body) document.body.appendChild(modal);

  const closeBtn   = document.getElementById('closeModalBtn');
  const bodyHost   = document.getElementById('taskModalBody');
  const closeModal = () => { modal.style.display = 'none'; document.body.style.overflow = ''; };

  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal.style.display !== 'none') closeModal(); });

  // 1) Prevent opening the modal when using the <select>
  document.addEventListener('mousedown', (e) => { if (e.target.closest('.state-select')) e.stopPropagation(); }, { capture:true });
  document.addEventListener('click',     (e) => { if (e.target.closest('.state-select')) e.stopPropagation(); }, { capture:true });

  // 2) Open modal from any .task-card (delegated)
  document.addEventListener('click', (e) => {
    const card = e.target.closest('.task-card');
    if (!card || e.target.closest('.state-select')) return;

    // Build modal HTML from the card’s dataset
    const canDelete = card.dataset.canDelete === '1';
    const html = `
      <h2 style="margin-top:0">${escapeHtml(card.dataset.name || '')}</h2>
      ${canDelete ? `
        <button
          type="button"
          id="deleteTaskBtn"
          class="icon-btn"
          title="Delete task"
          aria-label="Delete task"
          data-id="${escapeHtml(card.dataset.taskId)}"
          style="flex:0 0 auto"
        >
          <!-- trash icon SVG -->
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M3 6h18M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2m1 0v14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V6h12zM10 10v8m4-8v8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      ` : ''}
      ${escapeHtml(card.dataset.description || '') ? `<p>${escapeHtml(card.dataset.description)}</p>` : ''}
      <p><strong>Duration:</strong> ${escapeHtml(card.dataset.duration || '-')}</p>
      <p><strong>Priority:</strong> ${escapeHtml(card.dataset.priority || '-')}</p>
      <p><strong>Limit date:</strong> ${escapeHtml(card.dataset.limitDate || '-')}</p>
      <p><strong>State:</strong> ${escapeHtml(card.dataset.state || '-')}</p>
      <p><strong>Person of interest:</strong> ${escapeHtml(card.dataset.person || '-')}</p>
      <p><strong>Origin:</strong> ${escapeHtml(card.dataset.origin || '-')}</p>
      
    `;
    bodyHost.innerHTML = html;

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  });

  //Change state (delegated, works for cards rendered anywhere)
  document.addEventListener('change', (e) => {
    const select = e.target.closest('.state-select');
    if (!select) return;

    const id = select.getAttribute('data-id');
    const newState = select.value;
    const prev = select.dataset.state || select.value;

    fetch(`/Tasks/updateState/${id}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin', 
      body: JSON.stringify({ state: newState, ...csrf() })
    })
    .then(res => {
      if (!res.ok) throw new Error('network');
      return res.json();
    })
    .then(json => {
      updateCsrfFromResponse(json);           // <—— CRITICAL LINE

      // keep your existing badge update, but DON'T overwrite className:
      const card = select.closest('.task-card');
      if (card) {
        const badge = card.querySelector('.state-badge') || card.querySelector('.task-header .badge');
        if (badge) {
          const newClass = 'badge-' + newState.toLowerCase().replace(/\s+/g, '-');
          ['badge-to-do','badge-in-progress','badge-done'].forEach(c => badge.classList.remove(c));
          badge.classList.add('badge', 'state-badge', newClass);
          badge.textContent = newState;
        }
        card.dataset.state = newState;
      }
      select.dataset.state = newState;
      recalcCompletionPct();
    })
    .catch(() => {
      // revert on error, and (optional) toast/log to see the 403 on second attempt
      select.value = prev;
    });
  });

  // delegated click handler for delete
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('button#deleteTaskBtn');
    if (!btn) return;

    const card = document.querySelector(`.task-card[data-task-id="${btn.dataset.id}"]`);
    const url  = card?.dataset.deleteUrl;
    if (!url) return;
    if (!confirm('Delete this task?')) return;

    fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(csrf())     // reuse your csrf() helper
    })
    .then(r => r.json().catch(() => ({})))
    .then(json => {
      updateCsrfFromResponse(json);    // keep CSRF fresh for next calls
      if (json?.ok) {
        card?.remove();
        modal.style.display = 'none';
        document.body.style.overflow = '';
        if (typeof recalcCompletionPct === 'function') recalcCompletionPct();
      } else {
        alert(json?.error || 'Delete failed');
      }
    })
    .catch(() => alert('Delete failed'));
  });


  // tiny util to avoid XSS when injecting dataset values
  function escapeHtml(str) {
    return String(str)
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'",'&#39;');
  }

  function recalcCompletionPct() {
    const pctNode = document.getElementById('completionPct');
    if (!pctNode) return;                      // Not on My Day

    // Count cards in today's list (My Day). If your container differs, adjust selector.
    const cards = document.querySelectorAll('.tasks-container .task-card');
    const total = cards.length;
    if (!total) { pctNode.textContent = '0'; return; }

    let done = 0;
    cards.forEach(card => {
      const state =
        (card.dataset.state || '').toLowerCase() ||
        (card.querySelector('.state-select')?.dataset.state || '').toLowerCase();
      if (state === 'done') done++;
    });

    pctNode.textContent = Math.round((done / total) * 100);
  }

})();
