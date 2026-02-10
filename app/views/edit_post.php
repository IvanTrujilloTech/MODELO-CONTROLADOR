<?php include __DIR__ . '/layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Editar Post</h1>
            
            <!-- Botón de volver -->
            <a href="/posts/<?php echo $post->id; ?>" class="btn btn-outline-secondary mb-4">
                <i class="fas fa-arrow-left"></i> Volver al post
            </a>

            <!-- Errores de validación -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?php if ($_GET['error'] === 'csrf'): ?>
                        Token de seguridad inválido. Por favor, intente nuevamente.
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['errors'])): ?>
                <div class="alert alert-danger">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <div><?php echo $error; ?></div>
                    <?php endforeach; ?>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <!-- Formulario de edición -->
            <form method="POST" action="/posts/update/<?php echo $post->id; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generate_csrf_token(); ?>">
                
                <div class="form-group">
                    <label for="titulo">Título</label>
                    <input type="text" 
                           id="titulo" 
                           name="titulo" 
                           class="form-control" 
                           required 
                           maxlength="255"
                           value="<?php echo htmlspecialchars($post->titulo); ?>">
                </div>

                <div class="form-group">
                    <label for="categoria">Categoría</label>
                    <select id="categoria" 
                            name="categoria" 
                            class="form-control" 
                            required>
                        <option value="">Seleccionar categoría</option>
                        <option value="Finanzas" <?php echo ($post->categoria === 'Finanzas') ? 'selected' : ''; ?>>Finanzas</option>
                        <option value="Inversiones" <?php echo ($post->categoria === 'Inversiones') ? 'selected' : ''; ?>>Inversiones</option>
                        <option value="Ahorro" <?php echo ($post->categoria === 'Ahorro') ? 'selected' : ''; ?>>Ahorro</option>
                        <option value="Educación" <?php echo ($post->categoria === 'Educación') ? 'selected' : ''; ?>>Educación</option>
                        <option value="Tecnología" <?php echo ($post->categoria === 'Tecnología') ? 'selected' : ''; ?>>Tecnología</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="resumen">Resumen</label>
                    <textarea id="resumen" 
                              name="resumen" 
                              class="form-control" 
                              rows="3"
                              maxlength="500"><?php echo htmlspecialchars($post->resumen); ?></textarea>
                    <small class="text-muted">Breve resumen del post (opcional, máximo 500 caracteres)</small>
                </div>

                <div class="form-group">
                    <label for="imagen">URL de Imagen</label>
                    <input type="url" 
                           id="imagen" 
                           name="imagen" 
                           class="form-control"
                           value="<?php echo htmlspecialchars($post->imagen); ?>">
                    <small class="text-muted">URL de la imagen principal (opcional)</small>
                </div>

                <div class="form-group">
                    <label for="tags">Etiquetas</label>
                    <input type="text" 
                           id="tags" 
                           name="tags" 
                           class="form-control"
                           placeholder="Separadas por comas (ej: inversión,bolsa,ahorro)"
                           value="<?php echo htmlspecialchars($post->tags); ?>">
                </div>

                <div class="form-group">
                    <label for="contenido">Contenido</label>
                    <textarea id="contenido" 
                              name="contenido" 
                              class="form-control" 
                              rows="10"
                              required><?php echo htmlspecialchars($post->contenido); ?></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <a href="/posts/<?php echo $post->id; ?>" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>