<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="css/styles_register.css">
</head>
<body>
    <div class="login-container">
        <h1>Registro</h1>

        <!-- Mostrar mensajes de error -->
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/register">
            <div class="input-group">
                <input type="text" name="name" placeholder="Nombre completo" required>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="Correo electrónico" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>
            <button type="submit">Registrar</button>
        </form>
        <div class="register-link">
            <a href="/login">¿Ya tienes cuenta? Iniciar sesión</a>
        </div>
    </div>
</body>
</html>
