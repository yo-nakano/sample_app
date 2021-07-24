<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\Task;
use App\Http\Requests\CreateTask;
use App\Http\Requests\EditTask;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Folder $folder)
    {
        // $folders = Folder::all();
        // ★ ユーザーのフォルダを取得する
        $folders = Auth::user()->folders()->get();

        // $current_folder = Folder::find($id);
        // if (is_null($current_folder)) {
        //     abort(404);
        // }

        // $tasks = Task::where('folder_id', $current_folder->id)->get();
        // $tasks = $current_folder->tasks()->get();

        // 選ばれたフォルダに紐づくタスクを取得する
        $tasks = $folder->tasks()->get();

        return view('tasks/index', [
            'folders' => $folders,
            'current_folder_id' => $folder->id,
            'tasks' => $tasks,
        ]);
    }

    /**
     * GET /folders/{id}/tasks/create
     */
    public function showCreateForm(Folder $folder)
    {
        return view('tasks/create', [
            'folder_id' => $folder->id
        ]);
    }

    public function create(Folder $folder, CreateTask $request)
    {
        // $current_folder = Folder::find($id);
        $task = new Task();
        $task->title = $request->title;
        $task->due_date = $request->due_date;

        // $current_folder->tasks()->save($task);
        $folder->tasks()->save($task);

        return redirect()->route('tasks.index', [
            'id' => $folder->id,
        ]);
    }

    /**
     * GET /folders/{folder}/tasks/{task}/edit
     */
    public function showEditForm(Folder $folder, Task $task)
    {
        $this->checkRelation($folder, $task);
        
        // $task = Task::find($task_id);
        return view('tasks/edit', [
            'task' => $task,
        ]);
    }

    public function edit(Folder $folder, Task $task, EditTask $request)
    {
        $this->checkRelation($folder, $task);

        // $task = Task::find($task_id);
        $task->title = $request->title;
        $task->status = $request->status;
        $task->due_date = $request->due_date;
        $task->save();

        return redirect()->route('tasks.index', [
            'id' => $task->folder_id,
        ]);
    }

    private function checkRelation(Folder $folder, Task $task)
    {
        if ($folder->id !== $task->folder_id) {
            abort(404);
        }
    }
}
