<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\Workflow\ProjectWorkflowService;
use App\Services\Workflow\TaskWorkflowService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        abort_if(! auth()->user()->canAccessModule('projects'), 403, 'Anda tidak memiliki hak akses modul Proyek.');

        $user = auth()->user();

        $projects = Project::with(['order.customer', 'order.service', 'projectManager', 'tasks.assignee', 'documentations'])
            ->latest()
            ->paginate(15);

        $myTasksQuery = Task::with(['project.order.customer', 'assignee']);
        if (! $user->isSuperAdmin() && ! $user->isCeo() && ! $user->isQc()) {
            $myTasksQuery->where('assigned_to', $user->id);
        }
        $myTasks = $myTasksQuery->latest()->get();

        $qcProjects = Project::with(['order.customer', 'order.service', 'projectManager', 'tasks.assignee'])
            ->where('status', Project::STATUS_REVIEW)
            ->latest()
            ->get();

        $qcTasks = Task::with(['project.order.customer', 'assignee'])
            ->where('status', Task::STATUS_IN_REVIEW)
            ->latest()
            ->get();

        // Orders that are confirmed or in progress without a project yet
        $eligibleOrders = Order::with(['customer', 'service'])
            ->whereIn('status', [Order::STATUS_CONFIRMED, Order::STATUS_IN_PROGRESS])
            ->whereDoesntHave('project')
            ->latest()
            ->get();

        $team = User::where('role', '!=', 'customer')->get();
        $taskTemplates = TaskWorkflowService::getTemplates();

        return view('projects.index', compact('projects', 'myTasks', 'qcProjects', 'qcTasks', 'eligibleOrders', 'team', 'taskTemplates'));
    }

    public function store(Request $request, ProjectWorkflowService $workflow, TaskWorkflowService $taskWorkflow)
    {
        abort_if(! auth()->user()->canAccessModule('projects'), 403, 'Anda tidak memiliki hak akses.');

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_manager_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'target_completion_date' => 'nullable|date',
            'apply_template' => 'nullable',
            'template_key' => 'nullable|string',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $project = $workflow->createProjectFromOrder($order, $validated);

        $templateKey = $validated['template_key'] ?? (is_string($validated['apply_template'] ?? null) && ! empty($validated['apply_template']) && $validated['apply_template'] !== '1' ? $validated['apply_template'] : null);
        if ($templateKey || ! empty($validated['apply_template'])) {
            $key = $templateKey ?: ($order->service->category ?? 'web_development');
            $taskWorkflow->applyTemplate($project, $key, $project->project_manager_id);
        }

        return back()->with('success', 'Proyek '.$project->project_code.' berhasil dibuat dengan tugas awal terkonfigurasi!');
    }

    public function applyTemplate(Request $request, Project $project, TaskWorkflowService $taskWorkflow)
    {
        abort_if(! auth()->user()->canAccessModule('projects'), 403, 'Anda tidak memiliki hak akses.');

        $validated = $request->validate([
            'template_key' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $taskWorkflow->applyTemplate($project, $validated['template_key'], $validated['assigned_to'] ?? null);

        return back()->with('success', 'Template tugas berhasil diterapkan ke proyek '.$project->project_code.'!');
    }

    public function start(Project $project, ProjectWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('projects'), 403, 'Anda tidak memiliki hak akses.');

        $workflow->startWorking($project);

        return back()->with('success', 'Pengerjaan proyek '.$project->project_code.' resmi dimulai!');
    }

    public function submitQc(Request $request, Project $project, ProjectWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('projects'), 403, 'Anda tidak memiliki hak akses.');

        $request->validate([
            'deliverable_url' => 'required|url',
            'notes' => 'nullable|string',
        ]);

        $workflow->submitForQc($project, $request->deliverable_url, $request->notes);

        return back()->with('success', 'Hasil pekerjaan proyek '.$project->project_code.' berhasil diajukan ke QC!');
    }

    public function approveQc(Request $request, Project $project, ProjectWorkflowService $workflow)
    {
        abort_if(! auth()->user()->isQc() && ! auth()->user()->isSuperAdmin() && ! auth()->user()->isCeo(), 403, 'Hanya QC atau Super Admin yang dapat menyetujui hasil proyek.');

        $workflow->approveQc($project, $request->notes);

        return back()->with('success', 'Proyek '.$project->project_code.' telah disetujui oleh QC dan dinyatakan COMPLETED!');
    }

    public function requestRevision(Request $request, Project $project, ProjectWorkflowService $workflow)
    {
        abort_if(! auth()->user()->isQc() && ! auth()->user()->isSuperAdmin() && ! auth()->user()->isCeo(), 403, 'Hanya QC atau Super Admin yang dapat meminta revisi proyek.');

        $request->validate([
            'qc_notes' => 'required|string',
        ]);

        $workflow->requestRevision($project, $request->qc_notes);

        return back()->with('success', 'Catatan revisi untuk proyek '.$project->project_code.' berhasil dicatat.');
    }

    public function cancel(Request $request, Project $project, ProjectWorkflowService $workflow)
    {
        abort_if(! auth()->user()->isCeo() && ! auth()->user()->isSuperAdmin(), 403, 'Hanya CEO atau Super Admin yang berwenang membatalkan proyek.');

        $request->validate([
            'cancellation_reason' => 'required|string',
        ]);

        $workflow->cancelProject($project, $request->cancellation_reason);

        return back()->with('success', 'Proyek '.$project->project_code.' berhasil dibatalkan.');
    }
}
