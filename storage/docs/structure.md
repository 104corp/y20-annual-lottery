## 程式設計結構

### API 相關

主要的邏輯都放置於 `App/Actions/` 的目錄裡，依照檔案名稱來對應 API 的行為。

- `GetCandidates.php`：取得所有參加者資訊
- `GetAwards.php`：取得所有獎項資訊
- `Draw.php`：抽獎
- `Withdraw.php`：放棄獎項
- `CreateAward.php`：新增新獎項（2020 年的抽獎無提供此功能）
- `GetAward.php`：取得指定獎項的贏家

### 其他

- `Init.php`：初始化塞入資料進資料庫的邏輯，主要是用於 `routes/console.php` 下指令需要
- `PrintResult.php`： `AwardObserver::updated()` 監聽時呼叫，當抽出新得獎者時，會更新 `storage/app/winnerList.csv` 的資料。

### Laravel Action

> 官方文件：https://laravelactions.com/

這個 Project 使用到 `lorisleiva/laravel-actions` 這個套件。Laravel action 簡單來說，就是以「行為」作為切分物件的基本單位；不同於 Controller 與 Service 等，一個 Controller 會收納多個行為不同的方法。

在這裡，與 API 相關的 Actions 都作為 invokable controller 使用， Action 中的 `response()` 方法則是專屬的 HTTP response。

### Laravel Api Resource

官方文件：https://laravel.com/docs/5.8/eloquent-resources

API resource 是 Laravel 提供的一個包裝 json response 的一個功能。
它同時也提供 pagination、新增 meta data 或 model relatiosnship 關聯的判斷。

在此用在包裝 API 的 json 回傳格式。

相關檔案位於 `App/Http/Resource/`。

### Laravel Observer

官方文件：https://laravel.com/docs/7.x/eloquent#observers

Laravel 的 Observer 是綁定在 Model 上的，並且會監聽 model 的狀態：新增、更新、刪除，而每個都會再分為 Model 變化前與變化後的監聽。

在此用在即時紀錄得獎者名單，配合上述的 `App/Actions/PrintResult.php`。
