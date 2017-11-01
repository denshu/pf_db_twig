<?php 

require_once('config.php');

$gender = filter_var($_POST['select_gender'], FILTER_SANITIZE_STRING);
$nation = filter_var($_POST['select_nation'], FILTER_SANITIZE_STRING);
$location = filter_var($_POST['select_location'], FILTER_SANITIZE_STRING);
$sort = filter_var($_POST['sort_by'], FILTER_SANITIZE_STRING);

if ($gender !== '' && $nation !== '' && $location !== '')
    $selection = 'SELECT name,static_sprite FROM characters WHERE gender = "'.$gender.'" AND nation = "'.$nation.'" AND location = "'.$location.'" ORDER BY '.$sort;            

if ($gender !== '' && $nation !== '' && $location == '')
    $selection = 'SELECT name,static_sprite FROM characters WHERE gender = "'.$gender.'" AND nation = "'.$nation.'" ORDER BY '.$sort;

if ($gender !== '' && $nation == '' && $location !== '')
    $selection = 'SELECT name,static_sprite FROM characters WHERE gender = "'.$gender.'" AND location = "'.$location.'" ORDER BY '.$sort; 

if ($gender !== '' && $nation == '' && $location == '')
    $selection = 'SELECT name,static_sprite FROM characters WHERE gender = "'.$gender.'" ORDER BY '.$sort;

if ($gender == '' && $nation !== '' && $location !== '')
    $selection = 'SELECT name,static_sprite FROM characters WHERE nation = "'.$nation.'" AND location = "'.$location.'" ORDER BY '.$sort;

if ($gender == '' && $nation !== '' && $location == '')
    $selection = 'SELECT name,static_sprite FROM characters WHERE nation = "'.$nation.'" ORDER BY '.$sort;

if ($gender == '' && $nation == '' && $location !== '')
    $selection = 'SELECT name,static_sprite FROM characters WHERE location = "'.$location.'" ORDER BY '.$sort;

if ($gender == '' && $nation == '' && $location == '')
    $selection = 'SELECT name,static_sprite FROM characters ORDER BY '.$sort;
   
//$selection = 'SELECT name,static_sprite FROM characters WHERE gender = "'.$gender.'" ORDER BY '.$sort;   
$result = mysqli_query($con,$selection);

echo '<div class="character-grid">';
do {
    echo '<div class="row">';
    for ($x = 0; $x <= 3; $x++) {
        $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
        if ($row != NULL) {
            echo '<div class="col-xs-3 character character-playable" style="text-align:center" data-toggle="modal" data-target="#characterDetails">';
            echo '<h4 value="',$row["name"],'">',$row["name"],'</h4>';
            echo '<img src="',$row["static_sprite"],'" />';
            echo '</div>';
        }
    }
    echo '</div>';
} while($row != NULL);
echo '</div>';
mysqli_free_result($result);

?>