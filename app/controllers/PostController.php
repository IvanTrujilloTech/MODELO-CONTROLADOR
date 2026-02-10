<?php
// Controlador para gestión de posts del blog
// Incluye funcionalidades CRUD y integración con n8n
class PostController {
    private $db;
    private $post;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->post = new Post($this->db);
    }

    // Mostrar lista de posts
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $posts = $this->post->getAll($limit, $offset);
        require_once __DIR__ . '/../views/posts.php';
    }

    // Mostrar detalle de un post y sugerencias relacionadas
    public function show($id) {
        $id = Security::sanitize_int($id);
        
        if (!$this->post->getById($id)) {
            header("Location: /posts?error=not_found");
            exit;
        }

        $keywords = $this->post->extractKeywords($this->post->contenido);
        $relatedPosts = $this->post->findRelatedPosts($id, $keywords);
        
        require_once __DIR__ . '/../views/post_detail.php';
    }

    // Mostrar formulario para crear post (solo admin)
    public function create() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header("Location: /login");
            exit;
        }

        require_once __DIR__ . '/../views/create_post.php';
    }

    // Guardar nuevo post y enviar webhook a n8n
    public function store() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /posts/create");
            exit;
        }

        // Validar CSRF token
        if (!Security::validate_csrf_token($_POST['csrf_token'] ?? null)) {
            header("Location: /posts/create?error=csrf");
            exit;
        }

        // Validar datos
        $errors = $this->validatePostData($_POST);
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: /posts/create");
            exit;
        }

        // Guardar post
        $this->post->titulo = $_POST['titulo'];
        $this->post->contenido = $_POST['contenido'];
        $this->post->resumen = $_POST['resumen'] ?? '';
        $this->post->categoria = $_POST['categoria'];
        $this->post->tags = $_POST['tags'] ?? '';
        $this->post->imagen = $_POST['imagen'] ?? '';
        $this->post->autor_id = $_SESSION['user_id'];

        if ($this->post->create()) {
            // Enviar notificación a n8n
            $this->sendNewPostNotification();
            header("Location: /posts/" . $this->post->id . "?success=created");
            exit;
        } else {
            $_SESSION['errors'] = ['Error al guardar el post'];
            header("Location: /posts/create");
            exit;
        }
    }

    // Mostrar formulario para editar post (solo admin)
    public function edit($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header("Location: /login");
            exit;
        }

        $id = Security::sanitize_int($id);
        
        if (!$this->post->getById($id)) {
            header("Location: /posts?error=not_found");
            exit;
        }

        require_once __DIR__ . '/../views/edit_post.php';
    }

    // Actualizar post
    public function update($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /posts/edit/" . $id);
            exit;
        }

        // Validar CSRF token
        if (!Security::validate_csrf_token($_POST['csrf_token'] ?? null)) {
            header("Location: /posts/edit/" . $id . "?error=csrf");
            exit;
        }

        $id = Security::sanitize_int($id);
        $this->post->id = $id;

        // Validar datos
        $errors = $this->validatePostData($_POST);
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: /posts/edit/" . $id);
            exit;
        }

        // Actualizar post
        $this->post->titulo = $_POST['titulo'];
        $this->post->contenido = $_POST['contenido'];
        $this->post->resumen = $_POST['resumen'] ?? '';
        $this->post->categoria = $_POST['categoria'];
        $this->post->tags = $_POST['tags'] ?? '';
        $this->post->imagen = $_POST['imagen'] ?? '';

        if ($this->post->update()) {
            header("Location: /posts/" . $id . "?success=updated");
            exit;
        } else {
            $_SESSION['errors'] = ['Error al actualizar el post'];
            header("Location: /posts/edit/" . $id);
            exit;
        }
    }

    // Eliminar post
    public function delete($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header("Location: /login");
            exit;
        }

        $id = Security::sanitize_int($id);
        $this->post->id = $id;

        if ($this->post->delete()) {
            header("Location: /posts?success=deleted");
            exit;
        } else {
            header("Location: /posts?error=delete");
            exit;
        }
    }

    // Buscar posts
    public function search() {
        $query = isset($_GET['q']) ? Security::sanitize_string($_GET['q']) : '';
        $resultados = [];

        if (!empty($query)) {
            $resultados = $this->post->search($query);
        }

        require_once __DIR__ . '/../views/search_posts.php';
    }

    // Validar datos del post
    private function validatePostData($data) {
        $errors = [];

        if (empty($data['titulo']) || strlen($data['titulo']) < 5 || strlen($data['titulo']) > 255) {
            $errors[] = 'El título debe tener entre 5 y 255 caracteres';
        }

        if (empty($data['contenido']) || strlen($data['contenido']) < 50) {
            $errors[] = 'El contenido debe tener al menos 50 caracteres';
        }

        if (empty($data['categoria']) || !in_array($data['categoria'], ['Finanzas', 'Inversiones', 'Ahorro', 'Educación', 'Tecnología'])) {
            $errors[] = 'Categoría inválida';
        }

        if (!empty($data['imagen']) && !Security::validate_url($data['imagen'])) {
            $errors[] = 'URL de imagen inválida';
        }

        return $errors;
    }

    // Enviar notificación de nuevo post a n8n
    private function sendNewPostNotification() {
        $postData = [
            'titulo' => $this->post->titulo,
            'resumen' => $this->post->resumen,
            'categoria' => $this->post->categoria,
            'imagen' => $this->post->imagen,
            'autor_id' => $this->post->autor_id,
            'url' => $_SERVER['HTTP_HOST'] . "/posts/" . $this->post->id
        ];

        $webhookUrl = getenv('N8N_WEBHOOK_URL') ?: 'https://ivantrubar.app.n8n.cloud/webhook-test/ff373657-1ce7-4512-9329-1b534d87c759';
        
        if ($webhookUrl) {
            $payload = [
                'event' => 'new_post_published',
                'timestamp' => date('c'),
                'data' => $postData,
                'metadata' => [
                    'source' => 'blog_system',
                    'version' => '1.0'
                ]
            ];

            $ch = curl_init($webhookUrl);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'X-Webhook-Source: blog-system'
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_error($ch)) {
                error_log("Webhook error: " . curl_error($ch));
            } elseif ($httpCode >= 400) {
                error_log("Webhook error: " . $httpCode . " - " . $response);
            }

            curl_close($ch);
        }
    }
}
?>