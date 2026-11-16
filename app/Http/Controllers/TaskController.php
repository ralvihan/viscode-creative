<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Services\Workflow\TaskWorkflowService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Project $project, TaskWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('tasks'), 403, 'Anda tidak memiliki hak akses modul Tugas.');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'deadline' => 'nullable|date',
        ]);

        $task = $workflow->createTask($project, $validated);

        return back()->with('success', 'Tugas "'.$task->title.'" berhasil ditambahkan ke proyek!');
    }

    public function start(Task $task, TaskWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('tasks'), 403, 'Anda tidak memiliki hak akses.');

        $workflow->startTask($task);

        return back()->with('success', 'Status pengerjaan tugas diperbarui: IN_PROGRESS.');
    }

    public function submit(Request $request, Task $task, TaskWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('tasks'), 403, 'Anda tidak memiliki hak akses.');

        $request->validate([
            'submission_url' => 'required|url',
            'submission_notes' => 'nullable|string',
        ]);

        $workflow->submitTask($task, $request->submission_url, $request->submission_notes);

        return back()->with('success', 'Hasil tugas berhasil diajukan untuk review (IN_REVIEW)!');
    }

    public function requestRevision(Request $request, Task $task, TaskWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('tasks'), 403, 'Anda tidak memiliki hak akses.');

        $request->validate([
            'review_notes' => 'required|string',
        ]);

        $workflow->requestRevision($task, $request->review_notes);

        return back()->with('success', 'Catatan revisi tugas berhasil dicatat (REVISION).');
    }

    public function approve(Task $task, TaskWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('tasks'), 403, 'Anda tidak memiliki hak akses.');

        $workflow->approveTask($task);

        return back()->with('success', 'Tugas disetujui (DONE)!');
    }
}
