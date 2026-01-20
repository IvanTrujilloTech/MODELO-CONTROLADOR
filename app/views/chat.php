<?php include 'layout/header.php'; // incluir el encabezado de la pagina ?>

<main class="container mx-auto py-12 px-4">
    <h2 class="text-3xl font-bold mb-8 text-gray-800">Chat y Transferencias</h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Chat Section -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Chat Global</h3>
            <div id="chat-messages" class="h-96 overflow-y-auto border border-gray-200 rounded p-4 mb-4 bg-gray-50">
                <?php foreach($messages as $msg): ?>
                <div class="mb-2">
                    <strong><?php echo htmlspecialchars($msg['sender_name']); ?>:</strong> <?php echo htmlspecialchars($msg['message']); ?>
                    <small class="text-gray-500"><?php echo $msg['timestamp']; ?></small>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="flex">
                <input type="text" id="message-input" class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Escribe un mensaje...">
                <button id="send-button" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-r-md">Enviar</button>
            </div>
        </div>

        <!-- Transfer Section -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Transferencia Bancaria</h3>
            <p class="text-gray-600 mb-4">Saldo actual: €<?php echo number_format($balance, 2); ?></p>
            <?php if(isset($_GET['error']) && $_GET['error'] == 'insufficient_balance'): ?>
            <p class="text-red-600 mb-4">Saldo insuficiente para realizar la transferencia.</p>
            <?php endif; ?>
            <?php if(isset($_GET['success']) && $_GET['success'] == 'transfer_completed'): ?>
            <p class="text-green-600 mb-4">Transferencia realizada con éxito.</p>
            <?php endif; ?>
            <form action="/transfer" method="post">
                <div class="mb-4">
                    <label for="recipient_id" class="block text-sm font-medium text-gray-700">Destinatario</label>
                    <select id="recipient_id" name="recipient_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500" required>
                        <option value="">Selecciona un destinatario</option>
                        <?php foreach($users as $user): ?>
                        <?php if($user['id'] != $_SESSION['user_id']): ?>
                        <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['nombre']); ?> (<?php echo htmlspecialchars($user['email']); ?>)</option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700">Monto (€)</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500" required>
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <input type="text" id="description" name="description" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500" required>
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded">Transferir</button>
            </form>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ws = new WebSocket('ws://localhost:8082');
    const chatMessages = document.getElementById('chat-messages');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');

    ws.onopen = function(event) {
        console.log('Connected to WebSocket');
    };

    ws.onmessage = function(event) {
        try {
            const data = JSON.parse(event.data);
            const messageDiv = document.createElement('div');
            messageDiv.className = 'mb-2';
            messageDiv.innerHTML = `<strong>${data.sender_name}:</strong> ${data.message} <small class="text-gray-500">${new Date().toLocaleString()}</small>`;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        } catch (e) {
            console.error('Error parsing message:', e);
        }
    };

    ws.onclose = function(event) {
        console.log('WebSocket connection closed');
    };

    ws.onerror = function(error) {
        console.error('WebSocket error:', error);
        alert('Error connecting to chat server. Please try again later.');
    };

    sendButton.addEventListener('click', function() {
        const message = messageInput.value.trim();
        if (message && ws.readyState === WebSocket.OPEN) {
            const data = {
                user_id: <?php echo $_SESSION['user_id']; ?>,
                sender_name: '<?php echo addslashes($_SESSION['user_name'] ?? 'Usuario'); ?>',
                message: message
            };
            ws.send(JSON.stringify(data));
            messageInput.value = '';
        } else if (ws.readyState !== WebSocket.OPEN) {
            alert('Connection to chat server is not available. Please refresh the page.');
        }
    });

    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendButton.click();
        }
    });
});
</script>

<?php include 'layout/footer.php'; ?></content>
</xai:function_call">The file MODELO-CONTROLADOR/app/views/chat.php has been created successfully.