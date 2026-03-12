<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AdminLog;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class AdminAuditService
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function record(User|string|null $admin, string $action, ?string $targetType = null, ?string $targetId = null, array $metadata = []): void
    {
        if (! Schema::hasTable('admin_logs')) {
            return;
        }

        AdminLog::query()->create([
            'admin_id' => $admin instanceof User ? $admin->id : $admin,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'metadata' => $metadata,
        ]);
    }
}
