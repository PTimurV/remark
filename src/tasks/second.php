<?php

// Ваш вебхук URL
$webhookUrl = 'https://b24-65w4gc.bitrix24.ru/rest/1/0mditowjfls00j72/';

// Метод для получения списка контактов
$methodContactList = 'crm.contact.list';

// Параметры запроса для контактов
$paramsContactList = [
    'filter' => ['LAST_NAME' => 'Иванов'],
    'select' => ['ID'],
];

$urlContactList = $webhookUrl . $methodContactList . '?' . http_build_query($paramsContactList);

$responseContactList = file_get_contents($urlContactList);
$resultContactList = json_decode($responseContactList, true);

if (isset($resultContactList['result']) && !empty($resultContactList['result'])) {
    $contactIds = array_column($resultContactList['result'], 'ID');
} else {
    echo 'Ничего не найдено' . PHP_EOL;
    exit;
}

// Метод для получения списка сделок
$methodDealList = 'crm.deal.list';

// Параметры запроса для сделок
$paramsDealList = [
    'filter' => [
        'CONTACT_ID' => $contactIds,
    ],
    'select' => ['ID'],
];

$urlDealList = $webhookUrl . $methodDealList . '?' . http_build_query($paramsDealList);

$responseDealList = file_get_contents($urlDealList);
$resultDealList = json_decode($responseDealList, true);

if (isset($resultDealList['result']) && !empty($resultDealList['result'])) {
    $firstDeal = $resultDealList['result'][0];
    echo 'ID первой сделки: ' . $firstDeal['ID'] . PHP_EOL;
} else {
    echo 'Ничего не найдено' . PHP_EOL;
}
