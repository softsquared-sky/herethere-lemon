<?php
require 'function.php';


const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "index":
            echo "API Server";
            break;
        case "ACCESS_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/access.log");
            break;
        case "ERROR_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/errors.log");
            break;
        /*
         * API No. 0
         * API Name : 테스트 API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "test":
            http_response_code(200);
            $res->result = test();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 0
         * API Name : 테스트 Path Variable API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "testDetail":
            http_response_code(200);
            $res->result = testDetail($vars["testNo"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 0
         * API Name : 테스트 Body & Insert API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "testPost":
            http_response_code(200);
            #$res->result = testPost($req->name);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "SignUp":
            $email=$req->email;
            $password=$req->password;
            $rePassword=$req->rePassword;
            $name=$req->name;
            $birth=$req->birth;
            $nickName=$req->nickName;
            $schoolPicture=$req->schoolPicture;
            $schoolName=$req->schoolName;
            if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email))
            {
                $res->isSuccess = false;
                $res->code = 500;
                $res->message = "잘못된 형식의 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else if(!preg_match("/^[A-Za-z0-9+]{4,12}$/", $password)){
                $res->isSuccess = false;
                $res->code = 503;
                $res->message = "비밀번호는 영어, 숫자를 포함한 6~10자리입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else if($password!=$rePassword){
                $res->isSuccess = false;
                $res->code = 504;
                $res->message = "비밀번호가 일치하지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else if(!preg_match("/^[[0-9]{2}(0[1-9]|1[012])(0[1-9]|1[0-9]|2[0-9]|3[01])]{6,6}$/", $birth)){
                $res->isSuccess = false;
                $res->code = 500;
                $res->message = "잘못된 형식의 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else if(!preg_match("/^[\w\Wㄱ-ㅎㅏ-ㅣ가-힣]{2,10}$/", $nickName)){
                $res->isSuccess = false;
                $res->code = 501;
                $res->message = "너무 길거나 짧은 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else if(!preg_match("/^(http\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(?:\/\S*)?(?:[a-zA-Z0-9_])+\.(?:jpg|jpeg|gif|png))$/", $schoolPicture)){
                $res->isSuccess = false;
                $res->code = 505;
                $res->message = "첨부할수 없는 이미지입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            $able = SignUp($email, $password, $name, $birth, $nickName, $schoolPicture, $schoolName);
            if($able==1){
                $res->isSuccess = false;
                $res->code = 100;
                $res->message = "회원가입에 성공하였습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $res->isSuccess = false;
                $res->code = 502;
                $res->message = "이미 등록되어 있습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
