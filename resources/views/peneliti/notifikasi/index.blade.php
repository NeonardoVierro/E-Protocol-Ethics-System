@extends('layouts.dashboard')

@section('title', 'Notifikasi')

@section('content')
<div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-2xl">notifications</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-primary">Notifikasi</h1>
                <p class="text-sm text-on-surface-variant">Semua pemberitahuan dan informasi terbaru untuk Anda</p>
            </div>
        </div>
        <div class="flex gap-3">
            @if($unreadCount > 0)
                <form action="{{ route('peneliti.notifikasi.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-primary border border-primary rounded-lg hover:bg-primary/5 transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">done_all</span>
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
            <form action="{{ route('peneliti.notifikasi.clear-read') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 text-on-surface-variant border border-outline-variant rounded-lg hover:bg-surface-container-low transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">delete_sweep</span>
                    Hapus yang Dibaca
                </button>
            </form>
        </div>
    </div>

    <!-- Notifikasi List -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
        @if($notifications->count() > 0)
            <div class="divide-y divide-outline-variant">
                @foreach($notifications as $notification)
                    @php
                        $icon = match($notification->type) {
                            'account_activation' => 'check_circle',
                            'review_assignment' => 'assignment',
                            'proposal_status' => 'article',
                            'document_ready' => 'description',
                            'revision_request' => 'edit_note',
                            default => 'notifications'
                        };
                        $iconColor = match($notification->type) {
                            'account_activation' => 'text-emerald-600 bg-emerald-50',
                            'review_assignment' => 'text-blue-600 bg-blue-50',
                            'proposal_status' => 'text-primary bg-primary/10',
                            'document_ready' => 'text-secondary bg-secondary/10',
                            'revision_request' => 'text-amber-600 bg-amber-50',
                            default => 'text-on-surface-variant bg-surface-container-low'
                        };
                        $isUnread = $notification->status === 'unread';
                    @endphp
                    
                    <a href="{{ route('peneliti.notifikasi.redirect', $notification->id) }}" 
                       class="block hover:bg-surface-container-low transition-colors {{ $isUnread ? 'bg-surface-container-low' : '' }}">
                        <div class="p-4 sm:p-5">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full {{ $iconColor }} flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-lg">{{ $icon }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-1">
                                        <h3 class="font-semibold text-on-surface {{ $isUnread ? 'text-primary' : '' }}">
                                            {{ $notification->title }}
                                            @if($isUnread)
                                                <span class="ml-2 inline-block w-2 h-2 rounded-full bg-primary"></span>
                                            @endif
                                        </h3>
                                        <span class="text-xs text-on-surface-variant whitespace-nowrap">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-on-surface-variant">{{ $notification->message }}</p>
                                    <div class="flex items-center gap-3 mt-2">
                                        <button onclick="event.preventDefault(); markAsRead('{{ $notification->id }}')" 
                                                class="text-xs text-primary hover:underline">
                                            Tandai sudah dibaca
                                        </button>
                                        <button onclick="event.preventDefault(); deleteNotification('{{ $notification->id }}')" 
                                                class="text-xs text-error hover:underline">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="border-t border-outline-variant px-6 py-4">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 mx-auto bg-surface-container-low rounded-full flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-outline text-4xl">notifications_none</span>
                </div>
                <h3 class="text-lg font-semibold text-primary mb-2">Tidak Ada Notifikasi</h3>
                <p class="text-on-surface-variant">Belum ada notifikasi baru untuk Anda.</p>
            </div>
        @endif
    </div>
</div>

<script>
    // Mark single notification as read
    function markAsRead(id) {
        fetch(`/peneliti/notifikasi/mark-read/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
    
    // Delete notification
    function deleteNotification(id) {
        if (confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')) {
            fetch(`/peneliti/notifikasi/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }
</script>
@endsection