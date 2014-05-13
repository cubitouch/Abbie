<?php

require_once(dirname(__FILE__)."/../inc/autoload.php");

$note = new Note(false, $_GET['title'], $_GET['text']);
if ($note->getState())
	echo('OK');
else
	echo('KO');

?>