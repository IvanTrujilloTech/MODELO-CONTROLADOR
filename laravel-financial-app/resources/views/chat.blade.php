@extends('layout.header')

@section('title', 'Chat')
@section('page_title', 'Chat')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex h-96 bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Sidebar with user list -->
            <div class="w-64 bg-gray-50 border-r border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Chat</h3>
                </div>
                
                <div class="p-4 overflow-y-auto">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Usuarios</h4>
                    <div class="space-y-2">
                        @foreach($users as $user)
                            <div class="flex items-center p-2 rounded-lg hover:bg-gray-100 cursor-pointer user-item" data-user-id="{{ $user->id }}">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-700">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Chat messages -->
            <div class="flex-1 flex flex-col">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800" id="chat-title">Selecciona un usuario para chatear</h3>
                </div>
                
                <div class="flex-1 p-4 overflow-y-auto" id="chat-messages">
                    <div class="text-center text-gray-500 mt-20">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p class="mt-2 text-sm font-medium text-gray-900">Selecciona un usuario del lado izquierdo</p>
                        <p class="mt-1 text-sm text-gray-500">para iniciar una conversación</p>
                    </div>
                </div>
                
                <div class="p-4 border-t border-gray-200" id="chat-input-container" style="display: none;">
                    <form id="chat-form" class="flex">
                        @csrf
                        <input type="hidden" id="receiver_id" name="receiver_id">
                        <input type="text" id="message-input" name="message" placeholder="Escribe un mensaje..." required class="flex-1 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <button type="submit" class="ml-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Enviar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userItems = document.querySelectorAll('.user-item');
            const chatMessages = document.getElementById('chat-messages');
            const chatInputContainer = document.getElementById('chat-input-container');
            const chatForm = document.getElementById('chat-form');
            const messageInput = document.getElementById('message-input');
            const receiverIdInput = document.getElementById('receiver_id');
            const chatTitle = document.getElementById('chat-title');
            
            // Select user to chat with
            userItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const userName = this.querySelector('p:first-child').textContent;
                    
                    // Remove active class from all items
                    userItems.forEach(function(i) {
                        i.classList.remove('bg-gray-100');
                    });
                    
                    // Add active class to clicked item
                    this.classList.add('bg-gray-100');
                    
                    // Set receiver ID and chat title
                    receiverIdInput.value = userId;
                    chatTitle.textContent = 'Chat con ' + userName;
                    
                    // Show chat input
                    chatInputContainer.style.display = 'block';
                    
                    // Load messages
                    loadMessages(userId);
                });
            });
            
            // Send message
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const receiverId = receiverIdInput.value;
                const message = messageInput.value;
                
                fetch('{{ route('chat.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        receiver_id: receiverId,
                        message: message
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageInput.value = '';
                        loadMessages(receiverId);
                    }
                });
            });
            
            // Load messages
            function loadMessages(receiverId) {
                fetch('{{ route('chat.messages', '') }}/' + receiverId)
                .then(response => response.json())
                .then(data => {
                    displayMessages(data.messages);
                });
            }
            
            // Display messages
            function displayMessages(messages) {
                chatMessages.innerHTML = '';
                
                messages.forEach(function(message) {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'flex mb-4 ' + (message.sender_id === {{ Auth::id() }} ? 'justify-end' : 'justify-start');
                    
                    const messageContent = document.createElement('div');
                    messageContent.className = 'max-w-xs p-3 rounded-lg ' + (message.sender_id === {{ Auth::id() }} ? 'bg-emerald-100 text-emerald-900' : 'bg-gray-100 text-gray-900');
                    
                    const messageText = document.createElement('p');
                    messageText.className = 'text-sm';
                    messageText.textContent = message.message;
                    
                    const messageTime = document.createElement('p');
                    messageTime.className = 'text-xs text-gray-500 mt-1 text-right';
                    messageTime.textContent = new Date(message.timestamp).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                    
                    messageContent.appendChild(messageText);
                    messageContent.appendChild(messageTime);
                    messageDiv.appendChild(messageContent);
                    
                    chatMessages.appendChild(messageDiv);
                });
                
                // Scroll to bottom
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
            
            // Auto-reload messages every 5 seconds
            setInterval(function() {
                const receiverId = receiverIdInput.value;
                if (receiverId) {
                    loadMessages(receiverId);
                }
            }, 5000);
        });
    </script>
@endsection

@extends('layout.footer')
