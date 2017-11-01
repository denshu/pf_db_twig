<?php 

require_once('requires.php');

$gender = filter_var($_POST['select_gender'], FILTER_SANITIZE_STRING);
$nation = filter_var($_POST['select_nation'], FILTER_SANITIZE_STRING);
$location = filter_var($_POST['select_location'], FILTER_SANITIZE_STRING);

$characters = Util::searchCharacters($con, $gender, $nation, $location);

try {
    $loader = new Twig_Loader_Filesystem('template');
    $twig = new Twig_Environment($loader);
    $template = $twig->loadTemplate('character_grid.html.twig');

    echo $template->render(array(
        'characters' => $characters,
        'type' => 'npc'
    ));
} catch (Exception $e) {
    die ('error: ' . $e->getRawMessage() . ' at line ' . $e->getTemplateLine());
}