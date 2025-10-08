<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    
    public function index(Request $request)
    {
        $filter = $request->query('filter');
        $query = Task::query()->orderByRaw("FIELD(priority,'high','medium','low') ASC")->orderBy('due_date','asc');

        if ($filter === 'completed') $query->where('is_completed', true);
        if ($filter === 'pending') $query->where('is_completed', false);

        $tasks = $query->get()->map(function($t){
            $t->due_soon = $t->isDueSoon();
            return $t;
        });

        return response()->json(['success' => true, 'data' => $tasks]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => ['nullable', Rule::in(['low','medium','high'])],
            'is_completed' => 'sometimes|boolean',
        ]);

        $task = Task::create($data);

        return response()->json(['success' => true, 'data' => $task], 201);
    }

    
    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => ['nullable', Rule::in(['low','medium','high'])],
            'is_completed' => 'sometimes|boolean',
        ]);

        $task->update($data);

        return response()->json(['success' => true, 'data' => $task]);
    }


    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['success' => true]);
    }
}
