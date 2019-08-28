<?php

//READ
function test()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM testtable;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

//READ
function testDetail($testNo)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM TEST_TB WHERE no = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$testNo]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}


function testPost($name)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO TEST_TB (name) VALUES (?);";

    $st = $pdo->prepare($query);
    $st->execute([$name]);

    $st = null;
    $pdo = null;

}

function SignUp($email, $password, $name, $birth, $nickName, $schoolPicture, $schoolName){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO User SELECT ?, ?, ?, ?,?, ?,?
 FROM DUAL WHERE NOT EXISTS (SELECT ?, ? FROM User WHERE email = ? OR nickName = ?)";
    // email 또는 nickName 이 중복되었을 경우 실행 x

    $st = $pdo->prepare($query);
    $st->execute([$email, $password, $name, $birth, $nickName, $schoolPicture, $schoolName, $email, $nickName, $email, $nickName]);
    $count = $st->rowCount();   // insert가 실행되었는지 쿼리의 여부

    if($count>0){
        return 1;
    }
    else{
        return 0;
    }

}

// CREATE
//    function addMaintenance($message){
//        $pdo = pdoSqlConnect();
//        $query = "INSERT INTO MAINTENANCE (MESSAGE) VALUES (?);";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message]);
//
//        $st = null;
//        $pdo = null;
//
//    }


// UPDATE
//    function updateMaintenanceStatus($message, $status, $no){
//        $pdo = pdoSqlConnect();
//        $query = "UPDATE MAINTENANCE
//                        SET MESSAGE = ?,
//                            STATUS  = ?
//                        WHERE NO = ?";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message, $status, $no]);
//        $st = null;
//        $pdo = null;
//    }

// RETURN BOOLEAN
//    function isRedundantEmail($email){
//        $pdo = pdoSqlConnect();
//        $query = "SELECT EXISTS(SELECT * FROM USER_TB WHERE EMAIL= ?) AS exist;";
//
//
//        $st = $pdo->prepare($query);
//        //    $st->execute([$param,$param]);
//        $st->execute([$email]);
//        $st->setFetchMode(PDO::FETCH_ASSOC);
//        $res = $st->fetchAll();
//
//        $st=null;$pdo = null;
//
//        return intval($res[0]["exist"]);
//
//    }
