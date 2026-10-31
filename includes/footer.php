<?php
// Definir la ruta base del proyecto si todavía no existe
if (!defined('BASE_URL')) {
    $directorioActual = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

    // Si estamos dentro de pages o admin, subir a la carpeta principal
    if (basename($directorioActual) === 'pages' || basename($directorioActual) === 'admin') {
        $directorioActual = dirname($directorioActual);
    }

    define('BASE_URL', rtrim($directorioActual, '/'));
}
?>

<footer class="main-footer" id="contacto">
    <div class="container">
        <div class="row g-4">

            <div class="col-md-5">
                <h3 class="footer-brand">
                    MARANATHA<span>SHOP</span>
                </h3>

                <p>
                    Encuentra productos para tu día a día.
                    Calidad, variedad y una experiencia de compra sencilla.
                </p>
            </div>

            <div class="col-md-3">
                <h5>Información</h5>

                <p>
                    <a href="<?= BASE_URL ?>/index.php">Inicio</a>
                </p>

                <p>
                    <a href="<?= BASE_URL ?>/index.php#categorias">Categorías</a>
                </p>

                <p>
                    <a href="<?= BASE_URL ?>/index.php#productos">Productos</a>
                </p>

                <p>
                    <a href="<?= BASE_URL ?>/index.php#contacto">Contacto</a>
                </p>
            </div>

            <div class="col-md-4">
                <h5>Atención al cliente</h5>
                <p>Contáctanos para recibir asesoría.</p>
                <p>Colombia</p>
            </div>

        </div>

        <hr>

        <p class="copyright">
            © <?= date('Y') ?> Maranatha Shop. Todos los derechos reservados.
        </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>