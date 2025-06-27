<?php

spl_autoload_register(function ($sClassname) {
    require_once($sClassname.".php");
});

session_name("sess_blog");

Database::loadConfig("inc_db_config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Users and Tasks</title>
    <link rel="stylesheet" type="text/css" href="styles.css"/>
</head>
<body>
<?php

session_start();
if (!isset($_SESSION["user"])) {
    // not logged in
    if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "login") {
        // user wants to log in
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];
        if ($username && loginOk($username, $password)) {
            $_SESSION["user"]["username"] = $username;
            $_SESSION["user"]["userid"] = getUserID($username);
            // hilfe von ChatGPT da ich die Seite immer neu laden musste, da ich sonst nicht in den else Zweig gekommen bin??
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<div class='information'>
                    <p>Wrong Login !</p>
                    </div>";
            showLoginForm();
            showRegistrationForm();
            showBlogEntries();
        }
    }elseif (isset($_REQUEST["action"]) && $_REQUEST["action"] == "register") {
        // user has submitted the registration form
        $username = $_REQUEST['username'];
        $passwordreg = $_REQUEST['password'];
        $password2 = $_REQUEST['password2'];
        $email = $_REQUEST['email'];
        $admin = isset($_REQUEST['is_admin']) ? 1 : 0;
        $userid = 0;

        if ($passwordreg !== $password2) {
            echo "<div class='information'><p>Passwords do not match.</p></div>";
            showRegistrationForm();
        } elseif ($username && isAvailable($username) && $userid = register($username, $passwordreg, $email, $admin)) {
            echo "<div class='information'>";
            echo "<p>Welcome " . ($username) . "! You have been registered to the database. You can now log in with your username.</p>";
            echo "</div>";
            showLoginForm();
            showBlogEntries();
        } else {
            echo "<div class='information'><p>Something went wrong with the registration.</p></div>";
            showRegistrationForm();
            showBlogEntries();
        }
    }
    else {
        // user wants to view page
        showLoginForm();
        showRegistrationForm();
        showBlogEntries();
    }
} else {
    // logged in
    if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "logout") {
        // user wants to log out
        unset($_SESSION["user"]);
        showLoginForm();
        showRegistrationForm();
        showBlogEntries();
    }
    else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "createBlogEntry") {
        showLogoutForm();
        showBlogEntryForm();
        // user wants to create blogEntry
       /* echo "Creating Blog Entry<br>";*/
        createBlogEntry();
        showBlogEntries();

    }
    else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "deleteEntry") {
        // user wants to delete task
        if (isset($_REQUEST["entryid"])) {
            $entryId = ($_REQUEST["entryid"]);
           /* echo "Deleting Blog Entry<br>";
            echo "Blog entry with id: " . $entryId . " will be deleted<br>";*/
            $deleteStatementUserTask = "DELETE FROM blogEntry WHERE id=" . $entryId . ";";
            Database::deleteQuery($deleteStatementUserTask);
            showLogoutForm();
            if ($_SESSION["user"]["role"] == 1) {
                showBlogEntries();
                echo "<a class='xml-import' href='index.php?action=importXML'>Import XML-File: BlogEntry.xml</a>";
            }
            else{
                showBlogEntryForm();
                showBlogEntries();
            }
        } else {
            echo "No entry ID provided.";
        }
    }
    else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "editEntry") {
        if (isset($_REQUEST["entryid"])) {
            // Eintragsformular anzeigen
            showBlogEntryEditForm();
        }
    } else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "saveEditEntry") {
        // Bearbeitete Daten speichern
        if ($_SESSION["user"]["role"] == 1) {
            showLogoutForm();
            saveEditEntry();
            showBlogEntries();
            echo "<a class='xml-import' href='index.php?action=importXML'>Import XML-File: BlogEntry.xml</a>";
        }
        else{
            showLogoutForm();
            saveEditEntry();
            showBlogEntryForm();
            showBlogEntries();
        }

    }
    else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "importXML") {
        // Importieren der XML-Datei
        $domParser = new DomParser("BlogEntry.xml");
        $domParser->importXML();
        showLogoutForm();
        echo "<div class='information'><p>XML-File wurde erfolgreich importiert</p></div>";
        showBlogEntries();

    }
    else if ($_SESSION["user"]["role"] == 1) {
        showLogoutForm();
        showBlogEntries();
        echo "<a class='xml-import' href='index.php?action=importXML'>Import XML-File: BlogEntry.xml</a>";
    }
    else{
        showLogoutForm();
        showBlogEntryForm();
        showBlogEntries();

    }
}


