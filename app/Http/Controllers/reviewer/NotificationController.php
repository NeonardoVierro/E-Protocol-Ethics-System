<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $unreadCount = Auth::user()->notifications()
            ->where('status', Notification::STATUS_UNREAD)
            ->count();

        return view('reviewer.notifikasi.dashboard', compact('notifications', 'unreadCount'));
    }

    public function getLatest()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get();

        $unreadCount = Auth::user()->notifications()
            ->where('status', Notification::STATUS_UNREAD)
            ->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('status', Notification::STATUS_UNREAD)
            ->update([
                'status' => Notification::STATUS_READ,
                'read_at' => now(),
            ]);

        return redirect()->route('reviewer.notifikasi.index')
            ->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }

    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        return response()->json(['success' => true]);
    }

    public function clearRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('status', Notification::STATUS_READ)
            ->delete();

        return redirect()->route('reviewer.notifikasi.index')
            ->with('success', 'Notifikasi yang sudah dibaca telah dihapus.');
    }

    public function redirectFromNotification($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        if ($notification->status === Notification::STATUS_UNREAD) {
            $notification->markAsRead();
        }

        $data = $notification->data ?? [];

        switch ($notification->type) {
            case Notification::TYPE_REVIEW_ASSIGNMENT:
                if (isset($data['proposal_id'])) {
                    return redirect()->route('reviewer.review-proposal.show', $data['proposal_id']);
                }
                return redirect()->route('reviewer.proposal-masuk');

            case Notification::TYPE_PROPOSAL_STATUS:
                if (isset($data['proposal_id'])) {
                    return redirect()->route('reviewer.riwayat-review.show', $data['proposal_id']);
                }
                return redirect()->route('reviewer.riwayat-review');

            default:
                return redirect()->route('reviewer.dashboard');
        }
    }
}
