<?php
	include 'phpgraphlib.php';
	
	// Retrieve data
	$data = unserialize(
		urldecode(
			stripslashes($_GET['data'])
		)
	);
	
	// Create plot
	$graph = new PHPGraphLib(300, 200);
	$graph->addData($data);
	
	if ($_GET['type'] == "line") {
		$graph->setBars(false);
		$graph->setLine(true);
		$graph->setDataPoints(true);
	}
	
	$graph->createGraph();
?>
