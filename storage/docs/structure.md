## 程式設計結構

### API 相關

主要的邏輯都放置於 `App/Actions/` 的目錄裡，依照檔案名稱來對應 API 的行為。

- `GetCandidates.php`：取得所有參加者資訊
- `GetAwards.php`：取得所有獎項資訊
- `Draw.php`：抽獎
- `Withdraw.php`：放棄獎項
- `CreateAward.php`：新增新獎項
- `GetWinners.php`：取得所有贏家
- `GetWinner.php`：取得指定獎項的贏家

### 其他

- `Init.php`：初始化塞入資料進資料庫的邏輯，主要是用於 `routes/console.php` 下指令需要

### Laravel Action

> 官方文件：https://laravelactions.com/

這個 Project 使用到 `lorisleiva/laravel-actions` 這個套件。Laravel action 簡單來說，就是以「行為」作為切分物件的基本單位；不同於 Controller 與 Service 等，一個 Controller 會收納多個行為不同的方法。

在這裡，與 API 相關的 Actions 都作為 invokable controller 使用， Action 中的 `response()` 方法則是專屬的 HTTP response。
