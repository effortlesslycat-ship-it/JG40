<?php

require 'class.database.php';

require 'class.session.php';
// $session = new Session();

$text = 'Bia&amp;#322;ystok';
$texta = 'Bia&#322;ystok';
$text2 = strtoupper($texta);
$text2a = strtoupper($text);
$text3 = mb_strtoupper($texta, 'UTF-8');
$text4 = htmlentities(strtoupper(html_entity_decode($texta)));
$utf8_text = html_entity_decode($texta, ENT_QUOTES, 'utf-8');
$utf8_text2 = strtoupper($utf8_text);
$utf8_text3 = mb_strtoupper($utf8_text, 'UTF-8');

echo 'Original word using html entities=' . $text . ' = ' . $texta . '<BR>';
echo 'strtoupper=' . $text2a . ' = ' . $text2 . '<BR>';
echo 'mb_strtoupper=' . $text3 . '<BR>';
echo 'htmlentities(strtoupper(html_entity_decode=' . $text4 . '<BR><BR>';
echo 'html_entity_decode(original)=' . $utf8_text . '<BR>';
echo 'strtoupper=' . $utf8_text2 . '<BR>';
echo 'mb_strtoupper=' . $utf8_text3 . '<BR>';

?>

