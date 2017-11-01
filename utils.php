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
}