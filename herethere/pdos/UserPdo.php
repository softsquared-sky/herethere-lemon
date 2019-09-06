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

//    $locationCount = 0;
//    $locationList = Array();
//
//    while($locationCount<count($locationNo)){
//        $locationList[$locationCount]= $locationNo[$locationCount]['locationNo'];
//
//        $locationCount=$locationCount+1;
//    }

//    $splitNo = implode(',',$locationList);
//    echo $splitNo;

////    $a  = str_replace(array("/"),' ',$splitNo);

    $question_Count = 0;
    $question_Mark = Array();
    while($question_Count<count($locationNo)){
        $question_Mark[$question_Count] = '?';
        $question_Count=$question_Count+1;
    }

    $question_Marks = implode(',', $question_Mark);


    $query = "INSERT INTO UserLocation (email, location) SELECT a.email, b.location
 FROM User a inner join (SELECT * FROM LocationList WHERE locationNo IN ($question_Marks)) b on a.email = ?;";
    $st = $pdo->prepare($query);
    $locationCount = 0;

    while($locationCount<count($locationNo)) {
        $st->bindParam($locationCount+1,$locationNo[$locationCount]['locationNo'], PDO::PARAM_INT);
        $locationCount=$locationCount+1;
    }
    $st->bindParam($locationCount+1,$email, PDO::PARAM_STR);

    $st->execute();

//    $query = "INSERT INTO UserLocation (email, location) SELECT ?, b.location FROM
//User a inner join (SELECT * FROM LocationList WHERE locationNo IN (?)) b on a.email = ?;";
//
//    $st = $pdo->prepare($query);
//    $st -> bindParam(1, $email, PDO::PARAM_STR);
//    $st -> bindParam(2, $splitNo, PDO::PARAM);
//    $st -> bindParam(3, $email, PDO::PARAM_STR);
//    $st->execute();


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
    $query = "SELECT EXISTS(SELECT email, password FROM User WHERE email= ? AND password=?) AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$email, $password]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}

function profile($email){
    $pdo = pdoSqlConnect();

    $query = "SELECT a.userPicture, b.nickName, a.email, b.schoolName, a.status FROM UserProfile a 
INNER JOIN User b on a.email=b.email WHERE a.email = ?";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$email]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return $res;
}




