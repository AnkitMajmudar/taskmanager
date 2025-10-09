<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class TaskController extends Controller
{
    
        public function index(Request $request)
    {
        $filter = $request->query('filter');
        $perPage = $request->query('per_page', 6); // default 10 items per page

        $query = Task::query()
    ->orderByRaw("is_completed ASC") // incomplete (0) first, completed (1) last
    ->orderByRaw("FIELD(priority,'high','medium','low') ASC")
    ->orderBy('due_date','asc');


        if ($filter === 'completed') $query->where('is_completed', true);
        if ($filter === 'pending') $query->where('is_completed', false);

        // Use paginate instead of get
        $tasks = $query->paginate($perPage);

        // Add due_soon attribute
        $tasks->getCollection()->transform(function($t){
            $t->due_soon = $t->isDueSoon();
            return $t;
        });

        return response()->json([
            'success' => true,
            'data' => $tasks->items(),
            'pagination' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ]
        ]);
    }



    public function store(Request $request)
    {
        $data = $request->validate([
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => ['nullable', Rule::in(['low','medium','high'])],
        ]);

        $task = Task::create($data);

        return response()->json(['success'=>true,'data'=>$task], 201);
    }

    
    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'task_name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'due_date' => 'nullable|date',
            'priority' => ['nullable', Rule::in(['low','medium','high'])],
            'is_completed' => 'sometimes|boolean',
        ]);

        $task->update($data);

        return response()->json(['success'=>true,'data'=>$task]);
    }


    public function updateStatus(Request $request, Task $task)
    {
        $data = $request->validate([
            'is_completed' => 'required|boolean',
        ]);
        $task->is_completed = $data['is_completed'];
        $task->save();

        return response()->json(['success'=>true,'data'=>$task]);
    }


    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['success'=>true]);
    }
}
