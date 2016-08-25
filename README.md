# API
url - https://booking-emma02.c9users.io/API/API名稱?參數=值<br>
ex.request - https://booking-emma02.c9users.io/API/getBalance?username=emma<br>
回傳格式為JSON<br>

<h3>1. 建立帳號</h3>
API名稱 - addUser<br>
必要參數1 - username 帳號名稱 (請輸入4-20個字元，限英文字母與數字)<br>
Result - {"result":"回傳結果(true or false)","data":{"Message":"訊息"}}<br>

<h3>2.取得餘額</h3>
API名稱 - getBalance<br>
必要參數1 - username 帳號名稱<br>
Result - {"result":"回傳結果(true or false)","data":{"Balance":"餘額",("Message":"訊息" 查詢失敗才會有)}}<br>

<h3>3.轉帳</h3>
API名稱 - transfer<br>
必要參數1 - username 帳號名稱<br>
必要參數2 - transid 轉帳序號 (唯一值 請輸入1-2147683647)<br>
必要參數3 - type 轉帳型態 (IN 為轉入, OUT 為轉出)<br>
必要參數4 - amount 轉帳金額 (正整數)<br>
Result - {"result":"回傳結果(true or false)","data":{"Message":"訊息"}}<br>

<h3>4.轉帳確認</h3>
API名稱 - checkTransfer<br>
必要參數1 - transid 轉帳序號<br>
Result - {"result":"回傳結果(true or false)","data":{"TransID":"轉帳序號","TransType":"轉帳型態(轉入 or 轉出)","Message":"訊息"}}<br>





