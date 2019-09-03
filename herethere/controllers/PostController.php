<?php
require 'function.php';


const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";
$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "posts":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $no = $_GET['current'];
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 511;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            if(!is_numeric($no) || !preg_match( "/^(0|-?[1-9][0-9]*)$/", $no ) || $no<0){
                $res->isSuccess = false;
                $res->code = 500;
                $res->message = "잘못된 형식의 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
//            || !preg_match( "/[0-9]{0,10}/i", $no )
            $bool=isExistPosts($no);
            if(!$bool){
                $res->isSuccess = TRUE;
                $res->code = 103;
                $res->message = "게시글이 존재하지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);

                return;
            }
            $res->result = isExistPosts($no);
            $res->isSuccess = TRUE;
            $res->code = 102;
            $res->message = "조회에 성공하였습니다.";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            addErrorLogs($errorLogs, $res, $req);
            break;

        case "addPost":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $postPicture =Array();
            $xml_data=json_encode($req -> postPictureList);
            $postPicture=json_decode($xml_data,true);
            $postLocation = $req -> postLocation;
            $postContents = $req -> postContents;
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


            if(count($postPicture)<0 || count($postPicture)>10){
                $res->isSuccess = false;
                $res->code = 500;
                $res->message = "잘못된 형식의 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $pictureCount = 0;

            while($pictureCount<count($postPicture)){
                preg_match_all("/^http[s]?:\/\/.*\.(jp[e]?g|gif|png)/Ui"
                    , $postPicture[$pictureCount]['postPicture'],$matches);
                if($matches[0]==NULL){
                    $res->isSuccess = false;
                    $res->code = 505;
                    $res->message = "첨부할수 없는 이미지입니다.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                if($postPicture[$pictureCount]['pictureNo'] > count($postPicture)
                    || $postPicture[$pictureCount]['pictureNo'] < 1) {
                    $res->isSuccess = false;
                    $res->code = 500;
                    $res->message = "잘못된 형식의 입력값입니다.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                $pictureCount = $pictureCount+1;
            }

            if(!preg_match("/^[가-힣]+$/", $postLocation)){
                $res->isSuccess = false;
                $res->code = 500;
                $res->message = "잘못된 형식의 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if(!isRedundantLocation($postLocation)){
                $res->isSuccess = false;
                $res->code = 500;
                $res->message = "잘못된 형식의 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if(mb_strlen('$postContents', 'utf-8')>1000 ||
                mb_strlen('$postContents', 'utf-8')<1){
                $res->isSuccess = false;
                $res->code = 501;
                $res->message = "너무 길거나 짧은 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
//            $postTime =getTodayByTimeStamp();
//            postPosts($postPicture,$postLocation, $postContents, $email, $postTime);


            $res->isSuccess = true;
            $res->code = 105;
            $res->message = "동록에 성공하였습니다.";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
