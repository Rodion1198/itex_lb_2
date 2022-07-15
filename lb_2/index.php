<?php
require_once "vendor/autoload.php";

use MongoDB\Client;

$client = new \MongoDB\Client("mongodb://127.0.0.1/");
$db = $client->database1->database1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2</title>
    <script>
        function _1() {
            let name = document.getElementById("name").value;
            let result = localStorage.getItem(name);
            document.getElementById('res').innerHTML = result;
        }

        function _2() {
            let department = document.getElementById("department").value;
            let result = localStorage.getItem(department);
            document.getElementById('res').innerHTML = result;
        }

        function _3() {
            let shift = document.getElementById("shift").value;
            let departmentShift = document.getElementById("departmentShift").value;
            let result = localStorage.getItem(shift + departmentShift);
            document.getElementById('res').innerHTML = result;
        }
    </script>

</head>

<body>
    <form action="" method="GET">
        <p>Получить перечень палат, в которых дежурит выбранная медсестра:
            <select id="name" name="name" onchange="_1()">
                <?php
                $statement = $db->distinct("nurseName");
                foreach ($statement as $value)
                    print "<option> $value</option>";
                ?>
            </select>
            <button>ok</button>
        </p>
    </form>


    <form action="" method="GET">
        <p>Получить перечень медсёстр, выбранного отделения:
            <select name="department" id="department" onchange="_2()">
                <?php
                $statement = $db->distinct("department");
                foreach ($statement as $value)
                    print "<option> $value</option>";
                ?>
            </select>
            <button>ok</button>
        </p>
    </form>


    <form action="" method="GET">
        <p>Получить перечень дежурств в указанную смену в указанном отделении:</p>
        <p>Выберите смену
            <select id="shift" name="shift" onchange="_3()">
                <?php
                $statement = $db->distinct("shift");
                foreach ($statement as $value)
                    print "<option> $value</option>";
                ?>
            </select>
        </p>
        <p>Выберите отделение
            <select id="departmentShift" name="departmentShift" onchange="_3()">
                <?php
                $statement = $db->distinct("department");
                foreach ($statement as $value)
                    print "<option> $value</option>";
                ?>
            </select>
            <button>ok</button>
        </p>
    </form>
    <div id="res"> </div>
</body>

<?php
if (isset($_GET["name"])) {
    $name = $_GET["name"];
    $result = "Перечень палат, в которых дежурит медсестра <b>" . $name . "</b>";
    $result .= "<table border =1><tr><th>WardName</th></tr>";
    $cursor = $db->find(["nurseName" => $name]);
    $arr = array();
    foreach ($cursor as $document) {
        $WardName = $document['ward'];
        if (is_object($WardName)) {
            $WardName = (array)$WardName;
            foreach ($WardName as $value) {
                $result .= "<tr> <td>$value</td> </tr>";
            }
        }
    }
    echo "<script> localStorage.setItem('$name', '$result'); </script>";
    print $result;
}
if (isset($_GET["department"])) {
    $department = $_GET["department"];
    $department = intval($department, 10);

    $result =  "Перечень медсёстр отделения <b>" . $department . "</b>";
    $result .= "<table border =1><tr><th>NurseName</th></tr>";
    $cursor = $db->find(['department' => $department]);

    foreach ($cursor as $document) {
        $NurseName = $document['nurseName'];
        $result .= "<tr> <td>$NurseName</td> </tr>";
    }
    print $result;
    echo "<script> localStorage.setItem('$department', '$result'); </script>";
}
if (isset($_GET["shift"]) && isset($_GET["departmentShift"])) {
    $shift = $_GET["shift"];
    $department = intval($_GET["departmentShift"], 10);
    $key = $shift . $department;
    $result = "Перечень палат в <b>" . $shift . "</b> смену и в " . $department . " отделении";
    $result .=  "<table border =1><tr><th>Date</th> </tr>";
    $cursor = $db->find([
        'department' => $department,
        'shift' => $shift
    ]);

    foreach ($cursor as $document) {
        $date = gmdate("H:i:s Y-m-d", $document['date']);
        $result .= "<tr> <td>$date</td> </tr>";
    }
    echo "<script> localStorage.setItem('$key', '$result'); </script>";
    print $result;
}
?>

</html>