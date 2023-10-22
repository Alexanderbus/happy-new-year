<?php
require "includes/PHPMailer.php";
require "includes/SMTP.php";
require "includes/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer();

$data = json_decode(file_get_contents('php://input'), true);

$name = $data["nameAddPhoto"] || null;
$phone = $data["linkAddPhoto"] || null;
$comment = $data["comment"] || null;
$email_template = "template_mail.html";

$body = "
<!DOCTYPE html
PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html style='width:100%;font-family:arial, ' helvetica neue', helvetica,
sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0;'>

<head>
<meta charset='UTF-8'>
<meta content='width=device-width, initial-scale=1' name='viewport'>
<meta name='x-apple-disable-message-reformatting'>
<meta http-equiv='X-UA-Compatible' content='IE=edge'>
<meta content='telephone=no' name='format-detection'>
<title>Новая заявка!</title>
<!--[if (mso 16)]>
	<style type='text/css'>
	a {text-decoration: none;}
	</style>
	<![endif]-->
<!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]-->
<style>
	#outlook a {
		padding: 0;
	}

	.ExternalClass {
		width: 100%;
	}

	.ExternalClass,
	.ExternalClass p,
	.ExternalClass span,
	.ExternalClass font,
	.ExternalClass td,
	.ExternalClass div {
		line-height: 100%;
	}

	.es-button {
		mso-style-priority: 100 !important;
		text-decoration: none !important;
	}

	a[x-apple-data-detectors] {
		color: inherit !important;
		text-decoration: none !important;
		font-size: inherit !important;
		font-family: inherit !important;
		font-weight: inherit !important;
		line-height: inherit !important;
	}

	@-ms-viewport {
		width: device-width;
	}
</style>
</head>

<body style='width:100%;font-family:arial, ' helvetica neue', helvetica,
sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0;'>
<div class='es-wrapper-color' style='background-color:#F6F6F6;'>
	<!--[if gte mso 9]>
	<v:background xmlns:v='urn:schemas-microsoft-com:vml' fill='t'>
			<v:fill type='tile' src='' color='#f6f6f6'></v:fill>
	</v:background>
<![endif]-->
	
  <ul>
    <li>Имя: $name</li>
    <li>Телефон: $phone</li>
	<li>Комент: $comment</li>
  </ul>
</div>
</body>
";

$altbody = "Имя: $name, телефон: $phone, Комент: $comment";

header('Content-type: application/json');
try {
  $mail->isSMTP();
  $mail->Host = "smtp.gmail.com"; // TODO: заполнить здесь
  $mail->SMTPAuth = true;
  $mail->Username = "adventproject1@gmail.com"; // * и здесь
  $mail->Password = "jjriucrirsvtrjhu"; // * и ещё здесь
  $mail->SMTPSecure = "ssl";
  $mail->Port = 465;

  //Recipients
  $mail->setFrom("adventproject1@gmail.com");
  $mail->addAddress("alexandrobuslaev@gmail.com");

  $mail->CharSet = 'UTF-8';
  $mail->Encoding = 'base64';
  // Content
  $mail->isHTML(true);
  $mail->Subject = "[Заявка с формы]";
  $mail->Body = $body;
  $mail->AltBody = $altbody;

  $mail->send();

  $response = json_encode(["success" => true, "message" => "Данные отправлены"]);
  echo $response;
  
} catch (Exception $e) {
  $response = json_encode(["success" => false, "mailMessage" => "{$mail->ErrorInfo}", "message" => "{$e->getMessage()}"]);
  echo $response;
  // file_put_contents('mail_errors.log', date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n" . $mail->ErrorInfo . "\n\n", FILE_APPEND);
}
