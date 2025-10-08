@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h2>My Tasks</h2>
  <div>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#taskModal" id="addTaskBtn">+ Add Task</button>
    <select id="filterSelect" class="form-select d-inline-block w-auto ms-2">
      <option value="">All</option>
      <option value="pending">Pending</option>
      <option value="completed">Completed</option>
    </select>
  </div>
</div>

<div id="tasksContainer" class="row g-3">

</div>


<div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="taskForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="taskModalTitle">Add Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
          <input type="hidden" id="taskId" name="task_id">
          <div class="mb-3">
            <label class="form-label">Task Name</label>
            <input type="text" id="task_name" name="task_name" class="form-control" required>
            <div class="invalid-feedback" id="err_task_name"></div>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="3"></textarea>
            <div class="invalid-feedback" id="err_description"></div>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Due Date</label>
              <input type="datetime-local" id="due_date" name="due_date" class="form-control">
              <div class="invalid-feedback" id="err_due_date"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Priority</label>
              <select id="priority" name="priority" class="form-select">
                <option value="high">High</option>
                <option value="medium" selected>Medium</option>
                <option value="low">Low</option>
              </select>
              <div class="invalid-feedback" id="err_priority"></div>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="saveTaskBtn" type="submit" class="btn btn-primary">Save Task</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const tasksContainer = document.getElementById('tasksContainer');
  const taskModal = new bootstrap.Modal(document.getElementById('taskModal'));
  const taskForm = document.getElementById('taskForm');
  const addTaskBtn = document.getElementById('addTaskBtn');
  const taskModalTitle = document.getElementById('taskModalTitle');
  const filterSelect = document.getElementById('filterSelect');

  let editingId = null;

  async function fetchTasks(){
    const filter = filterSelect.value;
    try {
      const res = await axios.get('/api/tasks', { params: { filter }});
      renderTasks(res.data.data || []);
    } catch (err) {
      console.error(err);
      alert('Failed to fetch tasks');
    }
  }

  function renderTasks(tasks){
    tasksContainer.innerHTML = '';
    if (!tasks.length) {
      tasksContainer.innerHTML = `<div class="col-12"><div class="card p-4 text-center text-muted">No tasks yet</div></div>`;
      return;
    }
    tasks.forEach(task => {
      const col = document.createElement('div');
      col.className = 'col-md-6 col-lg-4';

      const isCompleted = task.is_completed ? 'completed' : '';
      const dueSoon = task.due_soon ? 'due-soon' : '';
      const priorityClass = task.priority ? 'priority-'+task.priority : '';
      const dueText = task.due_date ? new Date(task.due_date).toLocaleString() : 'No due date';

        const dueBadge = task.due_soon 
      ? `<span class="due-soon-badge">Due Soon!</span>` 
      : '';

      col.innerHTML = `
  <div class="card task-card ${isCompleted} ${dueSoon} ${priorityClass} h-100 position-relative">
    ${dueBadge}
    <div class="card-body d-flex flex-column">
      <div class="d-flex justify-content-between align-items-start">
        <h5 class="card-title mb-1">${escapeHtml(task.task_name)}</h5>
        <small class="text-muted">${escapeHtml(task.priority || '')}</small>
      </div>
      <p class="card-text mb-2">${escapeHtml(task.description || '')}</p>
      <div class="mt-auto">
        <div class="d-flex justify-content-between align-items-center">
          <small class="text-muted">Due: ${escapeHtml(dueText)}</small>
          <div>
            <button class="btn btn-sm btn-outline-primary me-1 btn-edit" data-id="${task.id}">Edit</button>
            <button class="btn btn-sm btn-outline-danger me-1 btn-delete" data-id="${task.id}">Delete</button>
            <button class="btn btn-sm ${task.is_completed ? 'btn-warning' : 'btn-success'} btn-toggle" data-id="${task.id}" data-completed="${task.is_completed}">${task.is_completed ? 'Mark Incomplete' : 'Mark Done'}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
`;

      tasksContainer.appendChild(col);
    });

  
    document.querySelectorAll('.btn-edit').forEach(btn=>{
      btn.addEventListener('click', async (e)=>{
        const id = e.currentTarget.dataset.id;
        await openEdit(id);
      });
    });
    document.querySelectorAll('.btn-delete').forEach(btn=>{
      btn.addEventListener('click', async (e)=>{
        const id = e.currentTarget.dataset.id;
        if (!confirm('Delete this task?')) return;
        await deleteTask(id);
      });
    });
    document.querySelectorAll('.btn-toggle').forEach(btn=>{
      btn.addEventListener('click', async (e)=>{
        const id = e.currentTarget.dataset.id;
        const curr = e.currentTarget.dataset.completed === 'true';
        await toggleStatus(id, !curr);
      });
    });
  }

  
  function escapeHtml(unsafe){
    if (!unsafe) return '';
    return unsafe.replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'","&#039;");
  }

  taskForm.addEventListener('submit', async (e)=>{
    e.preventDefault();
    clearErrors();
    const form = new FormData(taskForm);
    const payload = {
      task_name: form.get('task_name'),
      description: form.get('description'),
      due_date: form.get('due_date') ? new Date(form.get('due_date')).toISOString() : null,
      priority: form.get('priority'),
    };

    try {
      if (editingId) {
        const res = await axios.put(`/api/tasks/${editingId}`, payload);
        editingId = null;
      } else {
        const res = await axios.post('/api/tasks', payload);
      }
      taskModal.hide();
      taskForm.reset();
      await fetchTasks();
    } catch (err) {
      if (err.response && err.response.data && err.response.data.errors) {
        showErrors(err.response.data.errors);
      } else {
        console.error(err);
        alert('Save failed');
      }
    }
  });

  
  addTaskBtn.addEventListener('click', ()=>{
    editingId = null;
    taskModalTitle.textContent = 'Add Task';
    taskForm.reset();
    clearErrors();
  });

  
  async function openEdit(id){
    try {
      const res = await axios.get('/api/tasks', { params: { }});
      const task = res.data.data.find(t=>t.id==id);
      if (!task) { alert('Task not found'); return; }
      editingId = id;
      taskModalTitle.textContent = 'Edit Task';
      document.getElementById('task_name').value = task.task_name || '';
      document.getElementById('description').value = task.description || '';
      if (task.due_date) {
        const d = new Date(task.due_date);
        const local = new Date(d.getTime() - d.getTimezoneOffset()*60000).toISOString().slice(0,16);
        document.getElementById('due_date').value = local;
      } else {
        document.getElementById('due_date').value = '';
      }
      document.getElementById('priority').value = task.priority || 'medium';
      clearErrors();
      taskModal.show();
    } catch (err) {
      console.error(err);
      alert('Failed to load task for edit');
    }
  }

  async function deleteTask(id){
    try {
      await axios.delete(`/api/tasks/${id}`);
      await fetchTasks();
    } catch (err) {
      console.error(err);
      alert('Delete failed');
    }
  }

  async function toggleStatus(id, completed){
    try {
      await axios.patch(`/api/tasks/${id}/status`, { is_completed: completed });
      await fetchTasks();
    } catch (err) {
      console.error(err);
      alert('Update status failed');
    }
  }

  function clearErrors(){
    ['task_name','description','due_date','priority'].forEach(k=>{
      const el = document.getElementById('err_'+k);
      if (el) { el.textContent = ''; el.style.display='none'; }
      const input = document.getElementById(k);
      if (input) input.classList.remove('is-invalid');
    });
  }

  function showErrors(errors){
    Object.keys(errors).forEach(k=>{
      const el = document.getElementById('err_'+k);
      if (el) { el.textContent = errors[k][0]; el.style.display='block'; }
      const input = document.getElementById(k);
      if (input) input.classList.add('is-invalid');
    });
  }

  filterSelect.addEventListener('change', fetchTasks);

  
  fetchTasks();
});
</script>
@endpush
