<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader (created by composer, not included with PHPMailer)
require_once '../../Librerias/Mailer/Exception.php';
require_once '../../Librerias/Mailer/PHPMailer.php';
require_once '../../Librerias/Mailer/SMTP.php';

function enviarCorreo($destinatario, $Equipo, $N_Ambiente, $Estacion, $idfalla, $coordinacion){

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = 0;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'centrodecontroldefallas@gmail.com';                     //SMTP username
        $mail->Password   = 'cgsn rzvv wect wpfr';                      //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;                //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('centrodecontroldefallas@gmail.com', 'CCF');
        $mail->addAddress($destinatario);     //Add a recipient
        /*$mail->addAddress('ellen@example.com');               //Name is optional
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');*/

        //Attachments
        /* $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name*/

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Nuevo reporte de falla | ID #' . $idfalla;
        $mail->Body    = 'Estimado equipo de coordinación <strong>' . htmlspecialchars($coordinacion) . '</strong>,<br><br>' .
        'Se ha registrado una nueva falla en sus instalaciones. A continuación, los detalles:<br><br>' .
        '<ul>' .
        '<li><strong>ID de falla:</strong> ' . htmlspecialchars($idfalla) . '</li>' .
        '<li><strong>Equipo:</strong> ' . htmlspecialchars($Equipo) . '</li>' .
        '<li><strong>Número de ambiente:</strong> ' . htmlspecialchars($N_Ambiente) . '</li>' .
        '<li><strong>Estación:</strong> ' . htmlspecialchars($Estacion) . '</li>' .
        '</ul>' .
        'Por favor, procedan a tomar las acciones correspondientes a la brevedad posible.<br><br>' .
        'Gracias por su atención.<br>' .
        '<em>— Centro de Control de Fallas</em>';
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        return true;
        //echo 'Mensaje enviado';
    } catch (Exception $e) {
        echo "Error: {$mail->ErrorInfo}";
    }
}

function cerrarCorreo($destinatario, $Equipo, $N_Ambiente, $Estacion, $tecnico , $idFalla, $coordinacion){

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = 0;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'centrodecontroldefallas@gmail.com';                     //SMTP username
        $mail->Password   = 'cgsn rzvv wect wpfr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;                               //SMTP password
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('centrodecontroldefallas@gmail.com', 'CCF');
        $mail->addAddress($destinatario);     //Add a recipient
        /*$mail->addAddress('ellen@example.com');               //Name is optional
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');*/

        //Attachments
        /* $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name*/

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Falla finalizada | ID #' . $idFalla;
        $mail->Body  = 'Estimado equipo de coordinación<strong> ' . htmlspecialchars($coordinacion) . '</strong>,<br><br>' .
        'La siguiente falla ha sido finalizada exitosamente en sus instalaciones:<br><br>' .
        '<ul>' .
        '<li><strong>ID de falla:</strong> ' . htmlspecialchars($idFalla) . '</li>' .
        '<li><strong>Equipo:</strong> ' . htmlspecialchars($Equipo) . '</li>' .
        '<li><strong>Número de ambiente:</strong> ' . htmlspecialchars($N_Ambiente) . '</li>' .
        '<li><strong>Estación:</strong> ' . htmlspecialchars($Estacion) . '</li>' .
        '<li><strong>Técnico responsable:</strong> ' . htmlspecialchars($tecnico) . '</li>' .
        '</ul>' .
        'Para más información o seguimiento, puede consultar el sistema de reportes.<br><br>' .
        'Gracias por su colaboración.<br>' .
        '<em>— Sistema de Gestión de Fallas</em>';
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        return true;
        //echo 'Mensaje enviado';
    } catch (Exception $e) {
        echo "Error: {$mail->ErrorInfo}";
    }
}

?>