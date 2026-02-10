<?php include __DIR__ . '/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Buscar Posts</h1>
            
            <!-- Botón de volver -->
            <a href="/posts" class="btn btn-outline-secondary mb-4">
                <i class="fas fa-arrow-left"></i> Volver a lista
            </a>

            <!-- Formulario de búsqueda -->
            <form method="GET" action="/posts/search" class="mb-4">
                <div class="input-group">
                    <input type="text" 
                           name="q" 
                           class="form-control" 
                           placeholder="Buscar posts por título, contenido o etiquetas..."
                           value="<?php echo htmlspecialchars($query); ?>">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>

            <!-- Resultados de búsqueda -->
            <?php if (!empty($resultados)): ?>
                <h2 class="mb-3">Resultados de Búsqueda</h2>
                <div class="row">
                    <?php while ($post = $resultados->fetch(PDO::FETCH_ASSOC)): ?>
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
            <?php elseif (!empty($query)): ?>
                <div class="alert alert-info">
                    No se encontraron posts que coincidan con tu búsqueda.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>