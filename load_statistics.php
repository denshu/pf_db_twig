<?php
	require_once('config.php');
	$debug = false;
?>

<?php
	function convertRatingToInt($rating) {
		switch ($rating) {
			case 'S':
				return 6;
			case 'A':
				return 5;
			case 'B':
				return 4;
			case 'C':
				return 3;
			case 'D':
				return 2;
			case 'E':
				return 1;
			default:
				return 0;
		}
	}

	$stat_names = ['HP', 'SP', 'STR', 'DEX', 'AGI', 'INT'];

	class Character {
		public $name = '';
		public $location = '';
		public $gender = '';
		public $stats = ['HP' => '', 'SP' => '', 'STR' => '', 'DEX' => '', 'AGI' => '', 'INT' => ''];
		public $numeric_stats = ['HP' => 0, 'SP' => 0, 'STR' => 0, 'DEX' => 0, 'AGI' => 0, 'INT' => 0];
		public $description = '';
		public $sprite = '';
		public $static_sprite = '';

		public function __construct() {
		}

		public static function create() {
			$instance = new self();
			return $instance;
		}

		public function initializeFromDB($row) {
			$this->name = $row['name'];
			$this->location = $row['location'];
			$this->gender = $row['gender'];

			$this->stats['HP'] = $row['hp_rating'];
			$this->stats['SP'] = $row['sp_rating'];
			$this->stats['STR'] = $row['str_rating'];
			$this->stats['DEX'] = $row['dex_rating'];
			$this->stats['AGI'] = $row['agi_rating'];
			$this->stats['INT'] = $row['int_rating'];

			$this->numeric_stats['HP'] = convertRatingToInt($this->stats['HP']);
			$this->numeric_stats['SP'] = convertRatingToInt($this->stats['SP']);
			$this->numeric_stats['STR'] = convertRatingToInt($this->stats['STR']);
			$this->numeric_stats['DEX'] = convertRatingToInt($this->stats['DEX']);
			$this->numeric_stats['AGI'] = convertRatingToInt($this->stats['AGI']);
			$this->numeric_stats['INT'] = convertRatingToInt($this->stats['INT']);

			$this->descripion = $row['description'];
			$this->sprite = $row['sprite'];
			$this->static_sprite = $row['static_sprite'];
		}
	}

	class Faction {
		public $name = '';
		public $chart_name = '';
		public $stats = ['HP' => 0, 'SP' => 0, 'STR' => 0, 'DEX' => 0, 'AGI' => 0, 'INT' => 0];
		public $average_stats = ['HP' => 0, 'SP' => 0, 'STR' => 0, 'DEX' => 0, 'AGI' => 0, 'INT' => 0];
		public $strongest_char_by_type = ['HP' => '', 'SP' => '', 'STR' => '', 'DEX' => '', 'AGI' => '', 'INT' => ''];
		public $strongest_char_overall = '';
		public $characters = array();
		public $num_rows = 0;
		public $color = '';

		public function __construct($name, $chart_name, $color) {
			$this->name = $name;
			$this->chart_name = $chart_name;
			$this->color = $color;
		}

		public function averageStats() {
			if ($this->num_rows <= 0) {
				echo 'Cannot compute average stats for ' . $this->name . '.';
				return;
			}

			$this->average_stats = $this->stats;

			foreach ($this->stats as $total_stat => $total_rating) {
				$this->average_stats[$total_stat] = $total_rating / $this->num_rows;
			}
		}

		public function getStrongestCharByStat($stat) {
			$characters = $this->characters;
			usort($characters, function($a, $b) use($stat) {
			    if ($a->numeric_stats[$stat] == $b->numeric_stats[$stat]) {
			        return 0;
			    }
			    return $a->numeric_stats[$stat] < $b->numeric_stats[$stat] ? -1 : 1;
			});

			$strongest_char_by_stat = array_pop($characters);
			return $strongest_char_by_stat;
		}

		public function getStrongestOverallChar() {
			$characters = $this->characters;
			usort($characters, function($a, $b) {
			    if (array_sum($a->numeric_stats) == array_sum($b->numeric_stats)) {
			        return 0;
			    }
			    return array_sum($a->numeric_stats) < array_sum($b->numeric_stats) ? -1 : 1;
			});
			$strongest_char_overall = array_pop($characters);
			return $strongest_char_overall;
		}
	}

	$nansei = new Faction('Nansei Allied Union', 'Nansei', 'green');
	$gran = new Faction('Gran Eszak Empire', 'Gran Eszak', 'red');
	$aleria = new Faction('Alerian Federation', 'Aleria', 'blue');
	$unaffiliated = new Faction('Unaffiliated', 'Unaffiliated', 'yellow');

	$factions = array($nansei, $gran, $aleria, $unaffiliated);

	foreach ($factions as &$faction) {
		$selection = "SELECT * FROM characters WHERE nation = '" . $faction->name . "'";
		$result = mysqli_query($con, $selection);
		$faction->num_rows = mysqli_num_rows($result);
		do {
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			if ($row != NULL) { 
				$character = new Character;
				$character->initializeFromDB($row);
				$faction->characters[] = $character;

				$faction->stats['HP'] += convertRatingToInt($row['hp_rating']);
				$faction->stats['SP'] += convertRatingToInt($row['sp_rating']);
				$faction->stats['STR'] += convertRatingToInt($row['str_rating']);
				$faction->stats['DEX'] += convertRatingToInt($row['dex_rating']);
				$faction->stats['AGI'] += convertRatingToInt($row['agi_rating']);
				$faction->stats['INT'] += convertRatingToInt($row['int_rating']);
			}
		} while($row != NULL);

		$faction->averageStats();

		if ($debug) {
			echo $faction->name . '<br>';
			echo $faction->num_rows . '<br>';
			foreach ($faction->stats as $stat => $total_rating) {
				echo $stat . ' => ' . $total_rating . '<br>';
			}
			echo '<br>';
			foreach ($faction->average_stats as $stat => $average_rating) {
				echo $stat . ' => ' . $average_rating . '<br>';
			}
			echo '<br>';
			foreach ($faction->characters as $character) {
				echo '<img src="' . $character->static_sprite . '" />';
			}
			echo '<br><br>';
		}

		mysqli_free_result($result);
	}

	 for ($i = 0; $i < count($factions); $i++) { 
		foreach ($factions[$i]->stats as $stat => $rating) {
			$strongest_char = $factions[$i]->getStrongestCharByStat($stat);
			$factions[$i]->strongest_char_by_type[$stat] = $strongest_char;
		}
		$factions[$i]->strongest_char_overall = $factions[$i]->getStrongestOverallChar();
	 }
