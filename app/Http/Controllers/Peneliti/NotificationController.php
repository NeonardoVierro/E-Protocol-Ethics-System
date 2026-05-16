<?php

namespace App\Http\Controllers\Peneliti;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Constructor - pastikan user sudah login
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Halaman daftar notifikasi
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $unreadCount = Auth::user()->notifications()
            ->where('status', Notification::STATUS_UNREAD)
            ->count();
        
        return view('peneliti.notifikasi.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mendapatkan notifikasi terbaru (untuk dropdown)
     */
    public function getLatest()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $unreadCount = Auth::user()->notifications()
            ->where('status', Notification::STATUS_UNREAD)
            ->count();
        
        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('status', Notification::STATUS_UNREAD)
            ->update([
                'status' => Notification::STATUS_READ,
                'read_at' => now()
            ]);
        
        return redirect()->route('peneliti.notifikasi.index')
            ->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }

    /**
     * Hapus notifikasi
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        $notification->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * Hapus semua notifikasi yang sudah dibaca
     */
    public function clearRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('status', Notification::STATUS_READ)
            ->delete();
        
        return redirect()->route('peneliti.notifikasi.index')
            ->with('success', 'Notifikasi yang sudah dibaca telah dihapus.');
    }

    /**
     * Redirect berdasarkan tipe notifikasi
     */
    public function redirectFromNotification($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        // Tandai sebagai sudah dibaca
        if ($notification->status === Notification::STATUS_UNREAD) {
            $notification->markAsRead();
        }
        
        // Redirect berdasarkan tipe
        $data = $notification->data ?? [];
        
        switch ($notification->type) {
            case Notification::TYPE_ACCOUNT_ACTIVATION:
                return redirect()->route('peneliti.dashboard');
            
            case Notification::TYPE_PROPOSAL_STATUS:
            case Notification::TYPE_REVISION_REQUEST:
                if (isset($data['proposal_id'])) {
                    return redirect()->route('pengajuan.riwayat-pengajuan')
                        ->with('highlight_proposal', $data['proposal_id']);
                }
                return redirect()->route('pengajuan.riwayat-pengajuan');
            
            case Notification::TYPE_REVIEW_ASSIGNMENT:
                return redirect()->route('reviewer.proposal-masuk');
            
            case Notification::TYPE_DOCUMENT_READY:
                if (isset($data['ethics_document_id'])) {
                    return redirect()->route('peneliti.dashboard');
                }
                return redirect()->route('pengajuan.riwayat-pengajuan');
            
            default:
                return redirect()->route('peneliti.dashboard');
        }
    }
}