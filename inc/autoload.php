<?php
function  __autoload($nomClasse)
{
	//include_once a remplacer pour eviter la double declaration de classes
	require_once(dirname(__FILE__).'/../class/'.$nomClasse.'.class.php');

}

/* Code provenant de :  http://www.php.net/manual/fr/language.oop5.autoload.php */
?>