<?php
$password = 'your_secure_password'; // Ваш пароль
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo "Хэш пароля: " . $hashedPassword;
