<?php
include("functions.php");
include("config.php");
require_once('mailer/class.phpmailer.php');
guarda_db_contacto();
$config = get_config();

if(empty($_POST))
	die();
	
$rows;
foreach($_POST as $key=>$item){
	$key = ucfirst($key);
	$rows .= "<tr><td><strong>$key</strong></td><td>$item</td></tr>";
}

$message = <<<EOF
<html>
	<head></head>
	<body>
		<table border="0" width="100%">
			<tr>
				<td colspan="2">
					<h1>Contacto recibido:</h1>
				</td>
			</tr>
			$rows
		</table>
	</body>
</html>

EOF;

//phpmailer
$mail = new PHPMailer(); // defaults to using php "mail()"

if($config['phpmailer']){
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
	$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
	$mail->Port       = 465;                   // set the SMTP port for the GMAIL server
	$mail->Username   = "support@holdasociados.com";  // GMAIL username
	$mail->Password   = "hold123";            // GMAIL password	
}

$mail->CharSet = 'UTF-8';
//fin phpmailer

$mail->SetFrom('support@holdasociados.com', $config['from_name']);
$to_array = explode(",", $config['to']);
foreach($to_array as $item){
	$mail->AddAddress(trim($item));
}

if(isset($config['bcc'])){
	$bcc_array = explode(",", $config['bcc']);
	foreach($bcc_array as $item){
		if(!empty($item))
			$mail->AddBCC(trim($item));
	}	
}
$mail->Subject = $config['subject'];
$mail->MsgHTML($message);
$sent = $mail->Send();

if($sent)
	echo json_encode(array('msj'=>'OK','url'=>$config['gracias']));
else
	echo json_encode(array('msj'=>'NO','url'=>'#'));
?>