<?php
require 'function.php';


const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";
const EMAIL_VALID_CHECK_TYPE = 0;
const NICK_VALID_CHECK_TYPE = 1;
const SIGN_UP_TYPE = 2;
$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "signUp":
            $reqType=$req->reqType;

            switch($reqType){
                case EMAIL_VALID_CHECK_TYPE:
                    $email=$req->email;
                    if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email))
                    {
                        $res->isSuccess = false;
                        $res->code = 500;
                        $res->message = "잘못된 형식의 입력값입니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    if(isRedundantEmail($email)){
                        $res->isSuccess = false;
                        $res->code = 506;
                        $res->message = "이미 등록되어 있습니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    break;

                case NICK_VALID_CHECK_TYPE:
                    $nickName=$req->nickName;
                    if(!preg_match("/^[\w\Wㄱ-ㅎㅏ-ㅣ가-힣]{2,10}$/", $nickName)){
                        $res->isSuccess = false;
                        $res->code = 501;
                        $res->message = "너무 길거나 짧은 입력값입니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    if(isRedundantNickName($nickName)){
                        $res->isSuccess = false;
                        $res->code = 506;
                        $res->message = "이미 등록되어 있습니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    break;
                case SIGN_UP_TYPE:
                    $email=$req->email;
                    $password=$req->password;
                    $rePassword=$req->rePassword;
                    $name=$req->name;
                    $birth=$req->birth;
                    $nickName=$req->nickName;
                    $schoolPicture=$req->schoolPicture;
                    $schoolName=$req->schoolName;
                    $locationNo =Array();
                    $xml_data=json_encode($req -> locationList);
                    $locationNo=json_decode($xml_data,true);


                    if(!preg_match("/^[A-Za-z0-9+]{4,12}$/", $password)){
                        $res->isSuccess = false;
                        $res->code = 503;
                        $res->message = "비밀번호는 영어, 숫자를 포함한 6~10자리입니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    if($password!=$rePassword){
                        $res->isSuccess = false;
                        $res->code = 504;
                        $res->message = "비밀번호가 일치하지 않습니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    if(!preg_match("/[0-9]{2}(0[1-9]|1[012])(0[1-9]|1[0-9]|2[0-9]|3[01])/", $birth)){
                        $res->isSuccess = false;
                        $res->code = 500;
                        $res->message = "잘못된 형식의 입력값입니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    if(!preg_match("/^[0-9+]{1,6}$/", $birth)){
                        $res->isSuccess = false;
                        $res->code = 500;
                        $res->message = "잘못된 형식의 입력값입니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    if(!preg_match("/^(http\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(?:\/\S*)?(?:[a-zA-Z0-9_])+\.(?:jpg|jpeg|gif|png))$/", $schoolPicture)){
                        $res->isSuccess = false;
                        $res->code = 505;
                        $res->message = "첨부할수 없는 이미지입니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    if(!isRedundantNo($locationNo)){
                        $res->isSuccess = false;
                        $res->code = 500;
                        $res->message = "잘못된 형식의 입력값입니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    SignUp($email, $password, $name, $birth, $nickName, $schoolPicture, $schoolName,$locationNo);

                    $res->isSuccess = true;
                    $res->code = 100;
                    $res->message = "회원가입에 성공하였습니다.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
            }
            break;

        case "location":
            $res->result = locationList();
            $res->isSuccess = true;
            $res->code = 102;
            $res->message = "조회에 성공하였습니다.";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        case "login":
            $email = $req -> email;
            $password = $req -> password;

            if(isRedundantUser($email, $password)) {
                $jwt = getJWToken($email, $password, JWT_SECRET_KEY);
                $res->result->jwt = $jwt;
                $res->isSuccess = TRUE;
                $res->code = 101;
                $res->message = "로그인에 성공하였습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $res->isSuccess = FALSE;
                $res->code = 506;
                $res->message = "입력하신 이메일 또는 비밀번호가 잘못되었거나, 가입되어있는 정보가 없습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}