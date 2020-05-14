<?php
    require_once "includes.php";

    require 'phpmailer/PHPMailer.php';
    require 'phpmailer/SMTP.php';
    require 'phpmailer/Exception.php';


    $db_volleybet = new DBWorker(NULL);

    $textError = NULL;

    if (!(isset($_SESSION['id']))) {
        $code = 101;
        $data_post = $_POST;
        if (isset($data_post['restore-submit'])) {
            if (isCorrectFieldPost($data_post, $login, 'login')) {
                if ($db_volleybet->isUserExists($login)) {
                    $userData = $db_volleybet->getUserEmailByLogin($login);
                    $email = $userData['email'];

                    try {
                        $mail = new PHPMailer\PHPMailer\PHPMailer();

                        $newPass = getGeneratedPass();

                        $mail->isSMTP();
                        $mail->CharSet = 'UTF-8';
                        $mail->SMTPAuth = true;

                        $mail->Host = 'smtp.mail.ru';
                        $mail->Username = 'volley.bet@mail.ru';
                        $mail->Password = '88776655a';
                        $mail->SMTPSecure = 'ssl';
                        $mail->Port = 465;
                        $mail->setFrom('volley.bet@mail.ru', 'VolleyBet');

                        $mail->addAddress("$email");

                        $mail->isHTML(true);

                        $mail->Subject = 'VolleyBet: Restore password.';
                        $mail->Body = "<b>Login:</b> $login <br>
                                       <b>New password:</b> $newPass<br><br>
                                       <b>After logging into your account, we strongly recommend that you change your password!</b>";

                        if ($mail->send()) {
                            $data = [
                                'login' => $login,
                                'password' => password_hash($newPass, PASSWORD_DEFAULT),
                            ];
                            $db_volleybet->updatePasswordByLogin($data);
                            $code = 104;

                        } else {
                            $code = 400;
                        }

                    } catch (Exception $e) {
                        $textError = $mail->ErrorInfo;
                        $code = 400;
                    }


                } else {
                    $code = 500;
                }   
            }
        }
    } else {
        $code = 102;
    }

    $result['result'] = getSettingsByCode($code, $textError);
    echo $twig->render('restore.html', $result);

?>