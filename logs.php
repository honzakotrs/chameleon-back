<?php
    require_once "class_list.php";
    $log = isset($_GET['log']) ? $_GET['log'] : "err";
?>

<html>
<head>
    <title>Logs viewer</title>

    <style>
        body {
            background-color: #000;
            color: #FFFFFF;
            font-family: Consolas, Monaco, Lucida Console, monospace;
            font-size: 12px;
        }
        .log {
            margin: 5px;
            padding: 5px;
            float: left;
            white-space: pre;
            overflow-x: hidden;
        }
        h3 {
            margin: 0;
            padding: 0;
        }
    </style>

    <script type="application/javascript">
        setTimeout(function () { document.location = document.location; }, 2000);
    </script>

</head>

<body>

<h1>Logs viewer</h1>

<div class="log"><h3><?php echo $log ?></h3><?php
    $contents = Logger::getLog($log);
    $lines = array_reverse(explode("\n", $contents));
    foreach($lines as $line) {
        echo "$line\n";
    }
?></div>

</body>

</html>