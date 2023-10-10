<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "PHPMailer/src/Exception.php";
require "PHPMailer/src/PHPMailer.php";

$mail = new PHPMailer(true);

$mail->CharSet = "UTF-8";
$mail->IsHTML(true);

$name = $_POST["nameAddPhoto"];
$phone = $_POST["linkAddPhoto"];
$email_template = "template_mail.html";

$body = file_get_contents($email_template);
$body = str_replace('%name%', $name, $body);
$body = str_replace('%phone%', $phone, $body);

$mail->addAddress("alexandrobuslaev@gmail.com"); // Здесь введите Email, куда отправлять
$mail->setFrom($email);
$mail->Subject = "[Заявка с формы]";
$mail->MsgHTML($body);

if (!$mail->send()) {
  $message = "Ошибка отправки";
} else {
  $message = "Данные отправлены!";
}

$response = ["message" => $message];

header('Content-type: application/json');
echo json_encode($response);


?>