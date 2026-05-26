<?php

namespace App\Filament\Widgets;

use App\Models\WorkOrderApproval;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class PendingApprovalsWidget extends Widget
{
    protected static string $view = 'filament.widgets.pending-approvals-widget';
    
    // PERUBAHAN: Ganti dari 'full' menjadi 1 (setengah lebar)
    protected int|string|array $columnSpan = 1;
    
    protected static bool $isLazy = false;
    
    // OPSIONAL: Atur urutan untuk memastikan posisi
    protected static ?int $sort = 5;

    public $items = [];

    public static function canView(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        
        /** @var \App\Models\User $user */
        return $user->isAdminOrOwner();
    }

    public function mount(): void
    {
        $this->reload();
    }

    #[On('refresh-approvals-widget')]
    public function reload(): void
    {
        $this->items = WorkOrderApproval::with([
                'workOrder:id,wo_number,status',
                'mechanic:id,name,employee_number,avatar',
            ])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->toArray();
    }

    public function approve(int $id): void
    {
        $appr = WorkOrderApproval::with('workOrder')->findOrFail($id);
        $appr->workOrder->approveRequest($appr, Auth::id(), null);
        $this->reload();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "WO #{$appr->workOrder->wo_number} approved.",
        ]);
    }

    public $rejectNote = '';

    public function reject(int $id, string $note): void
    {
        $note = trim($note);
        if ($note === '') {
            $this->dispatch('notify', [
                'type' => 'danger',
                'message' => 'Reject note is required.',
            ]);
            return;
        }

        $appr = WorkOrderApproval::with('workOrder')->findOrFail($id);
        $appr->workOrder->rejectRequest($appr, Auth::id(), $note);
        $this->reload();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "WO #{$appr->workOrder->wo_number} rejected.",
        ]);
    }
}