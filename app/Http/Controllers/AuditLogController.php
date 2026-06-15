<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $action = $request->get('action');
        $tableName = $request->get('table_name');
        $tanggalAwal = $this->normalizeDate($request->get('tanggal_awal'));
        $tanggalAkhir = $this->normalizeDate($request->get('tanggal_akhir'));
        $userId = $request->get('user_id');
        $search = $request->get('search');

        $query = AuditLog::query()
            ->select('audit_log.*', 'users.nama as user_nama')
            ->leftJoin('users', 'users.id', '=', 'audit_log.user_id');

        if (!empty($action)) {
            $query->where('audit_log.action', $action);
        }
        if (!empty($tableName)) {
            $query->where('audit_log.table_name', $tableName);
        }
        if ($tanggalAwal) {
            $query->where('audit_log.created_at', '>=', $tanggalAwal . ' 00:00:00');
        }
        if ($tanggalAkhir) {
            $query->where('audit_log.created_at', '<=', $tanggalAkhir . ' 23:59:59');
        }
        if (!empty($userId)) {
            $query->where('audit_log.user_id', $userId);
        }
        if (!empty($search)) {
            $query->where('audit_log.description', 'like', "%{$search}%");
        }

        $data = $query
            ->orderBy('audit_log.created_at', 'DESC')
            ->orderBy('audit_log.id', 'DESC')
            ->paginate(25);

        $actions = AuditLog::select('action')->distinct()->pluck('action');
        $tables = AuditLog::select('table_name')->distinct()->pluck('table_name');
        $users = AuditLog::query()
            ->select('audit_log.user_id', 'users.nama')
            ->leftJoin('users', 'users.id', '=', 'audit_log.user_id')
            ->distinct()
            ->get();

        return view('pengaturan.audit-log.index', [
            'title' => 'Audit Log',
            'data' => $data,
            'actions' => $actions,
            'tables' => $tables,
            'users' => $users,
            'action' => $action,
            'tableName' => $tableName,
            'tanggalAwal' => $tanggalAwal ? date('d/m/Y', strtotime($tanggalAwal)) : '',
            'tanggalAkhir' => $tanggalAkhir ? date('d/m/Y', strtotime($tanggalAkhir)) : '',
            'userId' => $userId,
            'search' => $search,
        ]);
    }

    public function detail(AuditLog $auditLog)
    {
        $log = AuditLog::query()
            ->select('audit_log.*', 'users.nama as user_nama')
            ->leftJoin('users', 'users.id', '=', 'audit_log.user_id')
            ->where('audit_log.id', $auditLog->id)
            ->first();

        if (!$log) {
            return redirect()->route('pengaturan.auditlog.index')->with('error', 'Log tidak ditemukan');
        }

        return view('pengaturan.audit-log.detail', [
            'title' => 'Detail Audit Log',
            'log' => $log,
        ]);
    }

    private function normalizeDate(?string $value): ?string
    {
        if (!$value) {
            return null;
        }
        $value = trim($value);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
            return $value;
        }
        $dt = \DateTime::createFromFormat('d/m/Y', $value);
        if ($dt instanceof \DateTime) {
            return $dt->format('Y-m-d');
        }
        return null;
    }
}
