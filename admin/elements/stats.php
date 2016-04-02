<h1>Статистика</h1>

<div class="element" id="plot">
<?php
    if (empty($_GET['period'])) $_GET['period'] = "hour";
    switch($_GET['period']) {
        case "hour": gen_plot($mysqli, "1 DAY", $_GET['period']); break;
        case "day": gen_plot($mysqli, "1 MONTH", $_GET['period']); break;
    }
?>
</div>

<div class="element" id="plot">
	<?php

		/*
		 *  Общая статистика
		 */

		$query = "SELECT * FROM `mosmetro_stat` WHERE `date` > DATE_SUB(NOW(), INTERVAL 1 MONTH) ORDER BY `id` ASC";
		
		if (!apc_exists($query)) {
			$res = mysqli_query($mysqli, $query);
		
			$all = array();
			$all['num'] = mysqli_num_rows($res);
			$all['versions'] = array();
			$all['networks'] = array();
			while($row = mysqli_fetch_array($res)) {
				$all['versions'][$row['version']] = 1;
				$all['networks'][$row['ssid']] = 1;
			}
			
			apc_store($query, new ArrayObject($all), 30*60);
		} else {
			$all = apc_fetch($query)->getArrayCopy();
		}
		
		print("Total: " . $all['num'] . "<br>");
	?>
	<span id="settings">
		<form>
			<p>Способ: <select name="automatic">
				<option selected value="">Общая статистика</option>
				<option value="1">Только автоматические</option>
				<option value="0">Только ручные</option>
			</select> <?php echo $_GET['automatic']; ?></p>

			<p>Версия: <select name="version">
				<option selected value="">Все версии</option>

				<?php
					foreach(array_keys($all['versions']) as $version) {
						echo "<option value=\"" . $version . "\">" . $version . "</option>";
					}
				?>
			</select> <?php echo $_GET['version']; ?></p>

			<p>Статус: <select name="connected">
				<option selected value="">Общая статистика</option>
				<option value="1">Успешно подключено</option>
				<option value="0">Уже было подключено</option>
			</select> <?php echo $_GET['connected']; ?></p>

			<p>Период: <select name="period">
				<option selected value="hour">День</option>
				<option value="day">Месяц</option>
			</select> <?php echo $_GET['period']; ?></p>

			<p>Сеть: <select name="ssid">
				<option selected value="">Все сети</option>

				<?php
					foreach(array_keys($all['networks']) as $network) {
						echo "<option value=\"" . $network . "\">" . $network . "</option>";
					}
				?>
			</select> <?php echo $_GET['ssid']; ?></p>

			<input type="submit" />
		</form>
	</span>
</div>
