<?php

/*
 * http://weston.ruter.net/projects/google-tts/
 * 
 * 
 * 
 * 
 */

require_once(dirname(__FILE__)."/inc/autoload.php");

function getReadAllJSFunction($notes, $i = 0)
{
	$function = "";
	if ($notes->item($i) == null)
		return ($function);
	$note = new Note($notes->item($i));
	
	$function .= "
							note = $(list).find('li:eq('+ ($i+1) +')');
							note.toggleClass('ui-btn-active');
							soundManager.createSound('note".$note->getId()."','".$note->getAudioPath()."');
							soundManager.play('note".$note->getId()."',{
								onfinish: function() {
									note.removeClass('ui-btn-active');";
	if ($notes->item($i) != null)
		$function .= getReadAllJSFunction($notes, $i+1);
	$function .= "
								}
							});
						";
	return ($function);
}

$doc = new DOMDocument();
$doc->load(dirname(__FILE__)."/notes.xml");
$noteList = $doc->getElementsByTagName("note");
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Abbie - FR</title>
		<link href="css/default.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0a4.1/jquery.mobile-1.0a4.1.min.css" type="text/css" />
		<script src="http://code.jquery.com/jquery-1.5.2.min.js"></script>
		<script src="http://code.jquery.com/mobile/1.0a4.1/jquery.mobile-1.0a4.1.min.js"></script>
		
		<script type="text/javascript" src="js/soundmanager2/soundmanager2-jsmin.js"></script>
		<script>
			soundManager.url = 'js/soundmanager2/swf/';
			soundManager.useFlashBlock = false;
			soundManager.debugMode = false;
			
			//soundManager.flashVersion = 9; // optional: shiny features (default = 8)
			// enable HTML5 audio support, if you're feeling adventurous. iPad/iPhone will always get this.
			// soundManager.useHTML5Audio = true;
			
				
		</script>
		
		
		<script type="text/javascript">
			function readNote(referer, id)
			{
				$(referer).toggleClass('ui-btn-active');
				<?php
					$first = true;
					foreach ($noteList as $noteXml)
					{
						$note = new Note($noteXml);
						
						if ($first)
						{
							echo("if (id == ".$note->getId().")
							{
								soundManager.createSound('note".$note->getId()."','".$note->getAudioPath()."');
								soundManager.play('note".$note->getId()."',{
									onfinish: function() {
										$(referer).removeClass('ui-btn-active');
									}
								});
							}
							");
							$first = false;
						}
						else
						{
							echo("else if (id == ".$note->getId().")
							{
								soundManager.createSound('note".$note->getId()."','".$note->getAudioPath()."');
								soundManager.play('note".$note->getId()."',{
									onfinish: function() {
										$(referer).removeClass('ui-btn-active');
									}
								});
							}
							");
						}
					}
				?>
			}
			function readAll()
			{
				var list = $('#listNote');
				var note = null;
				<?php
					echo(getReadAllJSFunction($noteList));
				?>
			}
			function addNote(title, text)
			{
				$.get('./ajax/addNote.php?title='+title+'&text='+text, function(data){
					if (data == 'OK')
						location.reload();
					else
						$("#addError").click();
				});
			}
			function deleteNote(id)
			{
				$.get('./ajax/deleteNote.php?id='+id, function(data){
					if (data == 'OK')
						location.reload();
					else
						$("#deleteError").click();
				});
			}
		</script>
	</head>
	<body>
	<div  data-role="page" data-theme="b"> 
		<div data-role="header">Abbie</div>
		<div data-role="content">
			<ul data-role="listview" id="newNote" data-inset="true" data-theme="c"> 
				<li data-role="listdiviser">Ajouter une note</li>
				<li data-role="fieldcontain"> 
					<label for="title">Titre de la note :</label>
					<input type="text" name="title" id="title"/>
				</li> 
				<li data-role="fieldcontain"> 
					<label for="text">Texte de la note :</label>
					<input type="text" name="text" id="text"/>
				</li>
			</ul>
			<button data-icon="add" onclick="addNote($('#newNote #title').val(),$('#newNote #text').val());">Ajouter</button>
			
			<button onclick="readAll();" data-icon="gear">Lire vos notes</button>
			<ul data-role="listview" id="listNote" data-inset="true" data-theme="d"> 
				<li data-role="listdiviser" data-theme="c">Les notes</li>
				<?php
					if ($noteList->length > 0)
					{
						foreach ($noteList as $noteXml)
						{
							$note = new Note($noteXml);
							
							echo('
							<li onclick="readNote(this,'.$note->getId().');">
								<h3>'.$note->getTitle().'</h3>
								<p>'.$note->getText().'</p>
							</li>');
						}
					}
					else
					{
							echo('
							<li>
								Aucune note pour le moment...
							</li>');
					}
				?>
			</ul>
			<?php
				if ($noteList->length > 0)
				{
					echo('
			<ul data-role="listview" id="newNote" data-inset="true">
				<li data-role="listdiviser" data-theme="a">Supprimer une note</li>
				<li data-role="fieldcontain"> 
					<label for="selectNote">Titre de la note :</label>
					<select id="selectNote">
					');
					
					$first = true;
					foreach ($noteList as $noteXml)
					{
						$note = new Note($noteXml);
						echo('<option value="'.$note->getId().'"');
						if ($first)
						{
							echo(' selected="selected" ');
							$first = false;
						}
						echo('>'.$note->getTitle().'</option>');
					}
					
					echo('
					</select>
				</li> 
			</ul>
			<button data-icon="delete" onclick="deleteNote($(\'#selectNote\').val());">Supprimer</button>
					');
				}
			?>
			<a href="error/addError.html" data-rel="dialog" data-transition="pop" id="addError"></a>
			<a href="error/deleteError.html" data-rel="dialog" data-transition="pop" id="deleteError"></a>
		</div>
	</div>
	</body>
</html>