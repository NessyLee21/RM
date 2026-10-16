<?php
// Убедимся, что сессия запущена
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Проверяем, авторизован ли пользователь
$is_logged_in = isset($_SESSION['user_id']);
?>

<header class="header">
    <div class="logo">
        <a href="index.php"><img src="logo.png" alt="Logo"></a>
    </div>
    <nav>
        <a href="../RM/blog.php">Блог</a>
        <?php if ($is_logged_in): ?>
            <a href="../RM/profile.php">Профиль</a>
        <?php endif; ?>
        <a href="../RM/about.php">О нас</a>
        <a href="../RM/contact.php">Контакты</a>
    </nav>
    <?php if ($is_logged_in): ?>
        <a href="logout.php" class="login-button">Выйти</a>
    <?php else: ?>
        <button id="openModal" class="login-button">Войти</button>
    <?php endif; ?>
</header>

<!-- Модальное окно входа -->
<div id="loginModal" class="login-modal" style="display: none;">
    <div class="login-modal-content">
        <span class="modal-close" id="closeLoginModal">&times;</span>
        <h1>Вход</h1>
        <?php if (isset($_GET['message']) && $_GET['message'] === 'logout'): ?>
            <p class="success-message">Вы успешно вышли из системы.</p>
        <?php endif; ?>
        <form method="post" action="login.php" class="form">
            <input type="hidden" name="login" value="1">
            <input type="text" name="username" placeholder="Имя пользователя" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
        <div class="back-to-register">
            <a href="#" id="openRegisterModal" class="text-link" style="display: block; text-align: center;">Регистрация</a>
        </div>
    </div>
</div>

<!-- Модальное окно регистрации -->
<div id="registerModal" class="register-modal" style="display: none;">
    <div class="register-modal-content">
        <span class="modal-close" id="closeRegisterModal">&times;</span>
        <h1>Регистрация</h1>
        <form method="post" action="login.php" class="form">
            <input type="hidden" name="register" value="1">
            <input type="text" name="username" placeholder="Имя пользователя" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <input type="password" name="confirm_password" placeholder="Подтвердите пароль" required>
            <button type="submit">Зарегистрироваться</button>
        </form>
        <div class="back-to-login">
            <a href="#" id="openLoginModal" class="text-link" style="display: block; text-align: center;">Вход</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const loginModal = document.getElementById('loginModal');
        const registerModal = document.getElementById('registerModal');
        const openLoginModalButton = document.getElementById('openModal');
        const openRegisterModalButton = document.getElementById('openRegisterModal');
        const openLoginFromRegisterButton = document.getElementById('openLoginModal');
        const closeLoginModal = document.getElementById('closeLoginModal');
        const closeRegisterModal = document.getElementById('closeRegisterModal');

        if (openLoginModalButton) {
            openLoginModalButton.addEventListener('click', () => {
                loginModal.style.display = 'block';
            });
        }

        if (openRegisterModalButton) {
            openRegisterModalButton.addEventListener('click', () => {
                loginModal.style.display = 'none';
                registerModal.style.display = 'block';
            });
        }

        if (openLoginFromRegisterButton) {
            openLoginFromRegisterButton.addEventListener('click', () => {
                registerModal.style.display = 'none';
                loginModal.style.display = 'block';
            });
        }

        if (closeLoginModal) {
            closeLoginModal.addEventListener('click', () => {
                loginModal.style.display = 'none';
            });
        }

        if (closeRegisterModal) {
            closeRegisterModal.addEventListener('click', () => {
                registerModal.style.display = 'none';
            });
        }

        window.addEventListener('click', (event) => {
            if (event.target === loginModal) {
                loginModal.style.display = 'none';
            }
            if (event.target === registerModal) {
                registerModal.style.display = 'none';
            }
        });
    });
</script>
