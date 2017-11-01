<?php
require_once('requires.php');

$name = filter_var($_POST['character_name'], FILTER_SANITIZE_STRING);

$character = Util::getCharacterInfo($name, 'characters', $con);
$stat_coordinates = Util::getStatCoordinates($character);

$browser_check = strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== FALSE || strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== FALSE;

try {
	$loader = new Twig_Loader_Filesystem('template');
	$twig = new Twig_Environment($loader);
	$template = $twig->loadTemplate('info_character.html.twig');

	echo $template->render(array(
		'character' => $character,
		'stat_coordinates' => $stat_coordinates,
		'browser_check' => $browser_check
	));
} catch (Exception $e) {
	die ('error: ' . $e->getRawMessage() . ' at line ' . $e->getTemplateLine());
}