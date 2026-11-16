<?php

namespace App\Services\Workflow;

use App\Models\Order;
use App\Models\Project;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProjectWorkflowService
{
    public function createProjectFromOrder(Order $order, array $data): Project
    {
        return DB::transaction(function () use ($order, $data) {
            $prefix = 'VSC-PRJ-'.Carbon::now()->format('Ym').'-';
            $count = Project::where('project_code', 'like', $prefix.'%')->count() + 1;
            $prjCode = $prefix.str_pad((string) $count, 3, '0', STR_PAD_LEFT);

            $project = Project::create([
                'project_code' => $prjCode,
                'order_id' => $order->id,
                'title' => $data['title'] ?? ('Proyek '.$order->service->name.' - '.$order->customer->name),
                'description' => $data['description'] ?? $order->brief_description,
                'project_manager_id' => $data['project_manager_id'] ?? Auth::id(),
                'start_date' => $data['start_date'] ?? date('Y-m-d'),
                'target_completion_date' => $data['target_completion_date'] ?? $order->deadline,
                'status' => Project::STATUS_NOT_STARTED,
            ]);

            ActivityLogger::log('Projects', $project->project_code, 'CREATE', null, $project->toArray(), 'Proyek dibuat dari pesanan '.$order->order_code);

            return $project;
        });
    }

    public function startWorking(Project $project): Project
    {
        if ($project->status === Project::STATUS_NOT_STARTED) {
            $old = $project->toArray();
            $project->status = Project::STATUS_IN_PROGRESS;
            $project->save();

            // Also trigger order to in_progress
            app(OrderWorkflowService::class)->markInProgress($project->order);

            ActivityLogger::log('Projects', $project->project_code, 'STATUS_CHANGE', $old, $project->toArray(), 'Pengerjaan proyek resmi dimulai');
        }

        return $project;
    }

    public function submitForQc(Project $project, string $deliverableUrl, ?string $notes = null): Project
    {
        if (! in_array($project->status, [Project::STATUS_IN_PROGRESS, Project::STATUS_REVISION], true)) {
            throw new InvalidArgumentException('Proyek belum dapat diajukan ke QC.');
        }

        $old = $project->toArray();
        $project->status = Project::STATUS_REVIEW;
        $project->deliverable_url = $deliverableUrl;
        if ($notes) {
            $project->qc_notes = ($project->qc_notes ? $project->qc_notes."\n---\n" : '').
                '['.Carbon::now()->format('d/m/Y H:i').' Pengajuan] '.$notes;
        }
        $project->save();

        ActivityLogger::log('Projects', $project->project_code, 'STATUS_CHANGE', $old, $project->toArray(), 'Hasil proyek diajukan ke QC untuk peninjauan: '.$deliverableUrl);

        return $project;
    }

    public function requestRevision(Project $project, string $qcNotes): Project
    {
        if (trim($qcNotes) === '') {
            throw new InvalidArgumentException('Catatan revisi QC wajib diisi.');
        }

        if ($project->status !== Project::STATUS_REVIEW) {
            throw new InvalidArgumentException('Hanya proyek status REVIEW yang dapat diminta revisi.');
        }

        $old = $project->toArray();
        $project->status = Project::STATUS_REVISION;
        $project->qc_notes = ($project->qc_notes ? $project->qc_notes."\n---\n" : '').
            '['.Carbon::now()->format('d/m/Y H:i').' Catatan QC] '.$qcNotes;
        $project->save();

        ActivityLogger::log('Projects', $project->project_code, 'STATUS_CHANGE', $old, $project->toArray(), 'QC meminta perbaikan/revisi proyek: '.$qcNotes);

        return $project;
    }

    public function approveQc(Project $project, ?string $notes = null): Project
    {
        if ($project->status !== Project::STATUS_REVIEW) {
            throw new InvalidArgumentException('Hanya proyek yang sedang ditinjau QC (REVIEW) yang dapat disetujui.');
        }

        return DB::transaction(function () use ($project, $notes) {
            $old = $project->toArray();
            $project->status = Project::STATUS_COMPLETED;
            if ($notes) {
                $project->qc_notes = ($project->qc_notes ? $project->qc_notes."\n---\n" : '').
                    '['.Carbon::now()->format('d/m/Y H:i').' Persetujuan QC] '.$notes;
            }
            $project->save();

            // Check if order can be completed (requires payment completion as well)
            app(OrderWorkflowService::class)->checkAndTriggerOrderCompletion($project->order);

            ActivityLogger::log('Projects', $project->project_code, 'STATUS_CHANGE', $old, $project->toArray(), 'QC menyetujui hasil akhir proyek (COMPLETED)');

            return $project;
        });
    }

    public function cancelProject(Project $project, string $reason): Project
    {
        if (trim($reason) === '') {
            throw new InvalidArgumentException('Alasan pembatalan proyek wajib dicatat.');
        }

        $old = $project->toArray();
        $project->status = Project::STATUS_CANCELLED;
        $project->cancellation_reason = $reason;
        $project->save();

        ActivityLogger::log('Projects', $project->project_code, 'STATUS_CHANGE', $old, $project->toArray(), 'Proyek dibatalkan: '.$reason);

        return $project;
    }
}