?>

<style type="text/css">
	/* Chart.js */
	@-webkit-keyframes chartjs-render-animation{from{opacity:0.99}to{opacity:1}}@keyframes chartjs-render-animation{from{opacity:0.99}to{opacity:1}}.chartjs-render-monitor{-webkit-animation:chartjs-render-animation 0.001s;animation:chartjs-render-animation 0.001s;}</style>
	<style>
	canvas {
	    -moz-user-select: none;
	    -webkit-user-select: none;
	    -ms-user-select: none;
	}
</style>

<div class="row placeholders statisticsModule">
	<h1 class="sub-header">Statistics</h1>
    <div id="container-fluid" style="width: 100%;"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
        <canvas id="canvas" width="1068" height="700" class="chartjs-render-monitor" style="display: block; width: 1068px; height: 700px;"></canvas>
    </div>
    <script>
    	window.chartColors = {
			red: 'rgb(255, 99, 132)',
			orange: 'rgb(255, 159, 64)',
			yellow: 'rgb(255, 205, 86)',
			green: 'rgb(75, 192, 192)',
			blue: 'rgb(54, 162, 235)',
			purple: 'rgb(153, 102, 255)',
			grey: 'rgb(201, 203, 207)'
		};
        var color = Chart.helpers.color;
        var barChartData = {
            labels: ["HP", "SP", "STR", "DEX", "AGI", "INT"],
            datasets: [
			<?php for ($i = 0; $i < count($factions); $i++) { ?>
			{
                label: '<?php echo $factions[$i]->chart_name; ?>',
                backgroundColor: color(window.chartColors.<?php echo $factions[$i]->color; ?>).alpha(0.5).rgbString(),
                borderColor: window.chartColors.<?php echo $factions[$i]->color; ?>,
                borderWidth: 1,
                data: [<?php echo implode(',', $factions[$i]->average_stats); ?>]
        	}, 
            <?php } ?>
        	]

        };
        // window.onload = function() {
        function generateChart() {
            var ctx = document.getElementById("canvas").getContext("2d");

            window.myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                options: {
                    responsive: true,
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Average Ratings By Faction (E = 1.0, D = 2.0, ..., A = 5.0)'
                    },
                    scales: {
				      yAxes: [{
				        ticks: {
				       		min: 1,
				       		max: 5,
				         	stepSize: 0.5
				        }
				      }]
				    }
                }
            });

        };
    </script>

    <div id="character-grid-container">
		<div class="character-grid">
		<?php
		// Creating the grid of characters

		echo '<h3>Highest Overall Rating</h3>';
		echo '<div class="row">';
		for ($i = 0; $i < count($factions); $i++) {
			?>
			<div class="col-xs-3 character character-playable character-playable-<?php echo $i; ?>" style="text-align:center" data-toggle="modal" data-target="#characterDetails">
				<h4 value="<?php echo $factions[$i]->strongest_char_overall->name; ?>"><?php echo $factions[$i]->strongest_char_overall->name; ?></h4>
				<img src="<?php echo $factions[$i]->strongest_char_overall->static_sprite; ?>" />
			</div>
			<?php
		}
		echo '</div>';

		foreach ($stat_names as $stat_name) {
			echo '<h3>Highest ' . $stat_name . '</h3>';
			echo '<div class="row">';
			for ($i = 0; $i < count($factions); $i++) {
				?>
				<div class="col-xs-3 character character-playable character-playable-<?php echo $i; ?>" style="text-align:center" data-toggle="modal" data-target="#characterDetails">
					<h4 value="<?php echo $factions[$i]->strongest_char_by_type[$stat_name]->name; ?>"><?php echo $factions[$i]->strongest_char_by_type[$stat_name]->name; ?></h4>
					<img src="<?php echo $factions[$i]->strongest_char_by_type[$stat_name]->static_sprite; ?>" />
				</div>
				<?php
			}
			echo '</div>';
		}
		?>
		</div>
	</div>
</div>