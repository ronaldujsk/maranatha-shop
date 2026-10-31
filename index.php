
<?php

// Mostrar errores durante el desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Detectar automáticamente la ruta del proyecto
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));

// Conexión con la base de datos
require_once __DIR__ . '/config/database.php';

$database = new Database();
$connection = $database->connect();

$pageTitle = "Inicio | Maranatha Shop";

// CONSULTAR CATEGORÍAS
$sqlCategorias = "SELECT *
                  FROM categories
                  WHERE status = 'active'
                  ORDER BY name ASC";

$consultaCategorias = $connection->query($sqlCategorias);
$categorias = $consultaCategorias->fetchAll(PDO::FETCH_ASSOC);


// CONSULTAR PRODUCTOS
$sqlProductos = "SELECT
                    p.*,
                    c.name AS categoria,
                    i.quantity AS existencias
                 FROM products p
                 LEFT JOIN categories c
                    ON p.category_id = c.id
                 LEFT JOIN inventory i
                    ON p.id = i.product_id
                 WHERE p.status = 'active'
                 ORDER BY p.featured DESC, p.id DESC
                 LIMIT 8";

$consultaProductos = $connection->query($sqlProductos);
$productos = $consultaProductos->fetchAll(PDO::FETCH_ASSOC);


// CARGAR ENCABEZADO
require_once __DIR__ . '/includes/header.php';

?>

<!-- CONTENIDO PRINCIPAL -->
<main>

    <!-- BANNER PRINCIPAL -->
    <section class="hero-section">

        <div class="container">

            <h1>
                Todo lo que buscas,
                en un solo lugar.
            </h1>

            <p>
                Descubre nuestra variedad de productos
                para tu hogar, tecnología y vida diaria.
            </p>

            <a href="#productos" class="hero-button">
                Explorar productos
            </a>

        </div>

    </section>


    <!-- CATEGORÍAS -->
    <section class="store-section" id="categorias">

        <div class="container">

            <h2 class="section-title">
                Explora nuestras categorías
            </h2>

            <div class="row g-3">

                <?php if (count($categorias) > 0): ?>

                    <?php foreach ($categorias as $categoria): ?>

                        <div class="col-6 col-md-3">

                            <a
                                href="#productos"
                                class="category-card d-block text-decoration-none text-dark"
                            >

                                <div class="category-icon">
                                    <i class="bi bi-grid"></i>
                                </div>

                                <h5>
                                    <?= htmlspecialchars(
                                        $categoria['name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h5>

                            </a>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="col-12">
                        <p>No hay categorías disponibles.</p>
                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- PRODUCTOS -->
    <section class="store-section bg-light" id="productos">

        <div class="container">

            <h2 class="section-title">
                Nuestros productos
            </h2>

            <div class="row g-4">

                <?php if (count($productos) > 0): ?>

                    <?php foreach ($productos as $producto): ?>

                        <?php

                        $nombre = htmlspecialchars(
                            $producto['name'],
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        $descripcion = htmlspecialchars(
                            $producto['description'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        $categoriaNombre = htmlspecialchars(
                            $producto['categoria'] ?? 'General',
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        $precio = number_format(
                            (float) $producto['price'],
                            0,
                            ',',
                            '.'
                        );

                        $existencias = (int) ($producto['existencias'] ?? 0);

                        ?>

                        <!-- COLUMNA DEL PRODUCTO -->
                        <div class="col-6 col-md-4 col-lg-3">

                            <a
                                href="<?= BASE_URL ?>/pages/product.php?id=<?= (int) $producto['id'] ?>"
                                class="product-card-link"
                            >

                                <div class="product-card">

                                    <!-- IMAGEN -->
                                    <div class="product-image">
                                        🛍️
                                    </div>

                                    <!-- INFORMACIÓN -->
                                    <div class="product-info">

                                        <p class="product-description">
                                            <?= $categoriaNombre ?>
                                        </p>

                                        <h5>
                                            <?= $nombre ?>
                                        </h5>

                                        <p class="product-description">
                                            <?= $descripcion ?>
                                        </p>

                                        <p class="product-price">
                                            $<?= $precio ?> COP
                                        </p>

                                        <?php if ($producto['stock_control'] === 'tracked'): ?>

                                            <small class="text-muted">
                                                Existencias: <?= $existencias ?>
                                            </small>

                                        <?php else: ?>

                                            <small class="text-success">
                                                Disponibilidad flexible
                                            </small>

                                        <?php endif; ?>

                                        <div class="mt-3">
                                            <span class="btn btn-outline-dark btn-sm">
                                                Ver producto
                                            </span>
                                        </div>

                                    </div>

                                </div>

                            </a>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="col-12">

                        <p>
                            Todavía no hay productos disponibles.
                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- INFORMACIÓN DE LA TIENDA -->
    <section class="store-section">

        <div class="container text-center">

            <h2>Bienvenido a Maranatha Shop</h2>

            <p>
                Encuentra variedad, calidad y productos
                para tu vida diaria en un solo lugar.
            </p>

        </div>

    </section>

</main>

<?php

// CARGAR PIE DE PÁGINA
require_once __DIR__ . '/includes/footer.php';

?>
