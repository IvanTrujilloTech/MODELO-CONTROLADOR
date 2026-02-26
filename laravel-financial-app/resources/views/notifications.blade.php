@extends('layout.header')

@section('title', 'Notificaciones')
@section('page_title', 'Notificaciones')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Notificaciones</h1>
            <button onclick="markAllAsRead()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Marcar todas como leídas
            </button>
        </div>
        
        @if($notifications->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <p class="mt-2 text-sm font-medium text-gray-900">No tienes notificaciones</p>
                <p class="mt-1 text-sm text-gray-500">Todas tus actividades se mostrarán aquí</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 {{ !$notification->read ? 'border-l-4 border-l-emerald-500' : '' }}">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-lg font-semibold text-gray-800">{{ $notification->type }}</p>
                                    <p class="text-sm text-gray-500">{{ $notification->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if(!$notification->read)
                                    <button onclick="markAsRead('{{ $notification->id }}')" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                                        Marcar como leída
                                    </button>
                                @endif
                                <button onclick="deleteNotification('{{ $notification->id }}')" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                        
                        <div class="text-gray-600">
                            @foreach($notification->data as $key => $value)
                                <div class="flex mb-2">
                                    <span class="font-medium mr-2">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                    <span>{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
    
    <script>
        function markAsRead(notificationId) {
            fetch(`{{ route('notifications.read', '') }}/${notificationId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
            .then(response => response.json())
            .then(data => {
                location.reload();
            });
        }
        
        function markAllAsRead() {
            fetch('{{ route('notifications.readAll') }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
            .then(response => response.json())
            .then(data => {
                location.reload();
            });
        }
        
        function deleteNotification(notificationId) {
            if (confirm('¿Estás seguro de que quieres eliminar esta notificación?')) {
                fetch(`{{ route('notifications.destroy', '') }}/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(response => response.json())
                .then(data => {
                    location.reload();
                });
            }
        }
    </script>
@endsection

@extends('layout.footer')
