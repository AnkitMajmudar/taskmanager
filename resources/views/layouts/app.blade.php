<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Task Manager</title>

  <!-- Bootstrap 5 CSS (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
 .task-card {
    position: relative;
    transition: transform 0.2s, box-shadow 0.2s;
    border-radius: 0.5rem;
    background-color: #fff;
    padding: 0;
}

.task-card .card-body {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.task-card.due-soon {
    border-left: 6px solid #dc3545;
    background-color: #fff4f4;
    box-shadow: 0 2px 6px rgba(220,53,69,0.15);
}

.due-soon-badge {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    font-size: 0.75rem;
    font-weight: bold;
    padding: 0.25rem 0.5rem;
    background-color: #dc3545;
    color: #fff;
    border-radius: 0.25rem;
}

.task-card.completed {
    opacity: 0.65;
    text-decoration: line-through;
    background-color: #f8f9fa;
}

.card-title {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 65%;
    margin-bottom: 0;
}

.card-text {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0.5rem;
}

.task-card .btn {
    margin-right: 0.25rem;
    margin-top: 0.25rem;
}

.priority-high { border-left: 4px solid #ff6b6b; }
.priority-medium { border-left: 4px solid #ffc107; }
.priority-low { border-left: 4px solid #198754; }

.task-detail-box {
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 10;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.95);
    padding: 1rem;
    overflow-y: auto;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    border-radius: 0.5rem;
    font-size: 0.95rem;
}

.task-detail-box h5 {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
}

.task-detail-box p {
    margin-bottom: 0.25rem;
}

.task-detail-box button.close-box {
    position: absolute;
    top: 5px;
    right: 5px;
}

</style>


</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="{{ route('tasks.index') }}">TaskManager</a>
    </div>
  </nav>

  <main class="py-4">
    <div class="container">
      @yield('content')
    </div>
  </main>

  
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  </script>

  @stack('scripts')
</body>
</html>
