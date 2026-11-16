<?php

namespace App\Services\Workflow;

use App\Models\Project;
use App\Models\Task;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class TaskWorkflowService
{
    public function createTask(Project $project, array $data): Task
    {
        $task = Task::create([
            'project_id' => $project->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'assigned_to' => $data['assigned_to'],
            'deadline' => $data['deadline'] ?? null,
            'status' => Task::STATUS_TODO,
        ]);

        ActivityLogger::log('Tasks', 'Task #'.$task->id, 'CREATE', null, $task->toArray(), 'Tugas baru ditambahkan ke proyek '.$project->project_code);

        return $task;
    }

    public static function getTemplates(): array
    {
        return [
            'web_development' => [
                'name' => 'Template Pengembangan Website (5 Tahapan)',
                'tasks' => [
                    ['title' => 'Briefing & Sitemap Kebutuhan', 'description' => 'Mengumpulkan materi konten, warna brand, referensi desain, dan struktur halaman.', 'days' => 2],
                    ['title' => 'Perancangan UI/UX & Wireframe (Figma)', 'description' => 'Mendesain tata letak visual Figma resolusi desktop dan mobile responsive.', 'days' => 5],
                    ['title' => 'Slicing Frontend & Responsivitas Mobile', 'description' => 'Pengkodean komponen UI, animasi, dan layout responsif.', 'days' => 9],
                    ['title' => 'Integrasi Sistem, Form & Database', 'description' => 'Penyambungan form WhatsApp/API, database, dan optimasi performa.', 'days' => 12],
                    ['title' => 'Pengujian Fungsional, QC & Deploy', 'description' => 'Uji coba seluruh tombol, formulir kontak, dan verifikasi akhir QC.', 'days' => 14],
                ],
            ],
            'graphic_design' => [
                'name' => 'Template Desain Grafis & Konten (4 Tahapan)',
                'tasks' => [
                    ['title' => 'Moodboard & Konsep Visual Awal', 'description' => 'Riset konsep gaya desain, palet warna, dan tipografi brand.', 'days' => 2],
                    ['title' => 'Pengerjaan Draft Desain Master', 'description' => 'Pembuatan rancangan desain utama sesuai ukuran spesifikasi.', 'days' => 5],
                    ['title' => 'Finalisasi Format Siap Cetak/Digital', 'description' => 'Ekspor format resolusi tinggi (PNG, PDF siap cetak, SVG master).', 'days' => 7],
                    ['title' => 'Quality Control & Cek Kesesuaian Brief', 'description' => 'Verifikasi resolusi 300DPI, bleed, dan kepatuhan brief klien.', 'days' => 8],
                ],
            ],
            'bundling' => [
                'name' => 'Template Paket Bundling Digital (4 Tahapan)',
                'tasks' => [
                    ['title' => 'Koordinasi Tim & Timeline Deliverables', 'description' => 'Sinkronisasi timeline pengerjaan aset grafis dan platform website.', 'days' => 2],
                    ['title' => 'Pengerjaan Identitas Visual & Branding', 'description' => 'Desain logo, konten promosi, dan aset grafis pendukung.', 'days' => 6],
                    ['title' => 'Pembangunan Website & Pemasangan Aset', 'description' => 'Pembuatan web dan penempatan aset grafis yang telah selesai.', 'days' => 11],
                    ['title' => 'Quality Control Menyeluruh & Uji Coba', 'description' => 'Pemeriksaan kelengkapan seluruh item dalam paket bundling.', 'days' => 14],
                ],
            ],
        ];
    }

    public function applyTemplate(Project $project, string $templateKey, ?int $defaultPicId = null): array
    {
        $templates = self::getTemplates();
        if (! isset($templates[$templateKey])) {
            $templateKey = 'web_development';
        }

        $template = $templates[$templateKey];
        $createdTasks = [];
        $assignedTo = $defaultPicId ?? $project->project_manager_id ?? Auth::id();
        $baseDate = $project->start_date ? Carbon::parse($project->start_date) : Carbon::now();

        foreach ($template['tasks'] as $item) {
            $deadline = $baseDate->copy()->addDays($item['days'])->format('Y-m-d');
            $createdTasks[] = $this->createTask($project, [
                'title' => $item['title'],
                'description' => $item['description'],
                'assigned_to' => $assignedTo,
                'deadline' => $deadline,
            ]);
        }

        ActivityLogger::log('Projects', $project->project_code, 'UPDATE', null, null, 'Penerapan template tugas otomatis: '.$template['name']);

        return $createdTasks;
    }

    public function startTask(Task $task): Task
    {
        $old = $task->toArray();
        $task->status = Task::STATUS_IN_PROGRESS;
        $task->save();

        // Also ensure project is in progress
        app(ProjectWorkflowService::class)->startWorking($task->project);

        ActivityLogger::log('Tasks', 'Task #'.$task->id, 'STATUS_CHANGE', $old, $task->toArray(), 'Anggota mulai mengerjakan tugas');

        return $task;
    }

    public function submitTask(Task $task, string $submissionUrl, ?string $notes = null): Task
    {
        if (trim($submissionUrl) === '') {
            throw new InvalidArgumentException('Tautan file hasil pekerjaan (Figma/GitHub/GDrive) wajib disertakan.');
        }

        $old = $task->toArray();
        $task->status = Task::STATUS_IN_REVIEW;
        $task->submission_url = $submissionUrl;
        $task->submission_notes = $notes;
        $task->save();

        ActivityLogger::log('Tasks', 'Task #'.$task->id, 'STATUS_CHANGE', $old, $task->toArray(), 'Hasil tugas diserahkan untuk peninjauan');

        return $task;
    }

    public function requestRevision(Task $task, string $reviewNotes): Task
    {
        if (trim($reviewNotes) === '') {
            throw new InvalidArgumentException('Catatan revisi tugas wajib dicatat.');
        }

        $old = $task->toArray();
        $task->status = Task::STATUS_REVISION;
        $task->review_notes = $reviewNotes;
        $task->reviewed_by = Auth::id();
        $task->save();

        ActivityLogger::log('Tasks', 'Task #'.$task->id, 'STATUS_CHANGE', $old, $task->toArray(), 'Reviewer meminta revisi tugas: '.$reviewNotes);

        return $task;
    }

    public function approveTask(Task $task): Task
    {
        $old = $task->toArray();
        $task->status = Task::STATUS_DONE;
        $task->reviewed_by = Auth::id();
        $task->save();

        ActivityLogger::log('Tasks', 'Task #'.$task->id, 'STATUS_CHANGE', $old, $task->toArray(), 'Reviewer menyetujui tugas (DONE)');

        return $task;
    }
}
