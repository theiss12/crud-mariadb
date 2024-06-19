<?php

$login_ok = true;
// if ($_SERVER["REQUEST_METHOD"] !== "POST") {
//     $login_ok = false;
// }

if (session_status() === PHP_SESSION_NONE) session_start();
$connection = null;
$username = "";
$password = "";
$table = array_key_exists("table", $_POST) ? $_POST["table"] : "";

try {
    $servername = "localhost";
    $username = array_key_exists("username", $_SESSION) ? $_SESSION["username"] : ""; //"root";
    $password = array_key_exists("password", $_SESSION) ? $_SESSION["password"] : ""; //"p";
    $dbname = "notes";
    $connection = new mysqli($servername, $username, $password, $dbname);
} catch (Exception $e) {
    $login_ok = false;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes</title>
    <link rel="stylesheet" href="./styles/global.css">
    <link rel="stylesheet" href="./styles/notes.css">
</head>

<body>
    <?php
    if (!$login_ok) {
        $nok_message = "
                <div class='nok-message'>
                    <h2 class='nok-message__title'>Database Login Error</h2>
                    <p class='nok-message__description'>Please try again: <a href='./index.html'>login</a></p>
                </div>
            ";
        die($nok_message);
    }

    // let js know the current table name
    echo "
    <script id='table-name-script'>
        const currentTableName = '$table';
    </script>
    ";

    function get_table_names($connection) {
        $query = "SELECT TABLE_NAME FROM information_schema.tables WHERE TABLE_SCHEMA = 'notes'";

        $result = $connection->query($query);
    
        // Check if the query was successful
        if (!$result) {
            die("Query failed: " . $connection->error);
        }
    
        // Fetch and store table names
        $table_names = [];
        while ($row = $result->fetch_assoc()) {
            $table_names[] = $row['TABLE_NAME'];
        }
        return $table_names;
    }
    $table_names = get_table_names($connection);

    function display_table($connection, $table) {
        global $table_names;
        if ($table === "" || !in_array($table, $table_names)) {
            echo "
                <thead>
                    <tr>
                        <th>No data present</th>
                    </tr>
                </thead>
                <tbody></tbody>
            ";
            return;
        }
        $sql = "SELECT id, message, done FROM $table";
        $result = $connection->query($sql);

        if ($result->num_rows > 0) {
            echo "
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Message</th>
                            <th>Done</th>
                        </tr>
                    </thead>
                    <tbody>
            ";
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $current_id = "note-" . $row["id"];
                $class_name = $row["done"] ? "positive" : "negative";
                echo "
                    <tr id='$current_id'>
                        <td>".$row["id"]."</td>
                        <td class='message-$class_name'>".$row["message"]."</td>
                        <td class='done-$class_name'>".($row["done"] ? "Yes" : "No")."</td>
                    </tr>
                ";
            }
            echo "</tbody>";
        } else {
            echo "
                <thead>
                    <tr>
                        <th>No data present</th>
                    </tr>
                </thead>
                <tbody></tbody>
            ";
        }
    }

    function display_table_ids($connection, $table) {
        global $table_names;
        if ($table === "" || !in_array($table, $table_names)) {
            echo "<option>1</option>";
            return;
        }
        $sql = "SELECT id FROM $table";
        $result = $connection->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<option>".$row["id"]."</option>";
            }
        } else {
            echo "
                <option>1</option>
            ";
        }
    }

    ?>

    <ul id="navigation" class="navigation navigation--closed">
        <li class="navigation__item navigation__item--closed">
            <h2 class="item__headline"><a href="./index.html">Back to login</a></h2>
        </li>

        <li class="navigation__item navigation__item--closed">
            <h2 class="item__headline">CREATE</h2>
            <form id="create-table" class="item__post-box">
                <h3 class="item__input-label">Create new note group</h3>
                <p class="item__input-group">
                    <input type="text" id="new-table-name" name="table">
                    <label for="table">Table Name</label>
                </p>
                <p class="item__input-group">
                    <input type="button" value="Create" id="create-table-button">
                </p>
            </form>

            <form id="insert-item" class="item__post-box">
                <h3 class="item__input-label">Insert new note</h3>
                <p class="item__input-group">
                    <input type="text" id="new-item-name" name="item">
                    <label for="item">Message</label>
                </p>
                <p class="item__input-group">
                    <input type="button" value="Insert" id="insert-item-button">
                </p>
            </form>
        </li>

        <li class="navigation__item navigation__item--closed">
            <h2 class="item__headline">READ</h2>
            <form class="item__post-box">
                <select id="table-select">
                    <option>--CHOOSE NOTE-GROUP--</option>
                    <?php
                    foreach($table_names as $name) {
                        echo "<option>$name</option>";
                    }
                    ?>
                </select>
            </form>
        </li>

        <li class="navigation__item navigation__item--closed">
            <h2 class="item__headline">UPDATE</h2>
            <form class="item__post-box">
                <p class="item__input-group">
                    <select id="update-id">
                        <?php display_table_ids($connection, $table) ?>
                    </select>
                    <input type="text" id="update-message">
                    <select id="update-done">
                        <option value="1">done</option>
                        <option value="0">ongoing</option>
                    </select>
                </p>
                <p class="item__input-group">
                    <input type="button" id="update-item-button" value="Apply">
                </p>
            </form>
        </li>

        <li class="navigation__item navigation__item--closed">
            <h2 class="item__headline">DELETE</h2>
            <form class="item__post-box">
                <h3 class="item__input-label">Delete note-group</h3>
                <select id="delete-table-select">
                    <?php
                    foreach($table_names as $name) {
                        echo "<option>$name</option>";
                    }
                    ?>
                </select>
                <input type="button" id="delete-table-button" value="Delete group">
            </form>

            <form class="item__post-box">
                <h3 class="item__input-label">Delete note</h3>
                <select id="delete-item-select">
                    <?php
                    display_table_ids($connection, $table);
                    ?>
                </select>
                <input type="button" id="delete-item-button" value="Delete note">
            </form>
        </li>
        <li class="navigation__item navigation__item--toggle">
            <button
                id="toggle-button"
                class="navigation__toggle-button"
            >
                =
            </button>
        </li>
    </ul>

    <h1 class="note-title">Notes: <span><?php echo $table ?></span></h1>
    <table id="current-table" class="note-group">
        <?php
            display_table($connection, $table);
        ?>
    </table>

    <div class="blocking-layer blocking-layer--closed">
        <div class="error-message">
            <h2 class="error-message__title">
                An Error Occured
                <button class="error-message__close-button">
                    x
                </button>
            </h2>
            <p class="error-message__content">
                The action you wanted to carry out did not succeed. Try reading a note-group first.
            </p>
        </div>
    </div>

    <script src="./send-request.js"></script>
    <script src="./notes.js"></script>
</body>

</html>