function showBlogEntries(): void
{
    // Überprüfen, ob ein Benutzer eingeloggt ist
    $currentUserID = $_SESSION["user"]["userid"] ?? null;
    $currentUserRole = $_SESSION["user"]["role"] ?? null;

    // SQL-Join-Statement, um Blog-Einträge und Benutzerinformationen abzurufen
    $joinStatement = "SELECT blogEntry.*, user.username as creator, user2.username as lastEditor
                      FROM blogEntry
                      JOIN user ON blogEntry.userid = user.userid
                      JOIN user as user2 ON blogEntry.lastEditUser = user2.userid";

    // Abfrage ausführen
    $result = Database::selectQuery($joinStatement);

    echo "<h1>Blog Entries</h1>";
    echo "<div class='blog-container'>";
    // Ergebnis durchlaufen und ausgeben
    while ($row = $result->fetch_assoc())
    {
        $highlightClass = ($row["state"] === "highlighted") ? " highlighted" : "";

        echo "<div class='blog-entry$highlightClass'>";
        echo "<h2>" . ($row["title"]) . "</h2>";
        echo "<p>" . ($row["noteText"]) . "</p>";

        echo "<div class='meta-info'>";
        echo "<span>Created on: " . ($row["createdOn"]) . "</span>" . "<br/>";
        echo "<span>Created by: " . ($row["creator"]) . "</span>" . "<br/>";;
        echo "<span>Last Edited on: " . ($row["lastEdit"]) . "</span>" . "<br/>";;
        echo "<span>Last Edited by: " . ($row["lastEditor"]) . "</span>" . "<br/>";;
        echo "</div>";

        // Bearbeitungs- und Lösch-Buttons anzeigen, wenn der aktuelle Benutzer der Ersteller ist
        if ($currentUserID !== null && $row["userid"] == $currentUserID)
        {
            echo "<div class='blog-entry-buttons'>";
            echo '<button class="btn"><a href="index.php?action=editEntry&entryid=' . $row["id"] . '">Edit</a></button>';
            echo '<button class="btn"><a href="index.php?action=deleteEntry&entryid=' . $row["id"] . '">Delete</a></button>';
            echo "</div>";
        }

        // Bearbeiten und Löschen von allen anzeigen, wenn die userRole 1 ist
        else if ($currentUserRole == 1)
        {
            echo "<div class='blog-entry-buttons'>";
            echo '<button class="btn"><a href="index.php?action=editEntry&entryid=' . $row["id"] . '">Edit</a></button>';
            echo '<button class="btn"><a href="index.php?action=deleteEntry&entryid=' . $row["id"] . '">Delete</a></button>';
            echo "</div>";
        }

        echo "</div><br/>";
    }
    echo "</div>";
}



