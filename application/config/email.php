<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['email_config1'] = array(
    'protocol' => 'smtp', // 'mail', 'sendmail', or 'smtp'
    'smtp_host' => 'smtp.gmail.com', 
    'smtp_port' => 465,
    'smtp_user' => 'autoupdates.tor_our.uplb@up.edu.ph',
    'smtp_pass' => 'gxsoqrnqqwihalmv',//'bpsuemsepthmumpz',
    'smtp_crypto' => 'ssl', //can be 'ssl' or 'tls' for example
    'mailtype' => 'html', //plaintext 'text' mails or 'html'
    'smtp_timeout' => '10', //in seconds
    'charset' => 'utf-8',
    'wordwrap' => TRUE,
);


$config['noreply_our'] = array(
    'protocol' => 'smtp', // 'mail', 'sendmail', or 'smtp'
    'smtp_host' => 'smtp.gmail.com', 
    'smtp_port' => 465,
    'smtp_user' => 'noreply_our.uplb@up.edu.ph',
    'smtp_pass' => 'cwdkywvmnlsbzjja',//'bpsuemsepthmumpz',
    'smtp_crypto' => 'ssl', //can be 'ssl' or 'tls' for example
    'mailtype' => 'html', //plaintext 'text' mails or 'html'
    'smtp_timeout' => '10', //in seconds
    'charset' => 'utf-8',
    'wordwrap' => TRUE,
);
