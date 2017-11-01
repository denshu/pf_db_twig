<?php
require_once('requires.php');

$name = filter_var($_POST['character_name'], FILTER_SANITIZE_STRING);

$character = Util::getCharacterInfo($name, 'npcs', $con);

try {
	$loader = new Twig_Loader_Filesystem('template');
	$twig = new Twig_Environment($loader);
	$template = $twig->loadTemplate('info_npc.html.twig');

	echo $template->render(array(
		'character' => $character
	));
} catch (Exception $e) {
	die ('error: ' . $e->getRawMessage() . ' at line ' . $e->getTemplateLine());
}