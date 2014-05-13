<?php

class Note
{
	
	private $title;
	private $text;
	private $id;
	private $audioPath;
	
	private $state;
	
	
	function __construct($loaderXml = false, $title = "", $text = "")
	{
		if (!$loaderXml)
		{
			//remplissage de l'objet
			$this->setTitle(trim($title));
			$this->setId($this->findIdFromXmlFile());
			$this->generateAudioPath();
			$this->setText(trim($text), true);
			
			
			//modification du fichier XML
			$this->state = $this->saveNote();
		}
		else
		{
			$this->state = $this->loadNote($loaderXml);
		}
	}
	
	function generateAudioPath()
	{
		$this->setAudioPath("files/".$this->getId().".mp3");
	}
	
	function findIdFromXmlFile()
	{
		$doc = new DOMDocument();
		$doc->load(dirname(__FILE__)."/../notes.xml");
		$noteList = $doc->getElementsByTagName("note");
		
		$id = 0;
		foreach ($noteList as $note)
			if ($id < (int)($note->getAttribute("id")))
				$id = (int)($note->getAttribute("id"));
		
		return ($id + 1);
	}
	
	function saveNote()
	{
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		$doc->load(dirname(__FILE__)."/../notes.xml");
		$noteList = $doc->getElementsByTagName("note");
		$note = $doc->createElement("note");
		$note->setAttribute("path", $this->getAudioPath());
		$note->setAttribute("title", $this->getTitle());
		$note->setAttribute("text", $this->getText());
		$note->setAttribute("id", $this->getId());
		$doc->getElementsByTagName("notes")->item(0)->appendChild($note);
		$doc->normalizeDocument();
		return ($doc->save(dirname(__FILE__)."/../notes.xml"));
	}
	
	function loadNote($note)
	{
		$this->setAudioPath($note->getAttribute("path"));
		$this->setTitle($note->getAttribute("title"));
		$this->setText($note->getAttribute("text"));
		$this->setId($note->getAttribute("id"));
		return (true);
	}
	
	function deleteNote($noteList, $note)
	{
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		$doc->load(dirname(__FILE__)."/../notes.xml");
		$noteList = $doc->getElementsByTagName("note");
		foreach ($noteList as $note)
		{
			if ($note->getAttribute("id") == $this->getId())
			{
				unlink(dirname(__FILE__)."/../".$this->getAudioPath());
				$doc->getElementsByTagName("notes")->item(0)->removeChild($note);
				$doc->normalizeDocument();
				return ($doc->save(dirname(__FILE__)."/../notes.xml"));
			}
		}
	}
	
	function getAudioPath()
	{
		return ($this->audioPath);
	}
	function getText()
	{
		return ($this->text);
	}
	function getTitle()
	{
		return ($this->title);
	}
	function getId()
	{
		return ($this->id);
	}
	function getState()
	{
		return ($this->state);
	}
	
	function setAudioPath($value)
	{
		$this->audioPath = $value;
	}
	function setText($value, $isNew = false)
	{
		$this->text = $value;
		if ($isNew)
		{
			//création du fichier audio
			$tts = new Tts($value, dirname(__FILE__). "/../" .utf8_decode($this->getAudioPath()));
		}
	}
	function setTitle($value)
	{
		$this->title = $value;
	}
	function setId($value)
	{
		$this->id = $value;
	}
}

?>