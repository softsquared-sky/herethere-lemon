<?php
//require 'function.php';
//
//
//const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";
//$res = (Object)Array();
//header('Content-Type: json');
//$req = json_decode(file_get_contents("php://input"));
//try {
//    addAccessLogs($accessLogs, $req);
//    switch ($handler) {
//        case "addComment":
//            // jwt 유효성 검사
//            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//            $postNo = $req->postNo;
//            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
//                $res->isSuccess = FALSE;
//                $res->code = 201;
//                $res->message = "유효하지 않은 토큰입니다";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                addErrorLogs($errorLogs, $res, $req);
//                return;
//            }
//
//            if(isRedundantPostNo($postNo)){
//
//            }
//
//
//
//    }
//} catch (\Exception $e) {
//    return getSQLErrorException($errorLogs, $e, $req);
//}
