<?php
//Не получилось сделать, не находит нужные записи
// Ваш вебхук URL
$webhookUrl = 'https://b24-65w4gc.bitrix24.ru/rest/1/0mditowjfls00j72/';

// Метод batch для пакетного запроса
$methodBatch = 'batch';

// Параметры пакетного запроса
$paramsBatch = [
    'halt' => 0, // Продолжать выполнение команд, даже если одна из них вернула ошибку
    'cmd' => [
        // Команда для получения контактов с фамилией "Иванов"
        'getContacts' => 'crm.contact.list?filter[LAST_NAME]=Иванов&select[]=ID',

        // Команда для получения сделок, связанных с этими контактами
        // Попытаемся использовать функцию #MAP# и #JSON_ENCODE# для передачи массива ID контактов
        'getDeals' => 'crm.deal.list?' .
            'filter[CONTACT_ID]=#JSON_ENCODE{#implode{=result["getContacts"]["result"],"ID"}#}#' .
            '&select[]=ID',
    ],
];

// Выполнение пакетного запроса
$urlBatch = $webhookUrl . $methodBatch;

$options = [
    'http' => [
        'header'  => "Content-Type: application/x-www-form-urlencoded",
        'method'  => 'POST',
        'content' => http_build_query($paramsBatch),
    ],
];

$context  = stream_context_create($options);
$responseBatch = file_get_contents($urlBatch, false, $context);

if ($responseBatch === FALSE) {
    echo 'Ошибка при выполнении запроса' . PHP_EOL;
    exit;
}

$resultBatch = json_decode($responseBatch, true);

// Проверяем результаты
if (isset($resultBatch['result']['result']['getDeals']) && !empty($resultBatch['result']['result']['getDeals'])) {
    $firstDeal = $resultBatch['result']['result']['getDeals'][0];
    echo 'ID первой сделки: ' . $firstDeal['ID'] . PHP_EOL;
} else {
    echo 'Ничего не найдено' . PHP_EOL;
}