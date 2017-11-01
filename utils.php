<?php
require_once('config.php');

class Util {

	public static function getCharacterModuleData($type = 'characters', $con) {
		/** get search options **/
		$search_options_gender = array();
		$search_options_nation = array();
		$search_options_location = array();

		// Selecting by gender
		$selection = "SELECT DISTINCT gender FROM " . $type . " ORDER BY gender";
		$result = mysqli_query($con, $selection);
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$search_options_gender[] = $row["gender"];
		}

		// Selecting by nation
		$selection = "SELECT DISTINCT nation FROM " . $type . " ORDER BY nation";
		$result = mysqli_query($con, $selection);
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$search_options_nation[] = $row["nation"];
		}

		// Selecting by location
		$selection = "SELECT DISTINCT location FROM " . $type . " ORDER BY location";
		$result = mysqli_query($con, $selection);
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$search_options_location[] = $row["location"];
		}
		/** finished getting search options **/


		/** get playable characters **/ 
		$characters = array();

		$selection = "SELECT name,static_sprite FROM " . $type . " ORDER BY name";
		$result = mysqli_query($con, $selection);
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$characters[] = array(
				'name' => $row['name'],
				'static_sprite' => $row['static_sprite']
			);
		};
		/** finished getting playable characters **/

		mysqli_free_result($result);

		return array(
			'gender' => $search_options_gender, 
			'nation' => $search_options_nation, 
			'location' => $search_options_location, 
			'characters' => $characters
		);
	}

	public static function searchCharacters($con, $gender = '', $nation = '', $location = '', $sort = 'name') {
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

		return mysqli_query($con,$selection);
	}

	public static function getCharacterInfo($name, $type, $con) {
		$selection = 'SELECT * FROM ' . $type . ' WHERE name = "'.$name.'"';
		$result = mysqli_query($con,$selection);
		return mysqli_fetch_array($result, MYSQLI_ASSOC);
	}

	public static function getStatCoordinates($character) {
		$hp = $character['hp_rating'];
		$sp = $character['sp_rating'];
		$str = $character['str_rating'];
		$dex = $character['dex_rating'];
		$agi = $character['agi_rating'];
		$int = $character['int_rating'];

		if ($hp == 'A')
			$hp = '8,2';
		elseif ($hp == 'B')
			$hp = '8,3';
		elseif ($hp == 'C')
			$hp = '8,4';
		elseif ($hp == 'D')
			$hp = '8,5';
		elseif ($hp == 'E')
			$hp = '8,6';

		if ($sp == 'A')
			$sp = ' 14,5';
		elseif ($sp == 'B')
			$sp = ' 13,5.5';
		elseif ($sp == 'C')
			$sp = ' 12,6';
		elseif ($sp == 'D')
			$sp = ' 11,6.5';
		elseif ($sp == 'E')
			$sp = ' 10,7';

		if ($str == 'A')
			$str = ' 14,11';
		elseif ($str == 'B')
			$str = ' 13,10.5';
		elseif ($str == 'C')
			$str = ' 12,10';
		elseif ($str == 'D')
			$str = ' 11,9.5';
		elseif ($str == 'E')
			$str = ' 10,9';

		if ($dex == 'A')
			$dex = ' 8,14';
		elseif ($dex == 'B')
			$dex = ' 8,13';
		elseif ($dex == 'C')
			$dex = ' 8,12';
		elseif ($dex == 'D')
			$dex = ' 8,11';
		elseif ($dex == 'E')
			$dex = ' 8,10';
		elseif ($dex == 'S')
			$dex = ' 8,15';

		if ($agi == 'A')
			$agi = ' 2,11';
		elseif ($agi == 'B')
			$agi = ' 3,10.5';
		elseif ($agi == 'C')
			$agi = ' 4,10';
		elseif ($agi == 'D')
			$agi = ' 5,9.5';
		elseif ($agi == 'E')
			$agi = ' 6,9';
		elseif ($agi == 'S')
			$agi = ' 1,11.5';

		if ($int == 'A')
			$int = ' 2,5';
		elseif ($int == 'B')
			$int = ' 3,5.5';
		elseif ($int == 'C')
			$int = ' 4,6';
		elseif ($int == 'D')
			$int = ' 5,6.5';
		elseif ($int == 'E')
			$int = ' 6,7';

		if ($character['nation'] == 'Alerian Federation')
			$color = '#0000CC';
		elseif ($character['nation'] ==  'Gran Eszak Empire')
			$color = '#CC0000';
		else
			$color = '#00CC00';

		return array(
			'coordinates' => $hp . $sp .$str . $dex . $agi . $int,
			'color' => $color
		);
	}
}