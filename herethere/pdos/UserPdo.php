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

function isExistPost($no, $email){
    $pdo = pdoSqlConnect();
    $query = "SELECT a.postNo, b.userPicture, c.nickName, a.postLocation, a.postTime, a.postContents
    , a.heartCount, a.commentCount FROM Posts a INNER JOIN UserProfile b on a.email = b.email INNER JOIN
     User c on a.email = c.email WHERE a.email = ? ORDER BY a.postTime LIMIT ?,5;";
//    SELECT postNo, COUNT(*) AS comment FROM Comment where postNo = ?;
    $number = $no;
    $st = $pdo->prepare($query);
    $st->bindParam(1, $email, PDO::PARAM_STR);
    $st->bindParam(2, $number, PDO::PARAM_INT);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();


    if(count($res)==0) {
        return FALSE;
    }

    $query1 = "SELECT * FROM Picture ORDER BY postNo,pictureNo;";
    $st = $pdo->prepare($query1);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res1 = $st->fetchAll();

    $data = Array();

    $postCount = 0;
    $timeCount = 0;
    $timeAgo = Array();
    while ($timeCount < count($res)) {
        $res[$timeCount]['postTime'] =   strtotime(getTodayByTimeStamp())- strtotime($res[$timeCount]['postTime']);
        if ($res[$timeCount]['postTime'] / 60 < 1) {
            $res[$timeCount]['postTime'] = floor($res[$timeCount]['postTime']);
            $timeAgo[$timeCount] = ''.$res[$timeCount]['postTime'].' 초 전';
        } else if ($res[$timeCount]['postTime'] / 60 >= 1 && $res[$timeCount]['postTime'] / 60 < 60) {
            $res[$timeCount]['postTime'] = floor($res[$timeCount]['postTime'] / 60);
            $timeAgo[$timeCount] = ''.$res[$timeCount]['postTime'].'분 전';
        } else if ($res[$timeCount]['postTime'] / 60 >= 60 && $res[$timeCount]['postTime'] / 3600 < 24) {
            $res[$timeCount]['postTime'] = floor($res[$timeCount]['postTime'] / 3600);
            $timeAgo[$timeCount] = ''.$res[$timeCount]['postTime'].'시간 전';
        } else if ($res[$timeCount]['postTime'] / 3600 >= 24 && $res[$timeCount]['postTime'] / 3600 / 24 < 30) {
            $res[$timeCount]['postTime'] = floor($res[$timeCount]['postTime'] / 3600 / 24);
            $timeAgo[$timeCount] = ''.$res[$timeCount]['postTime'].'일 전';
        } else if ($res[$timeCount]['postTime'] / 3600 * 24 >= 30 && $res[$timeCount]['postTime'] / 3600 /24 < 365) {
            $res[$timeCount]['postTime'] = floor($res[$timeCount]['postTime'] / 3600 / 24 / 30);
            $timeAgo[$timeCount] = ''.$res[$timeCount]['postTime'].'달 전';
        } else if ($res[$timeCount]['postTime'] / 3600 * 24 >= 365) {
            $res[$timeCount]['postTime'] = floor($res[$timeCount]['postTime'] / 3600 / 24 / 365);
            $timeAgo[$timeCount] = ''.$res[$timeCount]['postTime'].'년 전';
        }
        $timeCount = $timeCount + 1;
    }



    while ($postCount < count($res)) {
        $pictureCount = 0;
        $count = 0;
        $pictureData = Array();
        $data[$postCount]['postNo'] = $res[$postCount]['postNo'];
        $data[$postCount]['userPicture'] = $res[$postCount]['userPicture'];
        $data[$postCount]['nickName'] = $res[$postCount]['nickName'];
        $data[$postCount]['postLocation'] = $res[$postCount]['postLocation'];
        $data[$postCount]['timeAgo'] = $timeAgo[$postCount];
        $data[$postCount]['postContents'] = $res[$postCount]['postContents'];
        while ($pictureCount < count($res1)) {
            if($res[$postCount]['postNo']==$res1[$pictureCount]['postNo']) {
                $pictureData[$count]['postPicture'] = $res1[$pictureCount]['postPicture'];
                $count = $count +1;
            }
            $pictureCount = $pictureCount + 1;
        }
        $data[$postCount]['pictureList'] = $pictureData;
        $data[$postCount]['heart'] = $res[$postCount]['heartCount'];
        $data[$postCount]['comment'] = '답글 '.$res[$postCount]['commentCount'].'개';

//        array_push($test["result"], $data);
        $postCount = $postCount + 1;
    }
    $st=null;$pdo = null;
    return $data;


}

function isExistPicture($no, $email){
    $pdo = pdoSqlConnect();
    $query = "SELECT a.postPicture FROM Picture a INNER JOIN Posts b on a.postNo = b.postNo WHERE b.email = ? ORDER BY b.postNo, a.pictureNo LIMIT ?,16;";
//    SELECT postNo, COUNT(*) AS comment FROM Comment where postNo = ?;
    $number = $no;
    $st = $pdo->prepare($query);
    $st->bindParam(1, $email, PDO::PARAM_STR);
    $st->bindParam(2, $number, PDO::PARAM_INT);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    if(count($res)==0) {
        return FALSE;
    }
    else{
        return $res;
    }
}

function newPassword($newPassword, $email){
    $pdo = pdoSqlConnect();
    $query = "UPDATE User SET password=? WHERE email = ?;";
//    SELECT postNo, COUNT(*) AS comment FROM Comment where postNo = ?;
    $st = $pdo->prepare($query);
    $st->bindParam(1, $newPassword, PDO::PARAM_STR);
    $st->bindParam(2, $email, PDO::PARAM_STR);
    $st->execute();

}




