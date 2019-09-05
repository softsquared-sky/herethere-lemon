<?php
require 'function.php';


const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";
$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "addComment":
            // jwt 유효성 검사
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 511;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $postNo = $req->postNo;
            $commentContents = $req->commentContents;

            $blank = ' ';

            if(!isset($postNo) || !isset($commentContents) || empty($postNo) || empty($commentContents)
            || !strpos($postNo,$blank)==false){
                $res->isSuccess = false;
                $res->code = 500;
                $res->message = "잘못된 형식의 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $userData = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $email = $userData -> email;

            if(mb_strlen($commentContents, 'utf-8')>150 ||
                mb_strlen($commentContents, 'utf-8')<1){
                $res->isSuccess = false;
                $res->code = 501;
                $res->message = "너무 길거나 짧은 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if(!isRedundantPostNo($postNo)){
                $res->isSuccess =false;
                $res->code = 507;
                $res->message = "해당하는 게시글이 존재하지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $commentTime = getTodayByTimeStamp();
            addComment($email, $postNo, $commentContents, $commentTime);

            $res->isSuccess = true;
            $res->code = 105;
            $res->message = "동록에 성공하였습니다.";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        case "comments":
            $no = $_GET['current'];
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 511;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $userData = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $email = $userData -> email;

            $postNo = $req->postNo;

            $blank = ' ';

            if(!isset($postNo) || !isset($no)){
                $res->isSuccess = false;
                $res->code = 500;
                $res->message = "잘못된 형식의 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if(!isRedundantPostNo($postNo)){
                $res->isSuccess =false;
                $res->code = 508;
                $res->message = "해당하는 댓글이 존재하지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            $res->result = comments($postNo, $no, $email);
            $res->isSuccess = true;
            $res->code = 102;
            $res->message = "조회에 성공하였습니다.";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "addHeart":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 511;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            $userData = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $email = $userData -> email;

            $postNo = $req->postNo;

            $blank = ' ';

            if(!isset($postNo) || !strpos($postNo,$blank)==false){
                $res->isSuccess = false;
                $res->code = 500;
                $res->message = "잘못된 형식의 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if(!isRedundantPostNo($postNo)){
                $res->isSuccess =false;
                $res->code = 507;
                $res->message = "해당하는 게시글이 존재하지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            if(isRedundantPostHeart($postNo, $email)){
                $res->isSuccess = false;
                $res->code = 500;
                $res->message = "잘못된 형식의 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            addHeart($email, $postNo);

            $res->isSuccess = true;
            $res->code = 105;
            $res->message = "동록에 성공하였습니다.";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;



    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
