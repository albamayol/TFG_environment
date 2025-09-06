(function initProjectModal() {

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

  //const ICON_TRASH = document.querySelector('meta[name="assets-url"]')?.content + '/media/icons/trash_bin.svg';

  const modal = document.getElementById('projectModal');
  if (!modal) return;

  if (modal.parentNode !== document.body) document.body.appendChild(modal);

  const closeBtn   = document.getElementById('closeModalBtn');
  const bodyHost   = document.getElementById('projectModalBody');
  const closeModal = () => { modal.style.display = 'none'; document.body.style.overflow = ''; };

  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal.style.display !== 'none') closeModal(); });

  document.addEventListener('mousedown', (e) => { if (e.target.closest('.state-select')) e.stopPropagation(); }, { capture:true });
  document.addEventListener('click',     (e) => { if (e.target.closest('.state-select')) e.stopPropagation(); }, { capture:true });

  document.addEventListener('click', (e) => {
    const card = e.target.closest('.project-card');
    if (!card || e.target.closest('.state-select')) return;

    const canDelete = card.dataset.canDelete === '1';
    const html = `
      <h2 style="margin-top:0">${escapeHtml(card.dataset.name || '')}</h2>
      ${canDelete ? `
        <button
          type="button"
          id="deleteProjectBtn"
          class="icon-btn icon-btn--trash"
          title="Delete project"
          aria-label="Delete project"
          data-id="${escapeHtml(card.dataset.projectId)}"
          style="flex:0 0 auto"
        >
          <!-- trash icon SVG -->
          <img src="${ICON_TRASH}" alt="Delete" width="22" height="22" loading="eager" decoding="async">
        </button>
      ` : ''}
      ${escapeHtml(card.dataset.description || '') ? `<p>${escapeHtml(card.dataset.description)}</p>` : ''}
      <p><strong>Start Date:</strong> ${escapeHtml(card.dataset.startDate || '-')}</p>
      <p><strong>End Date:</strong> ${escapeHtml(card.dataset.endDate || '-')}</p>
      <p><strong>State:</strong> ${escapeHtml(card.dataset.state || '-')}</p>      
    `;
  bodyHost.innerHTML = html;

  const projectId = card.dataset.projectId;

  fetch(`/Projects/matrix/${projectId}`, { credentials: 'same-origin' })
    .then(r => r.json())
    .then(json => {
      if (!json?.ok) return;
      const rows = json.rows || [];
      const matrixHTML = renderMatrixTable(rows);
      bodyHost.insertAdjacentHTML('beforeend', matrixHTML);
    })
    .catch(() => {
      bodyHost.insertAdjacentHTML('beforeend', `<p style="color:#c00">Could not load tasks for this project.</p>`);
    });

  function renderMatrixTable(rows){
    if (!rows.length) {
      return `<div class="matrix-wrap"><p>No tasks in this project yet.</p></div>`;
    }
    let html = `
    <div class="matrix-wrap">
      <h3 style="margin-top:14px">Tasks × People</h3>
      <table class="matrix-table">
        <thead>
          <tr>
            <th>Task</th>
            <th>Priority</th>
            <th>State</th>
            <th>Assignees</th>
            <th>Limit date</th>
            <th>Duration</th>
          </tr>
        </thead>
        <tbody>
  `;
    rows.forEach(r => {
      const priClass   = ('badge-' + String(r.priority || '').toLowerCase());
      const stateClass = ('badge-' + String(r.state || '').toLowerCase().replace(/\s+/g,'-'));
      const assignees  = r.assignees || '—';
      const limitDisp  = r.limit_date_display || '—';
      const dur        = r.duration || '—';
      const simClass   = (parseInt(r.simulated, 10) === 1) ? 'is-simulated' : '';

      html += `
        <tr class="${simClass}">
          <td>${escapeHtml(r.name || '')}</td>
          <td><span class="badge ${priClass}">${escapeHtml(r.priority || '')}</span></td>
          <td><span class="badge ${stateClass}">${escapeHtml(r.state || '')}</span></td>
          <td>${escapeHtml(assignees)}</td>
          <td>${escapeHtml(limitDisp)}</td>
          <td>${escapeHtml(dur)}</td>
        </tr>
      `;
    });

    html += `
          </tbody>
        </table>
      </div>
    `;
    return html;
  }  

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  });

  //Change state
  document.addEventListener('change', (e) => {
    const select = e.target.closest('.state-select');
    if (!select) return;

    const id = select.getAttribute('data-id');
    const newState = select.value;
    const prev = select.dataset.state || select.value;

    fetch(`/Projects/updateState/${id}`, {
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
      updateCsrfFromResponse(json); 

      const card = select.closest('.project-card');
      if (card) {
        const badge = card.querySelector('.state-badge') || card.querySelector('.project-card-header .badge') || card.querySelector('.badge');
        if (badge) {
          const newClass = 'badge-' + newState.toLowerCase().replace(/\s+/g, '-');
          ['badge-to-begin','badge-active','badge-on-pause','badge-finished'].forEach(c => badge.classList.remove(c));
          badge.classList.add('badge', 'state-badge', newClass);
          badge.textContent = newState;
        }
        card.dataset.state = newState;
      }
      select.dataset.state = newState;
      //recalcCompletionPct();
    })
    .catch(() => {
      select.value = prev;
    });
  });

  //to avoid XSS when injecting dataset values
  function escapeHtml(str) {
    return String(str)
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'",'&#39;');
  }
})();
