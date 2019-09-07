<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'function.php';

require "./PHPMailer/src/PHPMailer.php";
require "./PHPMailer/src/SMTP.php";
require "./PHPMailer/src/Exception.php";


const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";
$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "sendMail":
            $email = $req->email;
            $blank = ' ';

            if (!isset($email) || empty($email) || !strpos($email, $blank) == false) {
                $res->isSuccess = false;
                $res->code = 500;
                $res->message = "잘못된 형식의 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)) {
                $res->isSuccess = false;
                $res->code = 500;
                $res->message = "잘못된 형식의 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!isRightEmail($email)) {
                $res->isSuccess = false;
                $res->code = 510;
                $res->message = "해당하는 유저가 존재하지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);

                return;
            }

            $emailToken = getEmailToken($email, JWT_SECRET_KEY);

            $mail = new PHPMailer(true);
            try {

                // 서버세팅
                $mail->SMTPDebug = 2;    // 디버깅 설정
                $mail->isSMTP();        // SMTP 사용 설정

                $mail->Host = "smtp.naver.com";                // email 보낼때 사용할 서버를 지정
                $mail->SMTPAuth = true;                        // SMTP 인증을 사용함
                $mail->Username = "wogur1598";    // 메일 계정
                $mail->Password = "rl!dk!1059";                // 메일 비밀번호
                $mail->SMTPSecure = "ssl";                    // SSL을 사용함
                $mail->Port = 465;                            // email 보낼때 사용할 포트를 지정
                $mail->CharSet = "utf-8";                        // 문자셋 인코딩

                // 보내는 메일
                $mail->setFrom("wogur1598@naver.com", "HereThere");

                // 받는 메일
                $mail->addAddress($email, "Receiver");
//    $mail -> addAddress("yinglong200@naver.com", "receive02");

                // 첨부파일
//    $mail -> addAttachment("./test.zip");
//    $mail -> addAttachment("./anjihyn.jpg");
                // 메일 내용
                $mail->isHTML(true);                                               // HTML 태그 사용 여부
                $mail->Subject = "HereThere 임시 비밀번호 발급용 인증 링크 메일입니다.";              // 메일 제목
                $mail->Body = "HereThere 임시 비밀번호 발급용 인증 링크 메일입니다.<br/>\n<br/>\n<br/>\n아래 링크를 클릭하면 임시 비밀번호를 발급해줍니다.<br/>\n<br/>\n<br/>\n
http://52.79.198.51/mail/password?token=$emailToken<br/>\n<br/>\n<br/>\n감사합니다.";    // 메일 내용

                // Gmail로 메일을 발송하기 위해서는 CA인증이 필요하다.
                // CA 인증을 받지 못한 경우에는 아래 설정하여 인증체크를 해지하여야 한다.
                $mail->SMTPOptions = array(
                    "ssl" => array(
                        "verify_peer" => false
                    , "verify_peer_name" => false
                    , "allow_self_signed" => true
                    )
                );

                // 메일 전송
                $mail->send();
//                echo "Message has been sent";

            } catch (Exception $e) {
                $res->isSuccess = false;
                $res->code = 513;
                $res->message = "이메일 발송에 실패하였습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
//                echo "Message could not be sent. Mailer Error : ", $mail->ErrorInfo;
            }
            $res->isSuccess = true;
            $res->code = 107;
            $res->message = "임시 비밀번호 발급 이메일 전송에 성공하였습니다.";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        case "openMail":
            $email = $req->email;
            $blank = ' ';
            $emailToken = $_GET['token'];

            if (!isset($email) || empty($email) || !strpos($email, $blank) == false) {
                $res->isSuccess = false;
                $res->code = 500;
                $res->message = "잘못된 형식의 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)) {
                $res->isSuccess = false;
                $res->code = 500;
                $res->message = "잘못된 형식의 입력값입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            if (!isRedundantEmail($email)) {
                $res->isSuccess = false;
                $res->code = 510;
                $res->message = "해당하는 유저가 존재하지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);

                return;
            }

            $tokenEmail = getEmailByToken($emailToken, JWT_SECRET_KEY);

            if ($email != $tokenEmail) {
                $res->isSuccess = false;
                $res->code = 512;
                $res->message = "이메일 인증에 실패하였습니다. 인증 URL 또는 이메일이 잘못되었습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);

                return;
            }

            $temporaryPassword = generateRandomString(7);

            updatePassword($email,$temporaryPassword);

            $res->isSuccess = true;
            $res->code = 108;
            $res->message = "임시 비밀번호는 ".$temporaryPassword."입니다. 로그인 후 비밀번호를 변경해주시기 바랍니다.";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;



    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}






