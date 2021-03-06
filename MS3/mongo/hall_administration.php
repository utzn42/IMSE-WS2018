<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cinebase</title>
    <link rel="stylesheet"
          type="text/css"
          href="css/main.css"/>
    <script src="js/main.js"></script>
</head>
<body>

<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
?>

<div class="wrapper">
    <div class="topLine" id="topLine">
        cinebase
        <button onclick="window.location='index.php';" style="margin-left: 20px" class="buttonBig">Home
        </button>
        <button onclick="window.location='movies.php';" class="buttonBig">Movies
        </button>
        <button onclick="window.location='screening.php';" class="buttonBig">Screenings</button>
        <button onclick="window.location='employee_administration.php';" class="buttonBig">Employees
        </button>
        <button onclick="window.location='hall_administration.php';"
                style="border-bottom: 2px solid whitesmoke; font-weight: bold" class="buttonBig">Halls
        </button>
        <button id="signIn" onclick="document.getElementById('popUpLogin').style.display='block'"
                class="buttonLogin">
            Sign In
        </button>
        <button id="register" onclick="window.location='register.php';"
                class="buttonRegister">Register
        </button>
    </div>
</div>

<div class="wrapperMainBody">
    <div class="mainBody" id="mainBody">
        <br><br>
        <div>
            <form id='searchform' action='hall_administration.php' method='get'>
                <a href='hall_administration.php'>All Halls</a> ---
                Search for Hall:
                <input class='searchName' id='searchName' name='searchName' type='text' size='20'
                       value='<?php echo $_GET['searchName']; ?>'/>
                <input id='search' type='submit' value='Search!'/>
            </form>
            <br>
        </div>

        <?php




        try {

            $mng = new MongoDB\Driver\Manager("mongodb://localhost:27017");
            $query = new MongoDB\Driver\Query([], ['sort' => [ '_id' => 1]]);

            $rows = $mng->executeQuery("cinebase.halls", $query);
            $idx=0;
            foreach ($rows as $row) {

                if($row->_id>$idx){
                    $idx=$row->_id;
                }


            }

        } catch (MongoDB\Driver\Exception\Exception $e) {

            $filename = basename(__FILE__);

            echo "The $filename script has experienced an error.\n";
            echo "It failed with the following exception:\n";

            echo "Exception:", $e->getMessage(), "\n";
            echo "In file:", $e->getFile(), "\n";
            echo "On line:", $e->getLine(), "\n";
        }
        ?>

        <br>

        <div id="insertHall">
            <form id='insertform' action='hall_administration.php' method='get'>
                Add new Hall:
                <table style='border: 1px solid #DDDDDD'>
                    <thead>
                    <tr>
                        <th>Hall-ID</th>
                        <th>Name</th>
                        <th>Equipment</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <input class="inputHall_ID" id='inputHall_ID' name='inputHall_ID' type='text'
                                   size='10'
                                   value='<?php echo $_GET['hall_id']; ?>'/>
                        </td>
                        <td>
                            <input id='nameHall' name='nameHall' type='text' size='20'
                                   value='<?php echo $_GET['name']; ?>'/>
                        </td>
                        <td>
                            <input id='equipmentHall' name='equipmentHall' type='text' size='20'
                                   value='<?php echo $_GET['equipment']; ?>'/>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <input id='insert' type='submit' value='Insert!'/>
            </form>
        </div>

        <?php
        //Handle delete
        try {

            $mng = new MongoDB\Driver\Manager("mongodb://localhost:27017");

            if (isset($_POST["del"])) {

                $bulk = new MongoDB\Driver\BulkWrite;

                $del_id = $_POST["del"];

                //$bulk->update(['name' => 'Audi'], ['$set' => ['price' => 52000]]);
                $bulk->delete(['_id' => intval($del_id)]);

                $mng->executeBulkWrite('cinebase.halls', $bulk);
                //header("location: movies.php");
                //header("Refresh:0");


            }

        } catch (MongoDB\Driver\Exception\Exception $e) {

            $filename = basename(__FILE__);

            echo "The $filename script has experienced an error.\n";
            echo "It failed with the following exception:\n";

            echo "Exception:", $e->getMessage(), "\n";
            echo "In file:", $e->getFile(), "\n";
            echo "On line:", $e->getLine(), "\n";
        }


        //echo("<script type=\"text/javascript\">hideFormInsertMovie();</script>");


        ?>
        <?php
        //Handle insert
        try {

            $mng = new MongoDB\Driver\Manager("mongodb://localhost:27017");

            if (isset($_GET['inputHall_ID']) && !empty($_GET['inputHall_ID'])) {

                $bulk = new MongoDB\Driver\BulkWrite;

                $doc = ['_id' => intval($_GET['inputHall_ID']), 'name' => $_GET['nameHall'], 'equipment' => $_GET['equipmentHall'] ];
                $bulk->insert($doc);
                //$bulk->update(['name' => 'Audi'], ['$set' => ['price' => 52000]]);
                //$bulk->delete(['name' => 'Hummer']);

                $mng->executeBulkWrite('cinebase.halls', $bulk);

            }
        } catch (MongoDB\Driver\Exception\Exception $e) {

            $filename = basename(__FILE__);

            echo "The $filename script has experienced an error.\n";
            echo "It failed with the following exception:\n";

            echo "Exception:", $e->getMessage(), "\n";
            echo "In file:", $e->getFile(), "\n";
            echo "On line:", $e->getLine(), "\n";
        }
        ?>
        <br>

        <table style='border: 1px solid #DDDDDD'>
            <thead>
            <tr>
                <th>Hall-ID</th>
                <th>Name</th>
                <th>Equipment</th>
            </tr>
            </thead>
            <tbody>

            <?php

            if (isset($_GET['searchName'])) {
                $filter = [ 'name' => new MongoDB\BSON\Regex($_GET['searchName'], 'i') ];
                $query = new MongoDB\Driver\Query($filter);
            }

            $rows = $mng->executeQuery("cinebase.halls", $query);
            $idx=0;
            foreach ($rows as $row) {

                $idx++;
                echo "<tr>";
                echo "<td style=\"padding: 5px 100px 5px 10px;\">$row->_id</td>";
                echo "<td style=\"padding: 5px 100px 5px 10px;\">$row->name</td>";
                echo "<td style=\"padding: 5px 100px 5px 10px;\">$row->equipment</td>";

                echo "<td>";
                echo "<form method='post' action='updatehall.php' class='inline'>";
                echo "<input type='hidden' name='hall_id' value=$row->_id>";

                $str_name = urlencode($row->name);
                echo "<input type='hidden' name='name' value=$str_name>";

                $str_equipment = urlencode($row->equipment);
                echo "<input type='hidden' name='equipment' value=$str_equipment>";

                echo "<button type='submit' name='submit_param' value='submit_value' class='link-button'>";
                echo "UPDATE";
                echo "</button>";
                echo "</form>";
                echo "</td>";

                echo "<td>";
                echo "<form action='hall_administration.php' method='post'>";
                echo "<input type='hidden' name='del' value=$row->_id>";
                echo "<button>DELETE</button>" ;
                echo "</form>";
                echo "</td>";

                "</tr>";

            }

            ?>
            </tbody>
        </table>

    </div>
