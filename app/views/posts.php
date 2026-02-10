<?php include __DIR__ . '/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Blog Financiero</h1>
            
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
                <div class="mb-3">
                    <a href="/posts/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Post
                    </a>
                </div>
            <?php endif; ?>

            <!-- Mensajes de éxito/error -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php if ($_GET['success'] === 'created'): ?>
                        Post creado con éxito!
                    <?php elseif ($_GET['success'] === 'updated'): ?>
                        Post actualizado con éxito!
                    <?php elseif ($_GET['success'] === 'deleted'): ?>
                        Post eliminado con éxito!
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?php if ($_GET['error'] === 'not_found'): ?>
                        Post no encontrado
                    <?php elseif ($_GET['error'] === 'delete'): ?>
                        Error al eliminar el post
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Lista de posts -->
            <div class="row">
                <?php while ($post = $posts->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if (!empty($post['imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($post['imagen']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($post['titulo']); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <span class="badge badge-primary mb-2">
                                    <?php echo htmlspecialchars($post['categoria']); ?>
                                </span>
                                <h5 class="card-title">
                                    <a href="/posts/<?php echo $post['id']; ?>" 
                                       class="text-dark">
                                        <?php echo htmlspecialchars($post['titulo']); ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted">
                                    <?php echo htmlspecialchars($post['resumen'] ?? substr($post['contenido'], 0, 150) . '...'); ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                                    </small>
                                    <a href="/posts/<?php echo $post['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        Leer más
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>