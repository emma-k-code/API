<?php

/**
 * Class Database
 * 資料庫相關方法
 */
require_once 'Database.php';

class Account extends Database
{
    public function addUser()
    {
        if (!isset($_GET['username'])) {
            $output['result'] = 'false';
            $output['data']['Message'] = '沒有所需參數';
        } else {
            $username = addslashes($_GET['username']);

            if ((strlen($username) < 4) || (strlen($username) > 20)) {
                $output['result'] = 'false';
                $output['data']['Message'] = '帳號長度錯誤';
            } elseif (!preg_match( '/^([0-9a-zA-Z]+)$/', $username)) {
                $output['result'] = 'false';
                $output['data']['Message'] = '帳號包含非英文和數字的字元';
            } else {
                $sql = "SELECT `aName` FROM `account` WHERE `aName` = :username";
                $result = $this->prepare($sql);
                $result->bindParam('username', $username);
                $result->execute();
                $row = $result->fetch();

                if (isset($row['aName'])) {
                    $output['result'] = 'false';
                    $output['data']['Message'] = '帳號已存在';
                } else {
                    $sql = "INSERT INTO `account`(`aName`) VALUES (:username)";
                    $sh = $this->prepare($sql);
                    $sh->bindParam('username', $username);

                    if ($sh->execute()) {
                        $output['result'] = 'true';
                        $output['data']['Message'] = '帳號建立成功';
                    } else {
                        $output['result'] = 'false';
                        $output['data']['Message'] = '帳號建立失敗';
                    }
                }
            }
        }

        echo json_encode($output,JSON_UNESCAPED_UNICODE);
    }
}