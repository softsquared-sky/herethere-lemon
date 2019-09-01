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

function isRedundantNo($locationNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT locationNo FROM LocationList;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $reqCount = 0;
    while($reqCount  < count($locationNo)) {
        $existCount = 0;
        $queryCount = 0;
        while ($queryCount <count($res)) {
            if($locationNo[$reqCount]["locationNo"]==$res[$queryCount]["locationNo"]){
                $existCount = $existCount+1;
            }
            $queryCount = $queryCount+1;
        }
        if($existCount==0){
            return false;
        }
        $reqCount =$reqCount+1;
    }

    $st=null;$pdo = null;
    return true;

}

function SignUp($email, $password, $name, $birth, $nickName, $schoolPicture, $schoolName, $locationNo){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO User SELECT ?,?,?,?,?,?,? FROM DUAL WHERE NOT EXISTS (SELECT email, nickName FROM User WHERE email=? OR
nickName = ?);";
//    $query = "INSERT INTO User (email, password, name, birth, nickName, schoolPicture, schoolName) VALUES (?, ?, ?, ? ,?, ?,?);";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$email, $password, $name, $birth, $nickName, $schoolPicture, $schoolName, $email, $nickName]);
//
//
    $query = "INSERT INTO UserProfile SELECT ?,?,? FROM DUAL WHERE NOT EXISTS (SELECT email FROM UserProfile WHERE email=?);";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$email,NULL,NULL,$email]);

//    $splitNo = implode('\', \'',$test);
////    $a  = str_replace(array("/"),' ',$splitNo);

//    $test = "1,2";
//    echo $test;
//
//    $test2 = "a,b";
//    echo $test2;
//    $a = '\'';
//    $b = '\'';
//    $splitNo = $a.$splitNo.$b;
//    print_r($splitNo);
////    $query = "SELECT * FROM LocationList;";
    $count = 0;

    while($count < count($locationNo)) {
        $query = "INSERT INTO UserLocation (email, location) SELECT ?, b.location FROM User a inner join LocationList b on b.locationNo = ? and a.email = ?;);";

        $st = $pdo->prepare($query);
        $st->execute([$email, $locationNo[$count]['locationNo'],$email]);
        $count = $count +1;

    }
    $st = null;
    $pdo = null;
}

function locationList(){
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM LocationList";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    return $res;

//    $arrayCount =0;
//    while($arrayCount<count($res)){
//        $data = Array();
//        $data["locationNo"] = $res[$arrayCount]["locationNo"];
//        $data["location"] = $res[$arrayCount]["location"];
//
//        array_push($result["result"], $data);
//
//        $arrayCount = $arrayCount+1;
//    }
//    echo $result["result"];
//    return $result;

}

function isRedundantUser($email, $password){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT email, password FROM user WHERE email= ? AND password=?) AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$email, $password]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}


