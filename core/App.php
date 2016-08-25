<?php

class App
{
    public function __construct()
    {
        $url = $this->parseUrl();

        require_once 'model/Account.php';

        $account = new Account;

        $methodName = $url[0];

        if (!method_exists($account, $methodName)) {
            return;
        }

        $account->$methodName();
    }

    public function parseUrl()
    {
        if (isset($_GET["url"])) {
            $url = rtrim($_GET['url'],"/");
            $url = explode("/",$url);

            return $url;
        }
    }
}
