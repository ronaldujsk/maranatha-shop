<?php

session_start();

require_once __DIR__ . '/../config/database.php';

// Definir la ruta base del proyecto
if (!defined('BASE_URL')) {
    $directorioActual = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

    if (basename($directorioActual) === 'pages') {
        $directorioActual = dirname($directorioActual);
    }

    define('BASE_URL', rtrim($directorioActual, '/'));
}

// Crear conexión
$database = new Database();
$connection = $database->connect();

// Crear el carrito en la sesión si todavía no existe
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$mensaje = '';
$error = '';

// Procesar acciones enviadas desde formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Agregar un producto
    if ($action === 'agregar') {
        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if (!$productId || $productId < 1 || !$quantity || $quantity < 1) {
            $error = 'El producto o la cantidad no son válidos.';
        } else {
            $sql = "SELECT id, name, price, stock_control
                    FROM products
                    WHERE id = :id
                      AND status = 'active'
                    LIMIT 1";

            $statement = $connection->prepare($sql);
            $statement->bindValue(':id', $productId, PDO::PARAM_INT);
            $statement->execute();

            $producto = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$producto) {
                $error = 'El producto no está disponible.';
            } else {
                $stockControl = $producto['stock_control'];
                $cantidadActual = (int) ($_SESSION['carrito'][$productId]['quantity'] ?? 0);
                $cantidadNueva = $cantidadActual + $quantity;

                // Verificar inventario si el producto tiene control de existencias
                if ($stockControl === 'tracked') {
                    $sqlStock = "SELECT quantity
                                 FROM inventory
                                 WHERE product_id = :id
                                 LIMIT 1";

                    $stockStatement = $connection->prepare($sqlStock);
                    $stockStatement->bindValue(':id', $productId, PDO::PARAM_INT);
                    $stockStatement->execute();

                    $stock = $stockStatement->fetchColumn();

                    if ($stock === false || $cantidadNueva > (int) $stock) {
                        $error = 'No hay suficientes existencias para esa cantidad.';
                    }
                }

                if ($error === '') {
                    $_SESSION['carrito'][$productId] = [
                        'product_id' => (int) $producto['id'],
                        'name' => $producto['name'],
                        'price' => (float) $producto['price'],
                        'quantity' => $cantidadNueva
                    ];

                    $mensaje = 'Producto agregado al carrito.';
                }
            }
        }
    }

    // Actualizar la cantidad de un producto
    if ($action === 'actualizar') {
        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if ($productId && $quantity && $quantity > 0 && isset($_SESSION['carrito'][$productId])) {
            $sql = "SELECT stock_control
                    FROM products
                    WHERE id = :id
                      AND status = 'active'
                    LIMIT 1";

            $statement = $connection->prepare($sql);
            $statement->bindValue(':id', $productId, PDO::PARAM_INT);
            $statement->execute();

            $stockControl = $statement->fetchColumn();

            if ($stockControl === 'tracked') {
                $sqlStock = "SELECT quantity
                             FROM inventory
                             WHERE product_id = :id
                             LIMIT 1";

                $stockStatement = $connection->prepare($sqlStock);
                $stockStatement->bindValue(':id', $productId, PDO::PARAM_INT);
                $stockStatement->execute();

                $stock = (int) $stockStatement->fetchColumn();

                if ($quantity > $stock) {
                    $error = 'La cantidad supera las existencias disponibles.';
                }
            }

            if ($error === '') {
                $_SESSION['carrito'][$productId]['quantity'] = $quantity;
                $mensaje = 'Cantidad actualizada.';
            }
        } else {
            $error = 'La cantidad indicada no es válida.';
        }
    }

    // Eliminar un producto del carrito
    if ($action === 'eliminar') {
        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

        if ($productId && isset($_SESSION['carrito'][$productId])) {
            unset($_SESSION['carrito'][$productId]);
            $mensaje = 'Producto eliminado del carrito.';
        }
    }
}

// Calcular el total
$total = 0;

foreach ($_SESSION['carrito'] as $item) {
    $total += (float) $item['price'] * (int) $item['quantity'];
}

$pageTitle = 'Carrito de compras | Maranatha Shop';

require_once __DIR__ . '/../includes/header.php';

?>

<main class="container store-section">

    <h1 class="section-title">Carrito de compras</h1>

    <?php if ($mensaje !== ''): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($_SESSION['carrito'])): ?>

        <div class="text-center py-5">
            <i class="bi bi-cart3 display-4"></i>
            <h3 class="mt-3">Tu carrito está vacío</h3>
            <p>Explora nuestros productos y agrega tus favoritos.</p>

            <a
                href="<?= BASE_URL ?>/index.php#productos"
                class="btn btn-dark"
            >
                Ver productos
            </a>
        </div>

    <?php else: ?>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th style="width: 150px;">Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($_SESSION['carrito'] as $productId => $item): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars(
                                    $item['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td>
                                $<?= number_format(
                                    (float) $item['price'],
                                    0,
                                    ',',
                                    '.'
                                ) ?> COP
                            </td>

                            <td>
                                <form
                                    action="<?= BASE_URL ?>/pages/cart.php"
                                    method="POST"
                                    class="d-flex gap-2"
                                >
                                    <input
                                        type="hidden"
                                        name="action"
                                        value="actualizar"
                                    >

                                    <input
                                        type="hidden"
                                        name="product_id"
                                        value="<?= (int) $productId ?>"
                                    >

                                    <input
                                        type="number"
                                        name="quantity"
                                        class="form-control"
                                        min="1"
                                        value="<?= (int) $item['quantity'] ?>"
                                        required
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-outline-dark btn-sm"
                                        aria-label="Actualizar cantidad"
                                    >
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </form>
                            </td>

                            <td>
                                $<?= number_format(
                                    (float) $item['price'] * (int) $item['quantity'],
                                    0,
                                    ',',
                                    '.'
                                ) ?> COP
                            </td>

                            <td>
                                <form
                                    action="<?= BASE_URL ?>/pages/cart.php"
                                    method="POST"
                                >
                                    <input
                                        type="hidden"
                                        name="action"
                                        value="eliminar"
                                    >

                                    <input
                                        type="hidden"
                                        name="product_id"
                                        value="<?= (int) $productId ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-outline-danger btn-sm"
                                    >
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-column align-items-end mt-4">
            <h4>
                Total:
                $<?= number_format($total, 0, ',', '.') ?> COP
            </h4>

            <button
                type="button"
                class="btn btn-dark mt-3"
                disabled
                title="El proceso de compra se implementará después"
            >
                Continuar con la compra
            </button>
        </div>

    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>