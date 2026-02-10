<?php include __DIR__ . '/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- Botón de volver -->
            <a href="/posts" class="btn btn-outline-secondary mb-4">
                <i class="fas fa-arrow-left"></i> Volver a lista
            </a>

            <!-- Mensajes de éxito -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php if ($_GET['success'] === 'created'): ?>
                        Post creado con éxito!
                    <?php elseif ($_GET['success'] === 'updated'): ?>
                        Post actualizado con éxito!
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Post -->
            <article class="mb-8">
                <div class="mb-4">
                    <span class="badge badge-primary">
                        <?php echo htmlspecialchars($post->categoria); ?>
                    </span>
                    <span class="text-muted ml-2">
                        <?php echo date('d/m/Y', strtotime($post->created_at)); ?>
                    </span>
                </div>
                
                <h1 class="mb-4"><?php echo htmlspecialchars($post->titulo); ?></h1>
                
                <?php if (!empty($post->imagen)): ?>
                    <img src="<?php echo htmlspecialchars($post->imagen); ?>" 
                         class="img-fluid rounded mb-4" 
                         alt="<?php echo htmlspecialchars($post->titulo); ?>">
                <?php endif; ?>
                
                <div class="content">
                    <?php echo $post->contenido; ?>
                </div>
                
                <?php if (!empty($post->tags)): ?>
                    <div class="mt-4">
                        <strong>Etiquetas:</strong>
                        <?php foreach (explode(',', $post->tags) as $tag): ?>
                            <span class="badge badge-light mr-1">
                                <?php echo htmlspecialchars(trim($tag)); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
                    <div class="mt-4">
                        <a href="/posts/edit/<?php echo $post->id; ?>" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="/posts/delete/<?php echo $post->id; ?>" 
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Estás seguro de eliminar este post?')">
                            <i class="fas fa-trash"></i> Eliminar
                        </a>
                    </div>
                <?php endif; ?>
            </article>

            <!-- Posts relacionados -->
            <?php if (!empty($relatedPosts)): ?>
                <hr>
                <h3 class="mb-4">Posts Relacionados</h3>
                <div class="row">
                    <?php foreach ($relatedPosts as $related): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <?php if (!empty($related['imagen'])): ?>
                                    <img src="<?php echo htmlspecialchars($related['imagen']); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($related['titulo']); ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <span class="badge badge-primary mb-2">
                                        <?php echo htmlspecialchars($related['categoria']); ?>
                                    </span>
                                    <h5 class="card-title">
                                        <a href="/posts/<?php echo $related['id']; ?>" 
                                           class="text-dark">
                                            <?php echo htmlspecialchars($related['titulo']); ?>
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <?php echo htmlspecialchars($related['resumen'] ?? substr($related['contenido'], 0, 100) . '...'); ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y', strtotime($related['created_at'])); ?>
                                        </small>
                                        <a href="/posts/<?php echo $related['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            Leer más
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>