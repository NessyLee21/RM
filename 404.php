<?php include 'header.php'; ?>

<link rel="stylesheet" href="css/styles.css">

<div style="
    text-align: center;
    padding: 50px;
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
">
    <h1 style="
        font-size: 80px;
        color: #dc3545;
        margin-bottom: 10px;
    ">404</h1>
    <h2 style="color: #343a40;">Ой! Такой страницы не существует.</h2>
    <p style="font-size: 18px; color: #6c757d;">Но не переживайте, вы можете вернуться на главную!</p>
    <img src="https://via.placeholder.com/400" alt="404 Image" style="margin: 20px 0; max-width: 100%; border-radius: 10px;">
    <br>
    <a href="/RM/index.php" style="
        display: inline-block;
        margin-top: 20px;
        padding: 12px 25px;
        font-size: 20px;
        font-weight: bold;
        color: white;
        background-color: #007bff;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
    " onmouseover="this.style.backgroundColor='#0056b3'" onmouseout="this.style.backgroundColor='#007bff'">
        Вернуться на главную
    </a>
</div>

<?php include 'footer.php'; ?>
