<?php

require_once  OW::getPluginManager()->getPlugin('base')->getClassesDir() . 'mail.php';

$sitename = OW::getConfig()->getValue('base', 'site_name');
$siteemail = OW::getConfig()->getValue('base', 'site_email');
$mail = OW::getMailer()->createMail();
$mail->addRecipientEmail('purushoth.r@gmail.com');
$mail->setSender($siteemail, $sitename);
$mail->setSubject("Virtual Site Tour");
$mail->setHtmlContent("Plugin installed");
$mail->setTextContent("Plugin installed");
OW::getMailer()->addToQueue($mail);