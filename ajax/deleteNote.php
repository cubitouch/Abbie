<?php

require_once(dirname(__FILE__)."/../inc/autoload.php");

$doc = new DOMDocument();
$doc->load(dirname(__FILE__)."/../notes.xml");
$noteList = $doc->getElementsByTagName("note");

foreach ($noteList as $noteXml){
	if ($noteXml->getAttribute("id") == $_GET['id'])
	{
		$note = new Note($noteXml);
		if ($note->deleteNote($noteList, $noteXml))
			echo('OK');
		else
			echo('KO');
		break;
	}
}

?>