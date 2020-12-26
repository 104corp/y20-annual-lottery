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

若程式已運行過，在 `database/database.sqlite` 會有抽獎紀錄，此時要運行則用 `make program.continue` 即可。

## Swagger 文件

使用 `zircote/swagger-php` 與 `darkaonline/l5-swagger` 撰寫 API 文件

- `make api-docs` 即可在 `storage/api-docs/` 產生 swagger json 文件；
- 靜態頁面於 `http://localhost:8000/api/documentation`。

## LOG 紀錄

抽獎、放棄這兩個行為會進行紀錄，紀錄檔 `drawing.log` 會紀錄於 `storage/logs/` 目錄下。
若要清空紀錄，可以下 `make program.clear-log` 指令。

## HASH 對照資料

`.env` 中有紀錄兩個 sha256 的 key，其中 `HASH_KEY` 是員工名單的 sha256 名單，若名單有不同，則無法運行程式；另一個 `LOG_KEY` 是 log 檔的 sha256，程式運行中會同時更新此 key。當程式重新啟動時也會對照此 key，log 若有異動則無法運行程式。

## DB 備份

有提供 cronjob 執行 DB 備份的功能。

首先需調整 `.env` 的 `DATABASE_BACKUP_PATH` 參數到你需要的路徑（絕對路徑，代表不能使用 `~/` 開頭）。

隨後在 cmd 下 `crontab -e` 後，新增 
```vim
* * * * * cd /{此專案位置} && php artisan schedule:run >> /dev/null 2>&1
```
新增後看見 `crontab: installing new crontab` 代表已成功執行 cronjob。他會**每分鐘**在背後執行備份。備份的 sql 檔名為 
```
database_{timestap}.sql
```

## 測試相關

- code-sniffing 可下 `./vendor/bin/phpcs` 相關設定檔於 [`phpcs.xml`](/phpcs.xml)；
- 測試可下 `make test.start`

## 其他文件

其他相關文件位於 [`storage/docs/`](storage/docs/)

## Built With

- [Laravel](http://laravel.com) - The web framework

## 開發者

- **Harbor Liu** 
