<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\BarangMasukDetail;
use App\Models\BarangKeluarDetail;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function list(Request $request)
    {
        $userId = auth()->id();
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get();

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function all(Request $request)
    {
        $userId = auth()->id();
        $keyword = $request->get('cari');

        $query = Notification::where('user_id', $userId);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('message', 'like', "%{$keyword}%");
            });
        }

        $notifications = $query
            ->orderBy('created_at', 'DESC')
            ->paginate(25);

        // Batch lookup barang_id for BM & BK notifications (avoid N+1)
        $bmIds = [];
        $bkIds = [];
        foreach ($notifications as $n) {
            if ($n->type === 'barang_masuk') $bmIds[] = $n->ref_id;
            elseif ($n->type === 'barang_keluar') $bkIds[] = $n->ref_id;
        }

        $bmMap = [];
        if (!empty($bmIds)) {
            $bmDetails = BarangMasukDetail::whereIn('barang_masuk_id', $bmIds)->get();
            foreach ($bmDetails as $d) {
                $bmMap[$d->barang_masuk_id] = $d->barang_id;
            }
        }

        $bkMap = [];
        if (!empty($bkIds)) {
            $bkDetails = BarangKeluarDetail::whereIn('barang_keluar_id', $bkIds)->get();
            foreach ($bkDetails as $d) {
                $bkMap[$d->barang_keluar_id] = $d->barang_id;
            }
        }

        $barangIds = [];
        foreach ($notifications as $n) {
            if ($n->type === 'barang_masuk') {
                $barangIds[$n->id] = $bmMap[$n->ref_id] ?? null;
            } elseif ($n->type === 'barang_keluar') {
                $barangIds[$n->id] = $bkMap[$n->ref_id] ?? null;
            } else {
                $barangIds[$n->id] = null;
            }
        }

        return view('notifikasi.all', [
            'title' => 'Notifikasi',
            'notifications' => $notifications,
            'cari' => $keyword,
            'barangIds' => $barangIds,
        ]);
    }

    public function unreadCount()
    {
        $userId = auth()->id();
        $count = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    public function markRead(Notification $notification)
    {
        $userId = auth()->id();

        $result = Notification::where('id', $notification->id)
            ->where('user_id', $userId)
            ->update(['is_read' => true]) > 0;

        return response()->json([
            'success' => $result,
        ]);
    }

    public function markAllRead()
    {
        $userId = auth()->id();

        $result = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]) > 0;

        return response()->json([
            'success' => $result,
        ]);
    }
}
