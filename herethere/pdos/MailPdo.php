<?php

function isRightEmail($email){
$pdo = pdoSqlConnect();
$query = "SELECT EXISTS(SELECT * FROM User WHERE email= ?) AS exist;";


$st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
$st->execute([$email]);
$st->setFetchMode(PDO::FETCH_ASSOC);
$res = $st->fetchAll();

$st=null;$pdo = null;

return intval($res[0]["exist"]);

}

function updatePassword($email,$temporaryPassword){

    $pdo = pdoSqlConnect();
    $query = "UPDATE User SET password = ? WHERE email = ?;";


    $st = $pdo->prepare($query);
//    $st->execute([$param,$param])
    $st->execute([$temporaryPassword,$email]);
}