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
  /* Task card base styles */
  .task-card {
    position: relative; /* needed for badge positioning */
    transition: transform 0.2s, box-shadow 0.2s;
  }

  /* Highlight tasks due within 24h */
  .task-card.due-soon {
    border-left: 8px solid #dc3545; /* thicker red border */
    background-color: #fff0f0;      /* light red background */
    box-shadow: 0 4px 12px rgba(220,53,69,0.2); /* stronger shadow */
    transform: scale(1.02); /* subtle pop-out effect */
  }

  /* Optional “Due Soon!” badge */
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
    text-transform: uppercase;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  }

  /* Completed tasks */
  .task-card.completed {
    opacity: .6;
    text-decoration: line-through;
    background-color: #f8f9fa;
  }

  /* Priority borders */
  .priority-high { border-left: 6px solid #ff6b6b; }
  .priority-medium { border-left: 6px solid #ffc107; }
  .priority-low { border-left: 6px solid #198754; }
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
