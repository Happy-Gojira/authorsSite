<?php

if(isset($_POST["to_address"]))
        $to = $_POST["to_address"];
else
	{
	echo "404 error";
	exit;
	}

$subject = stripslashes($_POST["subject"]);
$message = stripslashes($_POST["body"]);






if(isset($_POST["from_address"]))
{

$from_address = stripslashes($_POST["from_address"]);
$from_name = stripslashes($_POST["from_name"]);

if(isset($_POST["use_local_domain"]))
{
         $l_domain = $_SERVER['SERVER_NAME'];
         
         $splitPos = strpos($l_domain, "www.");
         if ($splitPos !== false) {
         $l_domain = str_replace('www.', '', $l_domain);
          }

         $from_address = substr($from_address,  0, strpos($from_address, '@')) . '@' . $l_domain;
}


$header =  "MIME-Version: 1.0\r\n";
$header .= "Content-type: text/plain; charset=iso-8859-1\r\n";
$header .= 'From: ' . '"' . $from_name . '"' .  " <" . $from_address . ">\r\n";
$header .= "Reply-To: $from_address\r\n";
$result = mail(stripslashes($to), stripslashes($subject), stripslashes($message), stripslashes($header));
if($result){echo 'good';}else{echo 'error : '.$result;}
}
else
{
$result = mail(stripslashes($to), stripslashes($subject), stripslashes($message));
if($result){echo 'good';}else{echo 'error : '.$result;}
}

 
?>