function createBlogEntry(): void
{
    // Daten aus dem Formular
    $title = $_REQUEST["entrytitle"];
    $noteText = $_REQUEST["noteText"];
    $status = $_REQUEST["status"];
    $userID = $_SESSION["user"]["userid"];

    // Aktuelles Datum und Zeit
    $creationDate = (new DateTime())->format('Y-m-d H:i:s');
    $lastEditTime = $creationDate;
    $lastEditUserID = $userID;
    /*var_dump($title);
    var_dump($creationDate);*/

    $insertquery = ("INSERT INTO `blogEntry` (`title`, `userid`, `noteText`, `state`, `createdOn`, `lastEdit`, `lastEditUser`)
                    VALUES ('$title', '$userID', '$noteText', '$status', '$creationDate', '$lastEditTime', '$lastEditUserID');");

    Database::insertQuery($insertquery);
}

function getUserID(string $username):int
{
    $mysqliResult = Database::selectQuery("SELECT userid FROM user WHERE username='".$username."';");

    $row = $mysqliResult->fetch_assoc();

    return $row["userid"];
}


function loginOk(string $username, string $password): bool
{
    $query = "SELECT password, role FROM user WHERE username='".$username."'";
    $result = Database::selectQuery($query)->fetch_assoc();

    if ($result && $password == $result["password"]) {
        $_SESSION["user"]["role"] = $result["role"];
        return true;
    }

    return false;
}



function register(string $username, string $password, string $email, int $admin): int {
    /*echo "Registering user: " . $username . "<br>";*/
    return Database::insertQuery("INSERT INTO user (username, password, email, role) VALUES ('$username', '$password', '$email', '$admin');");
}


function isAvailable(string $username):bool
{
    $result = Database::selectQuery("SELECT * FROM user WHERE username='".$username."';");
    if ($result->num_rows == 0)
    {
        return true;
    }
    return false;
}


function showLoginForm(){
?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="login" class="user-form">
    <div>
        <label for="usernameLogin">Username: </label>
        <input id="usernameLogin" name="username" type="text"/><br/>
    </div>
    <div>
        <label for="passwordLogin">Password: </label>
        <input id="passwordLogin" name="password" type="password"/><br/>
    </div>
    <div>
        <input type="hidden" name="action" value="login"/>
        <input type="submit" value="Login" class="btn btn-primary"/>
    </div>
</form>
<br/>
<?php
}

function showLogoutForm(){
?>
    <div class="logout">
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="logout" class="logout">
            <input type="hidden" name="action" value="logout"/>
            <input type="submit" value="Logout" class="btn-logout"/>
        </form>
    </div>

<?php
}

function showBlogEntryForm(){
    ?>
    <div class="newEntry">
        <h3 class="blogform">Create new Blog Entry:</h3>
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="entryForm" class="blog-entry-form">
            <div class="form-group">
                <label for="entrytitle">Blog Entry Title:</label>
                <input id="entrytitle" name="entrytitle" type="text" class="form-control" required/>
            </div>

            <div class="form-group">
                <label for="noteText">Note Text:</label>
                <textarea id="noteText" name="noteText" class="form-control" rows="4" cols="50"></textarea>
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" class="form-control">
                    <option value="normal">Normal</option>
                    <option value="highlighted">Highlighted</option>
                </select>
            </div>

            <input type="hidden" name="action" value="createBlogEntry"/>
            <input type="submit" value="Submit" class="btn btn-primary"/>
        </form>
    </div>

    <?php
}




function showRegistrationForm($formData = [], $errorMsg = '') {
    ?>
    <?php if ($errorMsg): ?>
        <div class="error-message"><?php echo ($errorMsg); ?></div>
    <?php endif; ?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="register" class="user-form">
        <div>
            <label for="username">Username: </label>
            <input id="username" name="username" type="text" value="<?php echo ($formData['username'] ?? ''); ?>" />
        </div>
        <div>
            <label for="password">Password: </label>
            <input id="password" name="password" type="password" value="<?php echo ($formData['password'] ?? ''); ?>" /><br />
        </div>
        <div>
            <label for="password2">Confirm Password: </label>
            <input id="password2" name="password2" type="password" value="<?php echo ($formData['password2'] ?? ''); ?>" /><br />
        </div>
        <div>
            <label for="email">E-mail: </label>
            <input id="email" name="email" type="email" value="<?php echo ($formData['email'] ?? ''); ?>" /><br />
        </div>
        <div>
            <label for="is_admin">I am an Admin: </label>
            <input id="is_admin" name="is_admin" type="checkbox" value="1" <?php echo ($formData['is_admin'] ?? '') ? 'checked' : ''; ?> /><br />
        </div>
        <div>
            <input type="hidden" name="action" value="register" />
            <input type="submit" value="Register" class="btn" />
        </div>
    </form>
    <?php
}

function showBlogEntryEditForm()
{
    if (isset($_REQUEST["entryid"])) {
        $entryId = ($_REQUEST["entryid"]);

        // Abfrage zum Abrufen der aktuellen Daten des Blog-Eintrags
        $entryQuery = "SELECT * FROM blogEntry WHERE id=" . $entryId . ";";
        $result = Database::selectQuery($entryQuery);

        if ($row = $result->fetch_assoc()) {
            ?>
            <div class="editEntry">
                <h3>Edit Blog Entry:</h3>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="editEntryForm" class="blog-entry-form">
                    <div class="form-group">
                        <label for="entrytitle">Blog Entry Title:</label>
                        <input id="entrytitle" name="entrytitle" type="text" class="form-control" value="<?php echo ($row['title']); ?>" required/>
                    </div>

                    <div class="form-group">
                        <label for="noteText">Note Text:</label>
                        <textarea id="noteText" name="noteText" class="form-control" rows="4" cols="50"><?php echo ($row['noteText']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" class="form-control">
                            <option value="normal" <?php echo ($row['state'] == 'normal') ? 'selected' : ''; ?>>Normal</option>
                            <option value="highlighted" <?php echo ($row['state'] == 'highlighted') ? 'selected' : ''; ?>>Highlighted</option>
                        </select>
                    </div>

                    <input type="hidden" name="entryid" value="<?php echo $row['id']; ?>"/>
                    <input type="hidden" name="action" value="saveEditEntry"/>
                    <input type="submit" value="Save" class="btn btn-primary"/>
                </form>
            </div>
            <?php
        } else {
            echo "No entry found with the provided ID.";
        }
    } else {
        echo "No entry ID provided.";
    }
}


function saveEditEntry()
{
    if (isset($_REQUEST["entryid"])) {
        $entryId = ($_REQUEST["entryid"]);
        $title = $_REQUEST["entrytitle"];
        $noteText = $_REQUEST["noteText"];
        $status = $_REQUEST["status"];
        $userID = $_SESSION["user"]["userid"];

        // Aktuelles Datum und Zeit für die letzte Bearbeitung
        $lastEditTime = (new DateTime())->format('Y-m-d H:i:s');
        $lastEditUserID = $userID;

        // SQL-Abfrage zum Aktualisieren des Blog-Eintrags
        $updateQuery = "UPDATE blogEntry SET 
                        title = '$title', 
                        noteText = '$noteText', 
                        state = '$status', 
                        lastEdit = '$lastEditTime', 
                        lastEditUser = '$lastEditUserID' 
                        WHERE id = $entryId;";

        Database::updateQuery($updateQuery);
    }
}
?>
</body>
</html>