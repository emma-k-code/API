# API
url - https://booking-emma02.c9users.io/API/API名稱?參數=值<br>

<h3>1. 建立帳號</h3>
API名稱 - addUser<br>
必要參數1 - username 帳號名稱 (請輸入4-20個字元，限英文字母與數字)<br>

<h3>2.取得餘額</h3>
API名稱 - getBalance<br>
必要參數1 - username 帳號名稱<br>

<h3>3.轉帳</h3>
API名稱 - transfer<br>
必要參數1 - username 帳號名稱<br>
必要參數2 - transid 轉帳序號 (唯一值)<長度限制為11><br>
必要參數3 - type 轉帳型態 (IN 為轉入, OUT 為轉出)<br>
必要參數4 - amount 轉帳金額 (正整數)<br>

<h3>4.轉帳確認</h3>
API名稱 - checkTransfer<br>
必要參數1 - transid 轉帳序號<br>





