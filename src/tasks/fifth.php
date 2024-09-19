<?php
// webhook.php

// Получаем сырые данные POST
$input = file_get_contents('php://input');

// Преобразуем JSON в массив
$data = json_decode($input, true);

// Проверяем наличие необходимых данных
if (isset($data['event']) && $data['event'] === 'ONCRMDEALADD') {
    // Получаем ID новой сделки
    $dealId = $data['data']['FIELDS']['ID'];

    // Получаем детали сделки из Битрикс24
    $dealDetails = getDealDetails($dealId);

    // Сохраняем данные сделки в базе данных
    saveDealToDatabase($dealDetails);
}

function getDealDetails($dealId) {
    // Ваш токен доступа к REST API Битрикс24
    $webhookUrl = 'https://your_bitrix24_domain/rest/1/your_webhook_code/';

    // Запрашиваем детали сделки
    $url = $webhookUrl . 'crm.deal.get.json?' . http_build_query([
            'id' => $dealId,
        ]);

    $response = file_get_contents($url);
    $result = json_decode($response, true);

    return $result['result'];
}

function saveDealToDatabase($dealDetails) {
    // Подключаемся к базе данных
    $pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');

    // Подготавливаем SQL-запрос для вставки данных
    $stmt = $pdo->prepare('INSERT INTO deals (id, title, amount, stage_id) VALUES (:id, :title, :amount, :stage_id)');

    // Выполняем запрос с параметрами
    $stmt->execute([
        ':id' => $dealDetails['ID'],
        ':title' => $dealDetails['TITLE'],
        ':amount' => $dealDetails['OPPORTUNITY'],
        ':stage_id' => $dealDetails['STAGE_ID'],
    ]);
}