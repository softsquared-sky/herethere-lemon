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
    , a.heartCount, d.comment FROM Posts a INNER JOIN UserProfile b on a.email = b.email INNER JOIN
     User c on a.email = c.email INNER JOIN (SELECT postNo, COUNT(*) AS comment FROM Comment GROUP BY postNo) d on a.postNo = d.postNo limit :number,5;";
    $number = $no;
    $st = $pdo->prepare($query);
    $st->bindParam(':number', $number, PDO::PARAM_INT);
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
    $pictureData = Array();

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
        $data[$postCount]['postNo'] = $res[$postCount]['postNo'];
        $data[$postCount]['userPicture'] = $res[$postCount]['userPicture'];
        $data[$postCount]['nickName'] = $res[$postCount]['nickName'];
        $data[$postCount]['postLocation'] = $res[$postCount]['postLocation'];
        $data[$postCount]['timeAgo'] = $timeAgo[$postCount];
        $data[$postCount]['postContents'] = $res[$postCount]['postContents'];
        while ($pictureCount < count($res1)) {
            if($res[$postCount]['postNo']==$res1[$pictureCount]['postNo']) {
                $pictureData[$pictureCount]['postPicture'] = $res1[$pictureCount]['postPicture'];
            }
            $pictureCount = $pictureCount + 1;
        }
        $data[$postCount]['pictureList'] = $pictureData;
        $data[$postCount]['heart'] = $res[$postCount]['heartCount'];
        $data[$postCount]['comment'] = '답글 '.$res[$postCount]['comment'].'개';

//        array_push($test["result"], $data);
        $postCount = $postCount + 1;
    }
    $st=null;$pdo = null;
    return $data;
}