</div>

<?php

echo("<script type=\"text/javascript\">hideFormInsertMovie();</script>");

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    $username = $_SESSION['username'];
    echo("<script type=\"text/javascript\">setLoggedIn(\"$username\");</script>");
}
if (isset($_SESSION['loggedinAdmin']) && $_SESSION['loggedinAdmin'] == true) {
    echo("<script type=\"text/javascript\">setAdminMode();</script>");
}
if (isset($_SESSION['loggedinEmployee']) && $_SESSION['loggedinEmployee'] == true) {
    $username = $_SESSION['username'];
    echo("<script type=\"text/javascript\">setEmployeeMode(\"$username\");</script>");
}
?>

<!-- Start of the part taken from: https://www.w3schools.com/howto/howto_css_login_form.asp -->
<div id="popUpLogin" class="modal">
    <span onclick="document.getElementById('popUpLogin').style.display='none'"
          class="close" title="Close Modal">&times;</span>

    <form class="modal-content animate" action="index.php" method="post">

        <div class="container">
            <label for="username"><b>Username</b></label>
            <input class="signInInputs" type="text" placeholder="Enter Username" name="username" required>

            <label for="password"><b>Password</b></label>
            <input class="signInInputs" type="password" placeholder="Enter Password" name="password"
                   required>

            <button class="buttonLoginModal" type="submit">Login</button>
            <label>
                <input type="checkbox" name="remember"> Employee
            </label>
        </div>

        <div class="container" style="background-color:#f1f1f1">
            <button type="button" onclick="document.getElementById('popUpLogin').style.display='none'"
                    class="cancelbtn">Cancel
            </button>
            <span class="psw">Forgot <a href="#">password?</a></span>
        </div>
    </form>
</div>
<!-- End of the part taken from: https://www.w3schools.com/howto/howto_css_login_form.asp -->


<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

<p style="margin: auto; width: 900px">Yasin Ergüven Utz Nisslmüller Alexander Ramharter Oliver
    Schweiger</p>


</body>
</html>