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

function isExistPosts($no)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT a.postNo, b.userPicture, c.nickName, a.postLocation, a.postTime, a.postContents
    , a.heartCount, a.commentCount FROM Posts a INNER JOIN UserProfile b on a.email = b.email INNER JOIN
     User c on a.email = c.email ORDER BY a.postTime LIMIT ?,5;";
//    SELECT postNo, COUNT(*) AS comment FROM Comment where postNo = ?;
    $number = $no;
    $st = $pdo->prepare($query);
    $st->bindParam(1, $number, PDO::PARAM_INT);
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

function isRedundantLocation($postLocation){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT location FROM LocationList WHERE location= ? ) AS exist;";


    $st = $pdo->prepare($query);

    $st->execute([$postLocation]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function postPosts($postPicture,$postLocation, $postContents, $email, $postTime){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO Posts (email, postContents, postLocation, postTime, heartCount, commentCount) VALUES (?, ?, ?, ?, ?,?);";

    $st = $pdo->prepare($query);
    $st->execute([$email, $postContents,$postLocation, $postTime,'0','0']);

    $postNo = $pdo->lastInsertId();



//    $query1= "INSERT INTO Picture (postNo, pictureNo, postPicture) VALUES "
    if(count($postPicture)==0){
        $query1="INSERT INTO Picture (postNo, pictureNo, postPicture) VALUES (?, ?, ?)";
        $st = $pdo->prepare($query1);
        $st->execute([$postNo, NULL, NULL]);
    }
    else{
        $question_marks = Array();
        $markCount = 0;
        while($markCount<count($postPicture)) {
            $question_marks[$markCount] = "(?,?,?)";
            $markCount = $markCount +1;
        }

        $question_mark = implode(',', $question_marks);
//        $pictureCount = 0;
//        $pictureArray = Array();

//        while($pictureCount<count($postPicture) ){
//            $pictureArray[$pictureCount] =(string)"'".$postNo."'".","."'".implode("','", $postPicture[$pictureCount])."'";
//            $pictureCount =$pictureCount +1;
//        }
//        echo $pictureArray[0];
//        echo implode(',', $pictureArray);

        $query = "INSERT INTO Picture (postNo, pictureNo, postPicture) VALUES $question_mark;";

        $st = $pdo->prepare($query);
        $paraCount = 0;
        while($paraCount<count($postPicture) ) {
            $st->bindParam(3 * $paraCount + 1, $postNo, PDO::PARAM_INT);
            $st->bindParam(3 * $paraCount + 2, $postPicture[$paraCount]['pictureNo'], PDO::PARAM_INT);
            $st->bindParam(3 * $paraCount + 3, $postPicture[$paraCount]['postPicture'], PDO::PARAM_STR);
            $paraCount = $paraCount + 1;
        }

        $st->execute();

    }
}

function isRedundantPost($postNo){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT postNo FROM Posts WHERE postNo= ? ) AS exist;";


    $st = $pdo->prepare($query);

    $st->execute([$postNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function showPost($postNo)
{
    $pdo = pdoSqlConnect();

    $query = "SELECT a.postNo, b.userPicture, c.nickName, a.postLocation, a.postTime, a.postContents
    , a.heartCount, a.commentCount FROM Posts a INNER JOIN UserProfile b on a.email = b.email INNER JOIN
     User c on a.email = c.email WHERE postNo = ?";


    $st = $pdo->prepare($query);
    $st->execute([$postNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $query1 = "SELECT * FROM Picture WHERE postNo = ? ORDER BY pictureNo;";
    $st = $pdo->prepare($query1);
    $st->execute([$postNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res1 = $st->fetchAll();

    $data = Array();
    $timeAgo = Array();

    $res[0]['postTime'] = strtotime(getTodayByTimeStamp()) - strtotime($res[0]['postTime']);
    if ($res[0]['postTime'] / 60 < 1) {
        $res[0]['postTime'] = floor($res[0]['postTime']);
        $timeAgo[0] = '' . $res[0]['postTime'] . ' 초 전';

    } else if ($res[0]['postTime'] / 60 >= 1 && $res[0]['postTime'] / 60 < 60) {
        $res[0]['postTime'] = floor($res[0]['postTime'] / 60);
        $timeAgo[0] = '' . $res[0]['postTime'] . '분 전';

    } else if ($res[0]['postTime'] / 60 >= 60 && $res[0]['postTime'] / 3600 < 24) {
        $res[0]['postTime'] = floor($res[0]['postTime'] / 3600);
        $timeAgo[0] = '' . $res[0]['postTime'] . '시간 전';

    } else if ($res[0]['postTime'] / 3600 >= 24 && $res[0]['postTime'] / 3600 / 24 < 30) {
        $res[0]['postTime'] = floor($res[0]['postTime'] / 3600 / 24);
        $timeAgo[0] = '' . $res[0]['postTime'] . '일 전';

    } else if ($res[0]['postTime'] / 3600 * 24 >= 30 && $res[0]['postTime'] / 3600 / 24 < 365) {
        $res[0]['postTime'] = floor($res[0]['postTime'] / 3600 / 24 / 30);
        $timeAgo[0] = '' . $res[0]['postTime'] . '달 전';

    } else if ($res[0]['postTime'] / 3600 * 24 >= 365) {
        $res[0]['postTime'] = floor($res[0]['postTime'] / 3600 / 24 / 365);
        $timeAgo[0] = '' . $res[0]['postTime'] . '년 전';
    }

    $pictureCount = 0;
    $pictureData = Array();
    $data[0]['postNo'] = $res[0]['postNo'];
    $data[0]['userPicture'] = $res[0]['userPicture'];
    $data[0]['nickName'] = $res[0]['nickName'];
    $data[0]['postLocation'] = $res[0]['postLocation'];
    $data[0]['timeAgo'] = $timeAgo[0];
    $data[0]['postContents'] = $res[0]['postContents'];
    while ($pictureCount < count($res1)) {
        $pictureData[$pictureCount]['postPicture'] = $res1[$pictureCount]['postPicture'];
        $pictureCount = $pictureCount + 1;
    }
    $data[0]['pictureList'] = $pictureData;
    $data[0]['heart'] = $res[0]['heartCount'];
    $data[0]['comment'] = '답글 ' . $res[0]['commentCount'] . '개';

//        array_push($test["result"], $data);
    $st = null;
    $pdo = null;
    return $data;
}