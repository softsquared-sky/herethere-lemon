<?php

function isRedundantPostNo($postNo)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT postNo FROM Posts WHERE postNo= ? ) AS exist;";

    $st = $pdo->prepare($query);

    $st->execute([$postNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);

}

function addComment($email, $postNo, $commentContents, $commentTime)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO Comment (postNo, email, commentContents, commentTime) VALUES (?,?,?,?);";

    $st = $pdo->prepare($query);
    $st->bindParam(1, $postNo, PDO::PARAM_INT);
    $st->bindParam(2, $email, PDO::PARAM_STR);
    $st->bindParam(3, $commentContents, PDO::PARAM_STR);
    $st->bindParam(4, $commentTime, PDO::PARAM_STR);
    $st->execute();


    $query1 = "UPDATE Posts SET commentCount = commentCount+1 WHERE postNo = ?;";

    $st = $pdo->prepare($query1);
    $st->execute([$postNo]);

    $st = null;
    $pdo = null;

}

function comments($postNo, $no, $email){
    $pdo = pdoSqlConnect();
    $query = "SELECT a.userPicture, b.nickName, c.commentTime, c.commentContents
    FROM UserProfile a INNER JOIN User b on a.email = b.email INNER JOIN Comment c on c.postNo=? WHERE b.email = ? ORDER BY commentTime DESC LIMIT ?,10";

    $st = $pdo->prepare($query);
    $st->bindParam(1, $postNo, PDO::PARAM_INT);
    $st->bindParam(2, $email, PDO::PARAM_STR);
    $st->bindParam(3, $no, PDO::PARAM_INT);
    $st->execute();

    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $timeCount = 0;
    $timeAgo = Array();
    
    while ($timeCount < count($res)) {
        $res[$timeCount]['commentTime'] =   strtotime(getTodayByTimeStamp())- strtotime($res[$timeCount]['commentTime']);
        if ($res[$timeCount]['commentTime'] / 60 < 1) {
            $res[$timeCount]['commentTime'] = floor($res[$timeCount]['commentTime']);
            $timeAgo[$timeCount] = ''.$res[$timeCount]['commentTime'].' 초 전';
        } else if ($res[$timeCount]['commentTime'] / 60 >= 1 && $res[$timeCount]['commentTime'] / 60 < 60) {
            $res[$timeCount]['commentTime'] = floor($res[$timeCount]['commentTime'] / 60);
            $timeAgo[$timeCount] = ''.$res[$timeCount]['commentTime'].'분 전';
        } else if ($res[$timeCount]['commentTime'] / 60 >= 60 && $res[$timeCount]['commentTime'] / 3600 < 24) {
            $res[$timeCount]['commentTime'] = floor($res[$timeCount]['commentTime'] / 3600);
            $timeAgo[$timeCount] = ''.$res[$timeCount]['commentTime'].'시간 전';
        } else if ($res[$timeCount]['commentTime'] / 3600 >= 24 && $res[$timeCount]['commentTime'] / 3600 / 24 < 30) {
            $res[$timeCount]['commentTime'] = floor($res[$timeCount]['commentTime'] / 3600 / 24);
            $timeAgo[$timeCount] = ''.$res[$timeCount]['commentTime'].'일 전';
        } else if ($res[$timeCount]['commentTime'] / 3600 * 24 >= 30 && $res[$timeCount]['commentTime'] / 3600 /24 < 365) {
            $res[$timeCount]['commentTime'] = floor($res[$timeCount]['commentTime'] / 3600 / 24 / 30);
            $timeAgo[$timeCount] = ''.$res[$timeCount]['commentTime'].'달 전';
        } else if ($res[$timeCount]['commentTime'] / 3600 * 24 >= 365) {
            $res[$timeCount]['commentTime'] = floor($res[$timeCount]['commentTime'] / 3600 / 24 / 365);
            $timeAgo[$timeCount] = ''.$res[$timeCount]['commentTime'].'년 전';
        }
        $timeCount = $timeCount + 1;
    }

    $data = Array();
    $commentCount = 0;
    while ($commentCount < count($res)) {
        $data[$commentCount]['userPicture']=$res[$commentCount]['userPicture'];
        $data[$commentCount]['nickName']=$res[$commentCount]['nickName'];
        $data[$commentCount]['timeAgo']=$timeAgo[$commentCount ];
        $data[$commentCount]['commentContents']=$res[$commentCount]['commentContents'];

        $commentCount = $commentCount + 1;
    }
    
    return $data;
}

function isRedundantPostHeart($postNo, $email){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM Heart WHERE postNo= ? AND email=?) AS exist;";

    $st = $pdo->prepare($query);

    $st->execute([$postNo,$email]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

function addHeart($email, $postNo){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO Heart (postNo, email) VALUES (?,?);";

    $st = $pdo->prepare($query);
    $st->bindParam(1, $postNo, PDO::PARAM_INT);
    $st->bindParam(2, $email, PDO::PARAM_STR);
    $st->execute();

    $query1 = "UPDATE Posts SET heartCount = heartCount+1 WHERE postNo = ?;";

    $st = $pdo->prepare($query1);
    $st->execute([$postNo]);

    $st = null;
    $pdo = null;

}
