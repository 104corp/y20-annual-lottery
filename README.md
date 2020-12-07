# 2020 尾牙抽獎後端 API

> 一個拿來尾牙抽獎的 API 們，使用 Laravel 7.0 做開發

## 運行環境需求

符合 [Laravel Server Requirements](https://laravel.com/docs/7.x/installation#server-requirements) 即可達到運行需求 

### 其他運行必須

- `composer` 進行相關套件的下載
- 生成需要的環境檔：`cp .env.example .env`
- 生成key：`make key.generate`

### 本機運行

`make program.start` 即可運行。

若是要運行測試資料（`candidates_test.csv` & `awards_test.csv`），則下 `make program.start type=test`。

若程式已運行過，在 `database/database.sqlite` 會有抽獎紀錄，此時要運行則用 `php artisan serve` 即可。

## Swagger 文件

使用 `zircote/swagger-php` 與 `darkaonline/l5-swagger` 撰寫 API 文件

- `make api-docs` 即可在 `storage/api-docs/` 產生 swagger json 文件；
- 靜態頁面於 `http://localhost:8000/api/documentation`。

## 測試相關

- code-sniffing 可下 `./vendor/bin/phpcs` 相關設定檔於 [`phpcs.xml`](/phpcs.xml)；
- 測試可下 `make test.start`

## 其他文件

其他相關文件位於 [`storage/docs/`](storage/docs/)

## Built With

- [Laravel](http://laravel.com) - The web framework

## 開發者

- **Harbor Liu** 
