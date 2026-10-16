<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О нас - RM News</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const faqQuestions = document.querySelectorAll(".faq-question");

            faqQuestions.forEach((question) => {
                question.addEventListener("click", () => {
                    const answer = question.nextElementSibling;
                    const isVisible = answer.style.display === "block";
                    answer.style.display = isVisible ? "none" : "block";
                });
            });
        });
    </script>
</head>
<body class="about-page">
<div class="about-container">
    <div class="about-header">
        <h1>О нас</h1>
    </div>
    <div class="about-description">
        <blockquote>
            «С неустанным стремлением к совершенству наше агентство цифрового маркетинга стремится разрабатывать индивидуальные стратегии, которые внушают уверенность и приводят к результатам.»
        </blockquote>
        <p>
            Благодаря инновационным подходам и глубокому пониманию тенденций в отрасли мы разрабатываем решения, которые соответствуют уникальным потребностям и целям наших клиентов.
            Мы помогаем компаниям процветать в динамичном цифровом ландшафте, что отражает нашу непоколебимую приверженность совершенству.
        </p>
    </div>

    <img src="uploads/about.jpg" alt="О нас" class="about-image">

    <div class="about-description">
        <p>
            RM News - это ваш надежный источник новостей о самых современных технологиях. Мы обеспечиваем наших читателей достоверной и актуальной информацией, которая помогает им оставаться на шаг впереди.
        </p>
    </div>

    <div class="faq-section">
        <h2>FAQ</h2>

        <div class="faq-item">
            <div class="faq-question">Что такое RM News?</div>
            <div class="faq-answer">RM News — это технологический новостной портал, созданный для тех, кто хочет быть в курсе современных инноваций.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Как связаться с нашей командой?</div>
            <div class="faq-answer">Вы можете отправить нам письмо через форму обратной связи на странице "Контакты" или написать на support@rmnews.com.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Какие темы освещает RM News?</div>
            <div class="faq-answer">Мы пишем о технологиях, искусственном интеллекте, стартапах, гаджетах и многом другом.</div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
