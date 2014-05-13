<?php
// FileName: tts.php
/*
 *  A PHP Class that converts Text into Speech using Google's Text to Speech API
 *
 * Author:
 * Abu Ashraf Masnun
 * http://masnun.com
 *
 */

class Tts {
	private $mp3data;
	
	function __construct($text="", $path = "")
	{
		$this->setText($text);
		$this->saveToFile($path);
	}
	
	function setText($text)
	{
		$text = rawurlencode(str_replace("\\","",trim($text)));
		if(!empty($text))
		{
			$this->mp3data = file_get_contents('http://translate.google.com/translate_tts?ie=UTF-8&tl=fr&q='.$text);
			return true;
		}
		else
		{
			return false;
		}
	}

	function saveToFile($filename)
	{
		$filename = trim($filename);
		if(!empty($filename))
			return(file_put_contents($filename,$this->mp3data));
		else
			return false;
	}

}
?>