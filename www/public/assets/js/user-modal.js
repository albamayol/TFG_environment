(function initUserModal() {

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

  const modal = document.getElementById('userModal');
  const body  = document.getElementById('userModalBody');
  const table = document.querySelector('#usersTable tbody');
  if (!modal || !table) return;

  if (modal.parentNode !== document.body) document.body.appendChild(modal);

  const closeBtn   = document.getElementById('closeModalBtn');
  const bodyHost   = document.getElementById('userModalBody');
  const closeModal = () => { modal.style.display = 'none'; document.body.style.overflow = ''; };

  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal.style.display !== 'none') closeModal(); });

  table.addEventListener('click', (e) => {
    if (e.target.closest('.js-delete-user')) return;
    const row = e.target.closest('.user-row');
    if(!row) return;

    const name     = row.dataset.name || '';
    const surnames = row.dataset.surnames || '';
    const email    = row.dataset.email || '-';
    
    let tasks = [];
    try { 
        tasks = JSON.parse(row.dataset.tasks || '[]'); 
    } catch (_) { 
        tasks = []; 
    }

    body.innerHTML = `
      <h2 style="margin:0 0 6px 0">${escapeHtml(name)}</h2>
      ${surnames ? `<p style="margin:0 0 10px 0">${escapeHtml(surnames)}</p>` : ''}
      <p><strong>Email:</strong> ${escapeHtml(email)}</p>
      ${renderTasksTable(tasks)}
    `;

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
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

  function renderTasksTable(rows) {
    if (!rows || !rows.length) {
      return `<div class="matrix-wrap"><p>No tasks for this user.</p></div>`;
    }
    let html = `
      <div class="matrix-wrap">
        <h3 style="margin-top:14px">Tasks</h3>
        <table class="matrix-table">
          <thead>
            <tr>
              <th>Task</th>
              <th>Priority</th>
              <th>State</th>
              <th>Project</th>
              <th>Limit date</th>
              <th>Duration</th>
            </tr>
          </thead>
          <tbody>
    `;
    rows.forEach(r => {
      const priClass   = 'badge-' + String(r.priority || '').toLowerCase();
      const stateClass = 'badge-' + String(r.state || '').toLowerCase().replace(/\s+/g, '-');
      const project    = r.origin_of_task || (r.id_project ? `#${r.id_project}` : '—');
      const limitDisp  = toLocal(r.limit_date) || '—';
      const dur        = r.duration || '—';
      const simClass   = String(r.simulated) === '1' ? 'is-simulated' : '';

      html += `
        <tr class="${simClass}">
          <td>${escapeHtml(r.name || '')}</td>
          <td><span class="badge ${priClass}">${escapeHtml(r.priority || '')}</span></td>
          <td><span class="badge ${stateClass}">${escapeHtml(r.state || '')}</span></td>
          <td>${escapeHtml(project)}</td>
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

  function toLocal(utc) {
    if (!utc) return '';
    const iso = utc.replace(' ', 'T') + 'Z';
    const d = new Date(iso);
    if (isNaN(d)) return utc; 
    return d.toLocaleString(undefined, { year:'numeric', month:'2-digit', day:'2-digit', hour:'2-digit', minute:'2-digit' });
  }

})();
