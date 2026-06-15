<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'ref_id',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUnread(int $userId, int $limit = 20)
    {
        return $this->where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }

    public function getAllForUser(int $userId, int $limit = 20)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->paginate($limit);
    }

    public function countUnread(int $userId): int
    {
        return $this->where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function markAsRead(int $id, int $userId): bool
    {
        return $this->where('id', $id)
            ->where('user_id', $userId)
            ->update(['is_read' => true]) > 0;
    }

    public function markAllAsRead(int $userId): bool
    {
        return $this->where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]) > 0;
    }

    public static function notify(string $title, string $message, string $type, int $refId, ?array $roles = null): void
    {
        $userId = auth()->id();
        if (!$userId) return;

        $query = User::where('id', '!=', $userId);
        if ($roles !== null) {
            $query->whereIn('role', $roles);
        }
        $recipients = $query->get();

        if ($recipients->isEmpty()) return;

        $now = now();
        $batch = [];
        foreach ($recipients as $r) {
            $batch[] = [
                'user_id' => $r->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'ref_id' => $refId,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        self::insert($batch);
    }
}
