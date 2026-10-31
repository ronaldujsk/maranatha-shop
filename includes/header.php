
<?php

// Definir la ruta principal si todavía no existe
if (!defined('BASE_URL')) {
    define(
        'BASE_URL',
        rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/')
    );
}

$pageTitle = $pageTitle ?? 'Maranatha Shop';

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Maranatha Shop: tecnología, hogar, cocina y mucho más.">

    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- CSS propio -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=6">

</head>

<body>

<header class="main-header">

    <nav class="navbar navbar-expand-lg navbar-light bg-white">

        <div class="container">

            <!-- LOGOTIPO -->
            <a class="navbar-brand brand-name" href="<?= BASE_URL ?>/index.php">
                MERC<span>APP</span>
            </a>

            <!-- MENÚ RESPONSIVE -->
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menuPrincipal"
                aria-controls="menuPrincipal"
                aria-expanded="false"
                aria-label="Abrir menú"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">

                <!-- NAVEGACIÓN -->
                <ul class="navbar-nav mx-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/index.php">
                            Inicio
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/index.php#categorias">
                            Categorías
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/index.php#productos">
                            Productos
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/index.php#contacto">
                            Contacto
                        </a>
                    </li>

                </ul>

                <!-- ACCIONES -->
                <div class="header-actions">

                    <a href="<?= BASE_URL ?>/pages/cart.php" class="cart-link">
                        <i class="bi bi-cart3"></i>
                        Carrito
                    </a>

                    <span class="btn btn-dark btn-login">
                        <i class="bi bi-person"></i>
                        Mi cuenta
                    </span>

                </div>

            </div>

        </div>

    </nav>

</header>
