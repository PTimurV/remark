<?php

class CompanyHandler {
    public static function getCompanies() {
        $result = [];
        $batchSize = 50; // Количество подзапросов в одном batch-запросе
        $itemsPerCall = 50; // Количество элементов, возвращаемых одним вызовом crm.company.list
        $totalItems = 5000; // Общее количество компаний, которые нужно получить
        $batches = ceil($totalItems / ($batchSize * $itemsPerCall));

        for ($batchNum = 0; $batchNum < $batches; $batchNum++) {
            $cmd = [];
            for ($i = 0; $i < $batchSize; $i++) {
                $offset = ($batchNum * $batchSize * $itemsPerCall) + ($i * $itemsPerCall);
                $cmd["list{$offset}"] = 'crm.company.list?' . http_build_query([
                        'order' => ['TITLE' => 'ASC'],
                        'filter' => [],
                        'select' => ["ID", "TITLE", "COMPANY_TYPE"],
                        'start' => $offset,
                    ]);
            }
            $response = \Yii::$app->bitrix24->admin()->call('batch', [
                'cmd' => $cmd,
            ]);
            foreach ($response['result']['result'] as $res) {
                $result = array_merge($result, $res);
            }
        }

        return $result;
    }
}