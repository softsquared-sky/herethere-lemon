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
            if(!is_numeric($no) || !preg_match( "/[0-9]{0,10}/i", $no )){
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

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
