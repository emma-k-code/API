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

    public function getBalance()
    {
        if (!isset($_GET['username'])) {
            $output['result'] = 'false';
            $output['data']['Message'] = '沒有所需參數';
        } else {
            $username = addslashes($_GET['username']);

            $sql = "SELECT `aName` FROM `account` WHERE `aName` = :username";
            $result = $this->prepare($sql);
            $result->bindParam('username', $username);
            $result->execute();
            $row = $result->fetch();

            if (!isset($row['aName'])) {
                $output['result'] = 'false';
                $output['data']['Message'] = '無此帳號';
            } else {
                $sql = "SELECT `balance` FROM `account` WHERE `aName` = :username";
                $result = $this->prepare($sql);
                $result->bindParam('username', $username);
                $result->execute();
                $row = $result->fetch();

                $output['result'] = 'true';
                $output['data']['Balance'] = $row['balance'];
            }
        }

        echo json_encode($output,JSON_UNESCAPED_UNICODE);
    }

    public function transfer()
    {
        if ((!isset($_GET['username'])) && (!isset($_GET['transid'])) && (!isset($_GET['type'])) && (!isset($_GET['amount']))) {
            $output['result'] = 'false';
            $output['data']['Message'] = '沒有所需參數';
        } else {
            $username = addslashes($_GET['username']);
            $transid = addslashes($_GET['transid']);
            $type = addslashes($_GET['type']);
            $amount = addslashes($_GET['amount']);

            if (!preg_match( '/^([0-9]+)$/', $transid)) {
                $output['result'] = 'false';
                $output['data']['Message'] = '轉帳序號格式錯誤';
            } else if (!preg_match( '/^([0-9]+)$/', $amount)) {
                $output['result'] = 'false';
                $output['data']['Message'] = '轉帳金額輸入錯誤';
            } elseif (($type != 'IN') && ($type != 'OUT')){
                $output['result'] = 'false';
                $output['data']['Message'] = 'type輸入錯誤';
            } else {
                $sql = "SELECT * FROM `account` WHERE `aName` = :username";
                $result = $this->prepare($sql);
                $result->bindParam('username', $username);
                $result->execute();
                $row = $result->fetch();

                if (!isset($row['aName'])) {
                    $output['result'] = 'false';
                    $output['data']['Message'] = '無此帳號';
                } else {
                    $aID = $row['aID'];

                    if ($type === 'OUT') {
                        if ($row['balance'] < $amount) {
                            $output['result'] = 'false';
                            $output['data']['Message'] = '餘額不足';
                        } else {
                            $amount = -$amount;
                        }
                    }

                    if (!isset($output)) {
                        $sql = "SELECT `tID` FROM `transfer` WHERE `tID` = :tID";
                        $result = $this->prepare($sql);
                        $result->bindParam('tID', $transid);
                        $result->execute();
                        $row = $result->fetch();

                        if (isset($row['tID'])) {
                            $output['result'] = 'false';
                            $output['data']['Message'] = '已有該轉帳序號';
                        } else {
                            $sql = "INSERT INTO `transfer`(`tID`, `aID`, " .
                            "`amount`) VALUES (:tID, :aID, :amount)";
                            $sh = $this->prepare($sql);
                            $sh->bindParam('tID', $transid);
                            $sh->bindParam('aID', $aID);
                            $sh->bindParam('amount', $amount);

                            if ($sh->execute()) {
                                $sql = "UPDATE `account` SET `balance` = " .
                                "`balance` + :amount WHERE `aID` = :aID";
                                $sh = $this->prepare($sql);
                                $sh->bindParam('aID', $aID);
                                $sh->bindParam('amount', $amount);
                                $sh->execute();

                                $output['result'] = 'true';
                                $output['data']['Message'] = '轉帳成功';
                            } else {
                                $output['result'] = 'false';
                                $output['data']['Message'] = '轉帳失敗';
                            }
                        }
                    }
                }
            }
        }

        echo json_encode($output,JSON_UNESCAPED_UNICODE);
    }

    public function checkTransfer()
    {
        if ((!isset($_GET['transid']))) {
            $output['result'] = 'false';
            $output['data']['Message'] = '沒有所需參數';
        } else {
            $transid = addslashes($_GET['transid']);

            if (!preg_match( '/^([0-9]+)$/', $transid)) {
                $output['result'] = 'false';
                $output['data']['Message'] = '轉帳序號格式錯誤';
            } else {

                $sql = "SELECT * FROM `transfer` WHERE `tID` = :tID";
                $result = $this->prepare($sql);
                $result->bindParam('tID', $transid);
                $result->execute();
                $row = $result->fetch();

                if (isset($row['tID'])) {
                    $output['result'] = 'true';
                    $output['data']['TransID'] = $row['tID'];

                    if ($row['amount'] > 0) {
                        $output['data']['TransType'] = '轉入';
                    } else {
                        $output['data']['TransType'] = '轉出';
                    }

                    $output['data']['Message'] = '轉帳結果為成功';
                } else {
                    $output['result'] = 'false';
                    $output['data']['Message'] = '查無此轉帳序號';
                }
            }

        }

        echo json_encode($output,JSON_UNESCAPED_UNICODE);
    }

}