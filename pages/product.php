<?php

// Cargar la conexión a la base de datos
require_once __DIR__ . '/../config/database.php';

// Definir la ruta base del proyecto si todavía no existe
if (!defined('BASE_URL')) {
    $directorioActual = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

    if (basename($directorioActual) === 'pages') {
        $directorioActual = dirname($directorioActual);
    }

    define('BASE_URL', rtrim($directorioActual, '/'));
}

// Crear la conexión
$database = new Database();
$connection = $database->connect();

// Obtener y validar el identificador del producto
$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$productId || $productId < 1) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// Consultar el producto, su categoría y sus existencias
$sql = "SELECT
            p.*,
            c.name AS categoria,
            i.quantity AS existencias
        FROM products p
        LEFT JOIN categories c
            ON p.category_id = c.id
        LEFT JOIN inventory i
            ON p.id = i.product_id
        WHERE p.id = :id
          AND p.status = 'active'
        LIMIT 1";

$statement = $connection->prepare($sql);
$statement->bindValue(':id', $productId, PDO::PARAM_INT);
$statement->execute();

$producto = $statement->fetch(PDO::FETCH_ASSOC);

// Si el producto no existe o no está activo, regresar al catálogo
if (!$producto) {
    header('Location: ' . BASE_URL . '/index.php#productos');
    exit;
}

// Preparar el título de la página
$pageTitle = $producto['name'] . ' | Maranatha Shop';

// Preparar los datos del inventario
$controlInventario = $producto['stock_control'] ?? 'tracked';
$existencias = (int) ($producto['existencias'] ?? 0);

$disponible = (
    $controlInventario === 'untracked'
    || $existencias > 0
);

// Cargar el encabezado
require_once __DIR__ . '/../includes/header.php';

?>

<main class="container product-detail">

    <!-- Ruta de navegación -->
    <div class="product-breadcrumb">
        <nav class="breadcrumb-custom" aria-label="Ruta de navegación">
            <a href="<?= BASE_URL ?>/index.php">Inicio</a>

            <i class="bi bi-chevron-right"></i>

            <a href="<?= BASE_URL ?>/index.php#productos">Productos</a>

            <i class="bi bi-chevron-right"></i>

            <span>
                <?= htmlspecialchars(
                    $producto['name'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>
        </nav>
    </div>

    <div class="row g-5">

        <!-- Imagen del producto -->
        <div class="col-md-6">
            <div class="product-detail-image">
                🛍️
            </div>
        </div>

        <!-- Información del producto -->
        <div class="col-md-6">

            <p class="detail-category">
                <?= htmlspecialchars(
                    $producto['categoria'] ?? 'General',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <h1 class="detail-title">
                <?= htmlspecialchars(
                    $producto['name'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </h1>

            <p class="detail-price">
                $<?= number_format(
                    (float) $producto['price'],
                    0,
                    ',',
                    '.'
                ) ?>
                COP
            </p>

            <div class="detail-divider"></div>

            <h5>Descripción del producto</h5>

            <p class="detail-description">
                <?= nl2br(htmlspecialchars(
                    $producto['description'] ?? 'Sin descripción disponible.',
                    ENT_QUOTES,
                    'UTF-8'
                )) ?>
            </p>

            <div class="detail-divider"></div>

            <h6>Disponibilidad</h6>

            <?php if ($controlInventario === 'tracked'): ?>

                <?php if ($existencias > 0): ?>
                    <p class="text-success">
                        Disponible: <?= $existencias ?> unidades
                    </p>
                <?php else: ?>
                    <p class="text-danger">
                        Producto agotado
                    </p>
                <?php endif; ?>

            <?php else: ?>

                <p class="text-success">
                    Disponibilidad flexible
                </p>

            <?php endif; ?>

            <div class="detail-divider"></div>

            <!-- Formulario para agregar al carrito -->
            <form
                action="<?= BASE_URL ?>/pages/cart.php"
                method="POST"
                class="product-cart-form"
            >
                <input type="hidden" name="action" value="agregar">

                <input
                    type="hidden"
                    name="product_id"
                    value="<?= (int) $producto['id'] ?>"
                >

                <label for="quantity" class="form-label">
                    Cantidad
                </label>

                <input
                    type="number"
                    id="quantity"
                    name="quantity"
                    class="form-control quantity-input"
                    value="1"
                    min="1"
                    <?php if ($controlInventario === 'tracked'): ?>
                        max="<?= $existencias ?>"
                    <?php else: ?>
                        max="99"
                    <?php endif; ?>
                    <?= !$disponible ? 'disabled' : '' ?>
                    required
                >

                <button
                    type="submit"
                    class="btn btn-primary btn-add-cart mt-4"
                    <?= !$disponible ? 'disabled' : '' ?>
                >
                    <i class="bi bi-cart-plus"></i>
                    Agregar al carrito
                </button>
            </form>

            <p class="detail-note">
                Compra fácil y atención personalizada en Maranatha Shop.
            </p>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 