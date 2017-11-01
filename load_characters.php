<?php
require_once('requires.php');

$characterModuleData = Util::getCharacterModuleData('characters', $con);

try {
	$loader = new Twig_Loader_Filesystem('template');
	$twig = new Twig_Environment($loader);
	$template = $twig->loadTemplate('module_characters.html.twig');

	echo $template->render(array(
		'search_options_gender' => $characterModuleData['gender'],
		'search_options_nation' => $characterModuleData['nation'],
		'search_options_location' => $characterModuleData['location'],
		'characters' => $characterModuleData['characters'],
		'type' => 'playable'
	));
} catch (Exception $e) {
	die ('error: ' . $e->getRawMessage() . ' at line ' . $e->getTemplateLine());
}