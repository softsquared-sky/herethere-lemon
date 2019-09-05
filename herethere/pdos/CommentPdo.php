<?php
//
//function isRedundantPostNo($postNo)
//{
//    $pdo = pdoSqlConnect();
//    $query = "SELECT EXISTS(SELECT postNo FROM Posts WHERE postNo= ? ) AS exist;";
//
//    $st = $pdo->prepare($query);
//
//    $st->execute([$postNo]);
//    $st->setFetchMode(PDO::FETCH_ASSOC);
//    $res = $st->fetchAll();
//
//    $st = null;
//    $pdo = null;
//
//    return intval($res[0]["exist"]);
//
//}
