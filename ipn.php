<?php
$file = fopen("/tuto/paiement/retour.log", "a");
fwrite($file,"Debut ipn.php"."\r\n");
fclose($file);

// Adresse e-mail du vendeur
$email_vendeur = "vdeng-vendeur@free.fr";

// Envoi des infos a Paypal
$req = "cmd=_notify-validate";
foreach ($_POST as $key => $value) {
    $value = urlencode(stripslashes($value));
    $req.= "&$key=$value";
}

$file = fopen("/tuto/paiement/retour.log", "a");
fwrite($file,"AVANT curl_init"."\r\n");
fclose($file);

$fp = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
curl_setopt($fp, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($fp, CURLOPT_POST, 1);
curl_setopt($fp, CURLOPT_RETURNTRANSFER,1);
curl_setopt($fp, CURLOPT_POSTFIELDS, $req);
curl_setopt($fp, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($fp, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($fp, CURLOPT_FORBID_REUSE, 1);
curl_setopt($fp, CURLOPT_HTTPHEADER, array('Connection: Close'));

$file = fopen("/tuto/paiement/retour.log", "a");
fwrite($file,"AVANT IF"."\r\n");
fclose($file);

if( !($res = curl_exec($fp)) ) {
    curl_close($fp);
    exit;
}
curl_close($fp);
// Le paiement est validé
if (strcmp(trim($res), "VERIFIED") == 0) {

    $file = fopen("/tuto/paiement/retour.log", "a");
    fwrite($file,"VERIFIED"."\r\n");
    fclose($file);
   
} else {

     $file = fopen("/tuto/paiement/retour.log", "a");
    fwrite($file,"email"."\r\n");
    fclose($file);
    // Le paiement est invalide, envoi d'un mail au vendeur
    $from = "From: vdeng-vendeur@free.fr";
    $to = "dimitri.tchomnou@os-masconsulting.com";
    $sujet = "Paiement invalide";
    $body = $req;
    foreach ($_POST as $key => $value) {
        $text.= $key . " = " .$value ."nn";
    }
    mail($to, $sujet, $text . "nn" . $body, $from);
}
?>