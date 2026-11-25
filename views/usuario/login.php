<style>
    .auth-contenedor {
        max-width: 440px;
        margin: 3rem auto;
        padding: 0 1rem;
    }

    .auth-tarjeta {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 2.2rem 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .auth-titulo {
        margin: 0 0 0.4rem 0;
        font-size: 1.65rem;
        color: #111827;
        text-align: center;
    }

    .auth-subtitulo {
        text-align: center;
        color: #4b5563;
        font-size: 0.92rem;
        margin-bottom: 1.5rem;
    }

    .auth-grupo {
        margin-bottom: 1.1rem;
    }

    .auth-grupo label {
        display: block;
        font-weight: 600;
        font-size: 0.9rem;
        color: #1f2937;
        margin-bottom: 0.4rem;
    }

    .auth-grupo input {
        width: 100%;
        padding: 0.65rem 0.85rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.95rem;
        box-sizing: border-box;
    }

    .auth-grupo input:focus {
        outline: none;
        border-color: #d8af1d;
    }

    .auth-btn {
        width: 100%;
        padding: 0.75rem;
        background-color: #000000;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        margin-top: 0.5rem;
        transition: background-color 0.2s ease;
    }

    .auth-btn:hover {
        background-color: #d8af1d;
        color: #000000;
    }

    .auth-alerta-error {
        background-color: #fee2e2;
        color: #b91c1c;
        padding: 0.75rem 1rem;
        border-radius: 6px;
        font-size: 0.9rem;
        margin-bottom: 1.2rem;
        text-align: center;
    }

    .auth-alerta-exito {
        background-color: #dcfce7;
        color: #166534;
        padding: 0.75rem 1rem;
        border-radius: 6px;
        font-size: 0.9rem;
        margin-bottom: 1.2rem;
        text-align: center;
    }

    .auth-pie {
        text-align: center;
        margin-top: 1.3rem;
        font-size: 0.9rem;
        color: #4b5563;
    }

    .auth-pie a {
        color: #111827;
        font-weight: 600;
        text-decoration: underline;
    }
</style>

<div class="auth-contenedor">
    <div class="auth-tarjeta">
        <h1 class="auth-titulo">Iniciar sesión</h1>
        <p class="auth-subtitulo">Accede con tu cuenta de la Posada de Hyrule</p>

        <?php if (isset($_GET['registrado'])): ?>
            <div class="auth-alerta-exito">
                Cuenta creada correctamente. Ya puedes iniciar sesión.
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="auth-alerta-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form action="index.php?controller=usuario&action=login" method="POST">
            <div class="auth-grupo">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" required placeholder="link@hyrule.com">
            </div>

            <div class="auth-grupo">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="auth-btn">Entrar</button>
        </form>

        <div class="auth-pie">
            ¿Aún no tienes cuenta? <a href="index.php?controller=usuario&action=registro">Regístrate aquí</a>
        </div>
    </div>
</div>