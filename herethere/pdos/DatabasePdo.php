<?php

//DB 정보
function pdoSqlConnect()
{
    try {
        $option = array(
            PDO::MYSQL_ATTR_FOUND_ROWS => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION    //에러출력 옵션 : 에러출력
        );

        $DB_HOST = "127.0.0.1";
        $DB_NAME = "herethere";
        $DB_USER = "root";
        $DB_PW = "tkrhk0152";
        $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PW, $option);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}