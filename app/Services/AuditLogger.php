<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    public function record(?User $actor, string $action, ?Model $subject = null, array $metadata = []): AuditLog
    {
        $safe = $this->sanitize($metadata);

        return AuditLog::query()->create([
            'actor_id' => $actor?->id,
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'metadata' => $safe ?: null,
            'ip_address' => request()?->ip(),
        ]);
    }

    private function sanitize(array $metadata): array
    {
        $blocked = ['password', 'password_confirmation', 'token', 'remember_token', 'secret'];

        return collect($metadata)
            ->reject(fn ($value, $key) => in_array(strtolower((string) $key), $blocked, true))
            ->toArray();
    }
}
