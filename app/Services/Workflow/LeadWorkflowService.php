<?php

namespace App\Services\Workflow;

use App\Models\Customer;
use App\Models\Lead;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LeadWorkflowService
{
    public function createLead(array $data): Lead
    {
        return DB::transaction(function () use ($data) {
            $prefix = 'VSC-LEAD-'.Carbon::now()->format('Ym').'-';
            $count = Lead::where('lead_code', 'like', $prefix.'%')->count() + 1;
            $leadCode = $prefix.str_pad((string) $count, 3, '0', STR_PAD_LEFT);

            // Link or create customer record if needed
            $customer = null;
            if (! empty($data['customer_phone'])) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $data['customer_phone']],
                    [
                        'name' => $data['customer_name'],
                        'email' => $data['customer_email'] ?? null,
                        'type' => 'individu',
                    ]
                );
            }

            $lead = Lead::create([
                'lead_code' => $leadCode,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'service_id' => $data['service_id'] ?? null,
                'customer_id' => $customer?->id,
                'source' => $data['source'] ?? 'website',
                'estimated_value' => $data['estimated_value'] ?? 0,
                'status' => Lead::STATUS_NEW,
                'assigned_to' => $data['assigned_to'] ?? null,
                'communication_notes' => $data['communication_notes'] ?? null,
            ]);

            ActivityLogger::log('Leads', $lead->lead_code, 'CREATE', null, $lead->toArray(), 'Lead baru masuk dari '.$lead->source);

            return $lead;
        });
    }

    public function reviewLead(Lead $lead, array $data = []): Lead
    {
        if ($lead->status !== Lead::STATUS_NEW) {
            throw new InvalidArgumentException('Hanya lead berstatus NEW yang dapat direview.');
        }

        return DB::transaction(function () use ($lead, $data) {
            $old = $lead->toArray();

            if (! $lead->customer_id && ! empty($lead->customer_phone)) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $lead->customer_phone],
                    [
                        'name' => $lead->customer_name,
                        'email' => $lead->customer_email,
                        'type' => 'individu',
                    ]
                );
                $lead->customer_id = $customer->id;
            }

            if (! empty($data['service_id'])) {
                $lead->service_id = $data['service_id'];
            }
            if (isset($data['estimated_value']) && $data['estimated_value'] !== null && $data['estimated_value'] !== '') {
                $lead->estimated_value = $data['estimated_value'];
            }
            if (! empty($data['assigned_to'])) {
                $lead->assigned_to = $data['assigned_to'];
            }
            if (! empty($data['review_notes'])) {
                $lead->review_notes = $data['review_notes'];
            }

            $lead->status = Lead::STATUS_REVIEWED;
            $lead->reviewed_at = Carbon::now();
            $lead->reviewed_by = Auth::id() ?? $lead->assigned_to;
            $lead->save();

            ActivityLogger::log(
                'Leads',
                $lead->lead_code,
                'STATUS_CHANGE',
                $old,
                $lead->toArray(),
                'Review Lead selesai: Status berubah dari '.$old['status'].' ke REVIEWED'.(! empty($data['review_notes']) ? ' ('.$data['review_notes'].')' : '')
            );

            return $lead;
        });
    }

    public function logCommunication(Lead $lead, string $notes, array $meta = []): Lead
    {
        if (in_array($lead->status, [Lead::STATUS_WON, Lead::STATUS_LOST], true)) {
            throw new InvalidArgumentException('Lead yang sudah WON atau LOST tidak dapat diubah melalui follow-up.');
        }

        $old = $lead->toArray();
        $contactDate = ! empty($meta['contact_date']) ? Carbon::parse($meta['contact_date'])->format('d/m/Y') : Carbon::now()->format('d/m/Y H:i');
        $resultLabel = ! empty($meta['follow_up_result']) ? ' [Hasil: '.$meta['follow_up_result'].']' : '';
        $nextDateLabel = ! empty($meta['next_follow_up_date']) ? ' (Follow-up Berikutnya: '.Carbon::parse($meta['next_follow_up_date'])->format('d/m/Y').')' : '';

        $lead->communication_notes = ($lead->communication_notes ? $lead->communication_notes."\n---\n" : '').
            '['.$contactDate.']'.$resultLabel.' '.$notes.$nextDateLabel;

        if (! empty($meta['contact_date'])) {
            $lead->last_contact_date = $meta['contact_date'];
        } else {
            $lead->last_contact_date = Carbon::now()->toDateString();
        }

        if (! empty($meta['follow_up_result'])) {
            $lead->follow_up_result = $meta['follow_up_result'];
        }
        if (array_key_exists('next_follow_up_date', $meta)) {
            $lead->next_follow_up_date = $meta['next_follow_up_date'] ?: null;
        }

        if (in_array($lead->status, [Lead::STATUS_NEW, Lead::STATUS_REVIEWED, Lead::STATUS_CONTACTED], true)) {
            $lead->status = Lead::STATUS_FOLLOW_UP;
        }

        $lead->save();

        ActivityLogger::log(
            'Leads',
            $lead->lead_code,
            'STATUS_CHANGE',
            $old,
            $lead->toArray(),
            'Pencatatan follow-up prospek ('.$old['status'].' -> '.$lead->status.')'
        );

        return $lead;
    }

    public function markQuotationSent(Lead $lead): Lead
    {
        $old = $lead->toArray();
        $lead->status = Lead::STATUS_QUOTATION_SENT;
        $lead->save();

        ActivityLogger::log(
            'Leads',
            $lead->lead_code,
            'STATUS_CHANGE',
            $old,
            $lead->toArray(),
            'Penawaran quotation dikirim kepada prospek ('.$old['status'].' -> QUOTATION_SENT)'
        );

        return $lead;
    }

    public function markOffered(Lead $lead): Lead
    {
        return $this->markQuotationSent($lead);
    }

    public function markWon(Lead $lead): Lead
    {
        $old = $lead->toArray();
        $lead->status = Lead::STATUS_WON;
        $lead->save();

        ActivityLogger::log('Leads', $lead->lead_code, 'STATUS_CHANGE', $old, $lead->toArray(), 'Lead berhasil dikonversi menjadi pesanan (WON)');

        return $lead;
    }

    public function markLost(Lead $lead, string $reason): Lead
    {
        if (trim($reason) === '') {
            throw new InvalidArgumentException('Alasan prospek ditandai lost wajib diisi.');
        }

        $old = $lead->toArray();
        $lead->status = Lead::STATUS_LOST;
        $lead->lost_reason = $reason;
        $lead->save();

        ActivityLogger::log('Leads', $lead->lead_code, 'STATUS_CHANGE', $old, $lead->toArray(), 'Prospek dibatalkan / lost: '.$reason);

        return $lead;
    }
}
