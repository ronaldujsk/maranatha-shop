<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Maranatha Shop</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                MARANATHA SHOP
            </a>
        </div>
    </nav>

    <main class="container py-5 text-center">

        <h1 class="display-4 fw-bold">
            Bienvenido a Maranatha Shop
        </h1>

        <p class="lead text-secondary">
            Todo lo que buscas, en un solo lugar.
        </p>

        <div class="card shadow-sm border-0 mx-auto mt-4"
             style="max-width: 500px;">

            <div class="card-body p-4">

                <h3 class="text-success">
                    ¡Nuestro proyecto ha comenzado!
                </h3>

                <p>
                    <?php
                        echo "PHP está funcionando correctamente.";
                    ?>
                </p>

                <button class="btn btn-primary">
                    Explorar tienda
                </button>

            </div>

        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
