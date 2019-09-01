<?php

function isValidJWToken($email, $password){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM User WHERE email= ? AND password =?) AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$email, $password]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}

function isExistPosts()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT a.postNo, b.userPicture, c.nickName, a.postLocation, a.postTime, a.postContents
    , a.heartCount, d.comment FROM Posts a INNER JOIN UserProfile b on a.email = b.email INNER JOIN
     User c on a.email = c.email INNER JOIN
        (SELECT postNo, COUNT(*) AS comment FROM Comment GROUP BY postNo) d on a.postNo = d.postNo ;";

}




    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();


    $st=null;$pdo = null;
}

function currentTime()