<?php
error_reporting(0);
include ('config.php');
?>


<!DOCTYPE html>
<html>
<head>
  <title>Měření atmosférického tlaku</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/canvasjs.js"></script>
  <link rel="shortcut icon" type="image/ico" href="img/favicon.ico"/>

  <script src="js/Chart.js"></script>
  <script src="js/Chart.min.js"></script>
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <script src="js/utils.js"></script>
</head>
<body>
<nav class="navbar navbar-inverse navbar-static-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <img class="navbar-brand" src="img/logo.png" style="width:60px; height: 55px;"></img>
     <strong> <a class="navbar-brand" href="index.php">Měření atmosférického tlaku</a></strong>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li ><a href="index.php">Statistika</a></li>
        <li><a href="about.php">O projektu</a></li>
        <li><a href="docs.php">Dokumentace</a></li>
      </ul>
    </div>
  </div>
</nav>

<?php

$ts = time() - 86400;

$query = "SELECT AVG(hodnota) AS avgHodnota , time FROM mereni WHERE time > $ts";
$statement = $connect->prepare($query);

if ($statement->execute()) {

    $result = $statement->get_result();
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['avgHodnota'])) {
            echo ('<center class="info"><strong><h2> Průměrný atmosférický tlak za posledních 24 hodin: ' . round($row['avgHodnota']) . ' hPA</h2></strong></center>');
        } else {
            echo '<center class="info"><strong><h2> Žádná data k zobrazení za posledních 24 hodin.</h2></strong></center>';
        }
    }

} else {
    die('<center class="info"><strong><h2> Žádná data k zobrazení za posledních 24 hodin.</h2></strong></center>');
}

for ($x = 7; $x >= 0; $x--) {
    if ($x == 1) {
        $from = time() - 86400 * $x * 2;
        $to = $from + 86400;
        $query = "SELECT AVG(hodnota) AS avgHodnota, time, id FROM mereni WHERE time BETWEEN $from AND $to ORDER BY time ASC";

    } else if ($x == 0) {
        $today = time() - 86400;
        $query = "SELECT AVG(hodnota) AS avgHodnota,time, id FROM mereni WHERE time > $today ORDER BY time ASC";
    } else {
        $from = time() - 86400 * $x;
        $to = $from + 86400;
        $query = "SELECT AVG(hodnota) AS avgHodnota, time, id FROM mereni WHERE time BETWEEN $from AND $to ORDER BY time ASC";
    }

    $statement = $connect->prepare($query);
    if ($statement->execute()) {

        $result = $statement->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {

            if (empty($row['time'])) {
                break;
            }

            if (!empty($row['id'])) {
                $data[$row['id']] = $row;
            } else {
                $data[] = $row;
            }
        }
        if (!empty($data)) {
            echo ('<center> <div style="width:70%; margin-top:3%;">
            <canvas id="canvas"></canvas>
            </div></center>'); // samotny graf
        }

    } else {
        die('<center class="info"><strong><h2> Žádna data k zobrazení za posledních několik dní.</h2></strong></center>');
    }

}

$str = "";
foreach ($data as $item) {
    if ($i == 0) {
        $str .= "'" . date("d.m Y", $item['time']) . "',";
    }

}

echo "<script>
        var datumz = [" . substr($str, 0, -1) . "];
        var config = {
            type: 'line',
            data: {
                labels: datumz,
                datasets: [{
                    label: 'hPa ',
                    backgroundColor: window.chartColors.red,
                    borderColor: window.chartColors.red,
                    data: [";

$str = "";
foreach ($data as $item) {
    if ($i == 0) {
        $str .= "'" . round($item['avgHodnota']) . "',";
    }

}

echo substr($str, 0, -1) . "],
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                title:{
                    display:true,
                    text:'Atmosférický tlak za posledních několik dní'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                 ticks: {
                beginAtZero: true
            },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: ''
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Tlak (hPa)'
                        }
                    }]
                }
            }
        };

        window.onload = function() {
            var ctx = document.getElementById('canvas').getContext('2d');
            window.myLine = new Chart(ctx, config);
        };

        document.getElementById('randomizeData').addEventListener('click', function() {
            config.data.datasets.forEach(function(dataset) {
                dataset.data = dataset.data.map(function() {
                    return randomScalingFactor();
                });

            });

            window.myLine.update();
        });

        var colorNames = Object.keys(window.chartColors);
        document.getElementById('addDataset').addEventListener('click', function() {
            var colorName = colorNames[config.data.datasets.length % colorNames.length];
            var newColor = window.chartColors[colorName];
            var newDataset = {
                label: 'Dataset ' + config.data.datasets.length,
                backgroundColor: newColor,
                borderColor: newColor,
                data: [],
                fill: false
            };

            for (var index = 0; index < config.data.labels.length; ++index) {
                newDataset.data.push(randomScalingFactor());
            }

            config.data.datasets.push(newDataset);
            window.myLine.update();
        });
    </script>";?>
</body>
</html>
