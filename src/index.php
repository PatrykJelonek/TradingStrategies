<?php

use TradingStrategies\Structures\Exchanges\CryptoCompare\CryptoCompareMarketData;

include('./../vendor/autoload.php');

$dataPoints = (new \TradingStrategies\GetStrategyResult())(
    TradingStrategies\Strategies\SeidenbergStuckey\SeidenbergStuckeyStrategy::class
);

$cumulativeLongAndShortPositionsProfits = $dataPoints['cumulativeLongAndShortPositionsProfits'];
$cumulativeLongPositionsProfits = $dataPoints['cumulativeLongPositionsProfits'];
$cumulativeShortPositionsProfits = $dataPoints['cumulativeShortPositionsProfits'];

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Strategies Test</title>

    <script>
		window.onload = function () {

			var chart = new CanvasJS.Chart("chartContainer", {
				title: {
					text: "Chart"
				},
				axisY: {},
				data: [
					{
						type: "line",
						color: "blue",
						axisXIndex: 0,
						dataPoints: <?php echo json_encode(
                            $cumulativeLongAndShortPositionsProfits,
                            JSON_NUMERIC_CHECK
                        ); ?>
					},
					{
						type: "line",
						color: "green",
						axisXIndex: 1,
						dataPoints: <?php echo json_encode($cumulativeLongPositionsProfits, JSON_NUMERIC_CHECK); ?>
					},
					{
						type: "line",
						color: "red",
						axisXIndex: 2,
						dataPoints: <?php echo json_encode($cumulativeShortPositionsProfits, JSON_NUMERIC_CHECK); ?>
					},
				]
			});
			chart.render();

		}
    </script>
</head>
<body>
<div id="chartContainer" style="height: 600px; width: 750px;"></div>
<script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
</body>
</html>
