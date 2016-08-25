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
            $output['data']['Message'] = 'The parameters are not complete.';
        } else {
            $username = addslashes($_GET['username']);

            if ((strlen($username) < 4) || (strlen($username) > 20)) {
                $output['result'] = 'false';
                $output['data']['Message'] = 'Length of the account is error.';
            } elseif (!preg_match( '/^([0-9a-zA-Z]+)$/', $username)) {
                $output['result'] = 'false';
                $output['data']['Message'] = 'The format of account is wrong.';
            } else {
                $sql = "SELECT `aName` FROM `account` WHERE `aName` = :username";
                $result = $this->prepare($sql);
                $result->bindParam('username', $username);
                $result->execute();
                $row = $result->fetch();

                if (isset($row['aName'])) {
                    $output['result'] = 'false';
                    $output['data']['Message'] = 'The account is repeated.';
                } else {
                    $sql = "INSERT INTO `account`(`aName`) VALUES (:username)";
                    $sh = $this->prepare($sql);
                    $sh->bindParam('username', $username);

                    if ($sh->execute()) {
                        $output['result'] = 'true';
                        $output['data']['Message'] = 'Add account successful.';
                    } else {
                        $output['result'] = 'false';
                        $output['data']['Message'] = 'Add account failed.';
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
            $output['data']['Message'] = 'The parameters are not complete.';
        } else {
            $username = addslashes($_GET['username']);

            $sql = "SELECT `aName` FROM `account` WHERE `aName` = :username";
            $result = $this->prepare($sql);
            $result->bindParam('username', $username);
            $result->execute();
            $row = $result->fetch();

            if (!isset($row['aName'])) {
                $output['result'] = 'false';
                $output['data']['Message'] = 'The account is not exist.';
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
            $output['data']['Message'] = 'The parameters are not complete.';
        } else {
            $username = addslashes($_GET['username']);
            $transid = addslashes($_GET['transid']);
            $type = addslashes($_GET['type']);
            $amount = addslashes($_GET['amount']);

            if (!preg_match( '/^([0-9]+)$/', $transid)) {
                $output['result'] = 'false';
                $output['data']['Message'] = 'The format of transid is wrong.';
            } else if (!preg_match( '/^([0-9]+)$/', $amount)) {
                $output['result'] = 'false';
                $output['data']['Message'] = 'The format of amount is wrong.';
            } elseif (($type != 'IN') && ($type != 'OUT')){
                $output['result'] = 'false';
                $output['data']['Message'] = 'The format of type is wrong.';
            } else {
                $sql = "SELECT * FROM `account` WHERE `aName` = :username";
                $result = $this->prepare($sql);
                $result->bindParam('username', $username);
                $result->execute();
                $row = $result->fetch();

                if (!isset($row['aName'])) {
                    $output['result'] = 'false';
                    $output['data']['Message'] = 'The account is not exist.';
                } else {
                    $aID = $row['aID'];

                    if ($type === 'OUT') {
                        if ($row['balance'] < $amount) {
                            $output['result'] = 'false';
                            $output['data']['Message'] = 'Insufficient Account Balance';
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
                            $output['data']['Message'] = 'The transid is repeated.';
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
                                $output['data']['Message'] = 'Transfer successful';
                            } else {
                                $output['result'] = 'false';
                                $output['data']['Message'] = 'Transfer failed';
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
            $output['data']['Message'] = 'The parameters are not complete.';
        } else {
            $transid = addslashes($_GET['transid']);

            if (!preg_match( '/^([0-9]+)$/', $transid)) {
                $output['result'] = 'false';
                $output['data']['Message'] = 'The format of transid is wrong.';
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
                        $output['data']['TransType'] = 'IN';
                    } else {
                        $output['data']['TransType'] = 'OUT';
                    }

                    $output['data']['Message'] = 'Result is Successful';
                } else {
                    $output['result'] = 'false';
                    $output['data']['Message'] = 'The transid is not exist.';
                }
            }

        }

        echo json_encode($output,JSON_UNESCAPED_UNICODE);
    }

}