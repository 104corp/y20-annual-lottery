<?php

namespace App\Actions\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait HandleCsv
{
    /**
     * 把 csv 轉成需要放進資料庫內的 data
     *
     * @param string $fileName csv 檔名，同時為 database, model 的名字
     * @return array
     */
    private function handleCsvData(string $fileName): array
    {
        $csvArray = $this->openCsvToArray($fileName);

        $model = "App\\Model\\" . Str::ucfirst(Str::singular($fileName));
        $tableAttributes = Schema::getColumnListing((new $model())->getTable());

        $insertKeys = $this->handleInsertKeys($tableAttributes);
        $keyNumber = count($insertKeys);

        $insertData = [];
        foreach ($csvArray as $data) {
            if (count(array_slice($data, 0, $keyNumber)) == $keyNumber) {
                $insertData[] = array_combine($insertKeys, array_slice($data, 0, 5));
            }
        }
        return $insertData;
    }

    /**
     * 把需要的 attributes 取出來
     * @param array $tableAttributes
     *
     * @return array
     */
    private function handleInsertKeys(array $tableAttributes): array
    {
        $insertKeys = [];
        foreach ($tableAttributes as $attribute) {
            if (strpos($attribute, 'id') === false) {
                $insertKeys[] = $attribute;
            }
        }
        return $insertKeys;
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    private function openCsvToArray(string $fileName): array
    {
        $filePath = storage_path("app/$fileName.csv");
        try {
            $file = file($filePath);
        } catch (\Exception $e) {
            $fileName .= '_test';
            $filePath = storage_path("app/$fileName.csv");
            $file = file($filePath);
        }

        $csvArray = array_map('str_getcsv', $file);
        array_shift($csvArray);

        return $csvArray;
    }
}
