@extends('layouts.app')

@section('title', 'Mailbox | SignUpGo')

@section('styles')
    <style>
        .container {
            max-width: 1200px;
            width: 100%;
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            margin: 0 0 0.5rem 0;
            color: #2c3e50;
        }

        .page-header p {
            margin: 0;
            color: #7f8c8d;
        }

        .unread-badge {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .notification-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            border-left: 4px solid;
        }

        .notification-card.unread {
            background: #f0f9ff;
            border-left-color: #3498db;
        }

        .notification-card.read {
            border-left-color: #95a5a6;
        }

        .notification-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .notification-type {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .notification-icon {
            font-size: 1.5rem;
        }

        .notification-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .notification-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-read {
            background: #3498db;
            color: white;
        }

        .btn-read:hover {
            background: #2980b9;
        }

        .btn-delete {
            background: #e74c3c;
            color: white;
        }

        .btn-delete:hover {
            background: #c0392b;
        }

        .notification-body {
            color: #555;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .notification-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #7f8c8d;
            font-size: 0.85rem;
        }

        .notification-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-system {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-eo {
            background: #fff3cd;
            color: #856404;
        }

        .badge-urgent {
            background: #f8d7da;
            color: #721c24;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        .empty-state h3 {
            color: #7f8c8d;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #95a5a6;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination a,
        .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            color: #3498db;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }

        .pagination .active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }

        .filter-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e0e0e0;
        }

        .filter-tab {
            padding: 0.75rem 1.5rem;
            border: none;
            background: none;
            cursor: pointer;
            font-weight: 600;
            color: #7f8c8d;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .filter-tab:hover {
            color: #3498db;
        }

        .filter-tab.active {
            color: #3498db;
            border-bottom-color: #3498db;
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="page-header">
            <div>
                <h1>📧 Mailbox</h1>
                <p>View all your notifications and messages</p>
            </div>
            @if($unreadCount > 0)
                <span class="unread-badge">{{ $unreadCount }} Unread</span>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($notifications->count() > 0)
            @foreach ($notifications as $notification)
                @php
                    $isUnread = !$notification->is_read;
                    $typeIcon = '📩';
                    $typeBadge = 'badge-system';
                    
                    if ($notification->type === 'eo_notification') {
                        $typeIcon = '👤';
                        $typeBadge = 'badge-eo';
                    } elseif ($notification->priority === 'urgent') {
                        $typeIcon = '⚠️';
                        $typeBadge = 'badge-urgent';
                    }
                @endphp

                <div class="notification-card {{ $isUnread ? 'unread' : 'read' }}">
                    <div class="notification-header">
                        <div class="notification-type">
                            <span class="notification-icon">{{ $typeIcon }}</span>
                            <h3 class="notification-title">{{ $notification->title }}</h3>
                        </div>
                        <div class="notification-actions">
                            @if($isUnread)
                                <button class="btn btn-read" onclick="markAsRead({{ $notification->id }})">
                                    Mark as Read
                                </button>
                            @endif
                            <form action="{{ route('mailbox.destroy', $notification->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-delete" onclick="return confirm('Delete this notification?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="notification-body">
                        {{ $notification->message }}
                    </div>

                    <div class="notification-meta">
                        <span class="notification-date">
                            📅 {{ \Carbon\Carbon::parse($notification->created_at)->format('M d, Y h:i A') }}
                        </span>
                        <span class="badge {{ $typeBadge }}">
                            @if($notification->type === 'eo_notification')
                                Event Organizer
                            @elseif($notification->priority === 'urgent')
                                Urgent
                            @else
                                System Notification
                            @endif
                        </span>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            @if ($notifications->hasPages())
                <div class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($notifications->onFirstPage())
                        <span>&laquo; Previous</span>
                    @else
                        <a href="{{ $notifications->previousPageUrl() }}">&laquo; Previous</a>
                    @endif

                    {{-- Page Numbers --}}
                    @for ($i = 1; $i <= $notifications->lastPage(); $i++)
                        @if ($i == $notifications->currentPage())
                            <span class="active">{{ $i }}</span>
                        @else
                            <a href="{{ $notifications->url($i) }}">{{ $i }}</a>
                        @endif
                    @endfor

                    {{-- Next Page Link --}}
                    @if ($notifications->hasMorePages())
                        <a href="{{ $notifications->nextPageUrl() }}">Next &raquo;</a>
                    @else
                        <span>Next &raquo;</span>
                    @endif
                </div>
            @endif
        @else
            <div class="empty-state">
                <h3>📭 No Notifications</h3>
                <p>You don't have any notifications yet. Check back later!</p>
            </div>
        @endif
    </div>

    <script>
        function markAsRead(notificationId) {
            fetch(`/mailbox/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
@endsection
