<?php

function isRedundantEmail($email){
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

function isRedundantNickName($nickName){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM User WHERE nickName= ?) AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$nickName]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}

function isSignUp($email, $password, $name, $birth, $nickName, $schoolPicture, $schoolName){

    $pdo = pdoSqlConnect();
    $query = "INSERT INTO User SELECT ?, ?, ?, ?,?, ?,?
 FROM DUAL WHERE NOT EXISTS (SELECT ?, ? FROM User WHERE email = ? OR nickName = ?)";
    // email 또는 nickName 이 중복되었을 경우 실행 x

    $st = $pdo->prepare($query);
    $st->execute([$email, $password, $name, $birth, $nickName, $schoolPicture, $schoolName, $email, $nickName, $email, $nickName]);
    $count = $st->rowCount();   // insert가 실행되었는지 쿼리의 여부

    if($count>0){
        return true;
    }
    else{
        return false;
    }

}
