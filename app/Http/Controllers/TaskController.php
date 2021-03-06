<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         //If is admin get all tasks
         if(Gate::allows('view_all_tasks_access')){
            $tasks = Task::with(['creator_user','assigned_user'])->get();
        }
        else {
            //If is user get user's tasks
            $loggedUserId = Auth::id();
            $tasks = Task::with(['assigned_user', 'creator_user'])->where('assigned_user_id',$loggedUserId)->get();
        }

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Declaration of vars in order to use the same form template
        $task = new Task;
        $title = "Nueva Tarea";
        $txtButton = "Agregar";
        $route = route('tasks.store');
        $users = User::all();
        return view('tasks.create', compact('task','title','txtButton','route','users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        //Save validated fields to task
        $task = new Task;
        $task->name = $validated['name'];
        $task->start_date = $validated['start_date'];
        $task->end_date = $validated['end_date'];

        //Save related users to task
        $loggedUserId = Auth::id();
        $creator_user = User::find($loggedUserId);
        
        //Check if the user is allowed to assign users to task
        if(Gate::allows('assigned_user_access')){
            $assigned_user = User::find($request->assigned_user);
        }
        else {
            //if not, creator user is assign to own tasks
            $assigned_user = $creator_user;
        }
        
        $task->creator_user()->associate($creator_user);
        $task->assigned_user()->associate($assigned_user);

        $task->save();

        return redirect()->route('tasks.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //Admin allow only 
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        //Declaration of vars in order to use the same form template
        $update = true;
        $title = "Actualizar Tarea";
        $txtButton = "Actualizar";
        $route = route('tasks.update',['task' => $task]);
        $users = User::all();
        return view('tasks.edit', compact('task','title','txtButton','route','users','update'));
    }

    public function completeTask(){
        $task->done = 1;
        $task->save();

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        //Admin allow only 
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'name' => 'required|max:100|unique:tasks,name,' . $task->id,
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        //Create new task
        $task = Task::create($validated);
        
        //Save related users to task
        $assigned_user = User::find($request->assigned_user);
        $task->assigned_user()->associate($assigned_user);

        // $task->save();

        return redirect()->route('tasks.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        //Admin allow only 
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $task->delete();

        return back();
    }
}
