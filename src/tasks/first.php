<?php

// Ваш вебхук URL
$webhookUrl = 'https://b24-65w4gc.bitrix24.ru/rest/1/0mditowjfls00j72/';

// Метод API, который мы будем вызывать
$method = 'crm.contact.list';

// Параметры запроса
$params = [
    'filter' => ['LAST_NAME' => 'Иванов'],
    'select' => ['ID', 'NAME', 'LAST_NAME'],
    'order'  => ['ID' => 'ASC'],
    'start'  => 0
];

// Построение полного URL для запроса
$url = $webhookUrl . $method . '?' . http_build_query($params);

// Выполнение HTTP-запроса
$response = file_get_contents($url);

if ($response === FALSE) {
    echo 'Ошибка при выполнении запроса' . PHP_EOL;
    exit;
}

// Декодирование JSON-ответа
$result = json_decode($response, true);

if (isset($result['result']) && !empty($result['result'])) {
    // Получаем ID первого контакта
    $firstContact = $result['result'][0];
    echo 'ID первого контакта: ' . $firstContact['ID'] . PHP_EOL;
} else {
    echo 'Ничего не найдено' . PHP_EOL;
}