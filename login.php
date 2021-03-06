<!-- Header -->
<?php include ("assets/header.php"); ?>

<?php
    // Check for logged in redirect
    if(isset($_SESSION['login'])) {
        // Relocate to account page because user is already logged in
        ob_start();
        header("Location: account.php");
        ob_end_flush();
        die();
    }

    // Establish shared variables
    // Include the validation functions
    include "assets/validationFunctions.php";

    // Security
    $thisURL = $domain.$phpSelf;

    // Variables
    $errorText = "";
    $username = $errorText;
    $usernameConf = $errorText;
    $password = $errorText;
    $passwordConf = $errorText;
    $email = $errorText;
    $emailConf = $errorText;
    $ip = $_SERVER['REMOTE_ADDR'];

    // Error Variables
    $username_ERROR = false;
    $password_ERROR = false;
    $email_ERROR = false;

    // Error Controls
    $errorMsg = array();

    // Include Account Controls
    include "actions/accountControls.php";
    
    // Check for submit
    if(isset($_POST["login"]) or isset($_POST["createAcc"])) {
        // Check security
        if(!securityCheck($thisURL)) {
            // Failed security
            // Include infraction controls
            include "actions\infractionControls.php";

            // Log information
            $infractionNode->logInfractionPHP("Bad From URL on login.php");

            // Inform user they're bad people
            $out = "<h1>You do not have permission for this page.</h1>";
            die($out);
        }

        // Decide which form was inputted
        if(isset($_POST["createAcc"])) {
            // Create Account Form
            // Sanitize Input
            $username = htmlentities($_POST["usernameA"], ENT_QUOTES, "UTF-8");
            $usernameConf = htmlentities($_POST["usernameB"], ENT_QUOTES, "UTF-8");
            $password = htmlentities($_POST["passwordA"], ENT_QUOTES, "UTF-8");
            $passwordConf = htmlentities($_POST["passwordB"], ENT_QUOTES, "UTF-8");
            $email = filter_var($_POST["emailA"], FILTER_SANITIZE_EMAIL);
            $emailConf = filter_var($_POST["emailA"], FILTER_SANITIZE_EMAIL);

            // Check if Username is already taken
            if($node->checkIfUserTakenPHP($username)) {
                // Username is taken
                $errorMsg[] = "'".$username."' is taken. Try another username.";
                $username_ERROR = true;
            }

            // Validate input
            if($username == $errorText) {
                $errorMsg[] = "Please enter a username.";
                $username_ERROR = true;
            }

            if($username == "DELETED" or strtolower($username) == "password" or strtolower($username) == "user") {
                $errorMsg[] = "That is not a valid account name.";
                $username_ERROR = true;
            }

            if($usernameConf == $errorText) {
                $errorMsg[] = "Please confirm your username.";
                $username_ERROR = true;
            }

            if($password == $errorText) {
                $errorMsg[] = "Please enter a password.";
                $password_ERROR = true;
            }

            if($password == "DELETED" or strtolower($password) == "password" or strtolower($password) == "user") {
                $errorMsg[] = "That is not a valid password.";
                $username_ERROR = true;
            }

            if($passwordConf == $errorText) {
                $errorMsg[] = "Please confirm your password.";
                $password_ERROR = true;
            }

            if($email == $errorText) {
                $errorMsg[] = "Please enter an email.";
                $email_ERROR = true;
            }

            if($emailConf == $errorText) {
                $errorMsg[] = "Please confirm you email.";
                $email_ERROR = true;
            }

            // Validate matches
            if($username != $usernameConf) {
                $errorMsg[] = "Usernames do not match.";
                $username_ERROR = true;
            }

            if($password != $passwordConf) {
                $errorMsg[] = "Passwords do not match.";
                $password_ERROR = true;
            }

            if($email != $emailConf) {
                $errorMsg[] = "Emails do not match.";
                $email_ERROR = true;
            }
        } else {
            // Log-In Form
            // Sanitize Input
            $username = htmlentities($_POST["username"], ENT_QUOTES, "UTF-8");
            $password = htmlentities($_POST["password"], ENT_QUOTES, "UTF-8");

            // Validate Input
            if($username == $errorText) {
                $errorMsg[] = "Please enter a username.";
                $username_ERROR = true;
            }

            if($username == "DELETED" or strtolower($username) == "password" or strtolower($username) == "user") {
                $errorMsg[] = "That is not a valid account name.";
                $username_ERROR = true;
            }

            if($password == $errorText) {
                $errorMsg[] = "Please enter a password.";
                $password_ERROR = true;
            }

            if($password == "DELETED" or strtolower($password) == "password" or strtolower($password) == "user") {
                $errorMsg[] = "That is not a valid password.";
                $username_ERROR = true;
            }

            // Check if Login is valid
            // if(!($node->tryLoginPHP($username,$password))) {
            if(!($node->tryLoginPHP($node->getAccountIdByNamePHP($username),$password))) {
                // Login is not valid
                $errorMsg[] = "Login is invalid.";
                $username_ERROR = true;
                $password_ERROR = true;
            }
        }
    } else {
        if($debug) {
            print '<h1>Form not submitted</h1>';
        }
    }
?>

<!-- Content Start -->
<h1 class="jumboHeader">Join the Trading Hub</h1>

<?php
    // Check Error Messages
    if(empty($errorMsg)) {
        // Decide which form
        if(isset($_POST["createAcc"])) {
            // Create Account Form
            // Execute the insert
            $node->createAccountPHP($username,$password,$email,$ip);
            
            // Get user's AccountId
            $accountId = $node->getAccountIdByNamePHP($username);

            // Attach data to Session
            $_SESSION['login'] = $accountId;

            // Relocate
            ob_start();
            header("Location: account.php");
            ob_end_flush();
            die();

        } else if(isset($_POST["login"])) {
            // Log-In Form
            // Get user's AccountId
            $accountId = $node->getAccountIdByNamePHP($username);

            // Attach data to Session
            $_SESSION['login'] = $accountId;

            // Relocate
            ob_start();
            header("Location: account.php");
            ob_end_flush();
            die();
        }
    } else {
        // Errors present
        // Print top of div
        print '<div class="forumErrors">';

        // Choose correct header
        if(isset($_POST["createAcc"])) {
            // Account Creation
            print '<h3>Account Creation Failed</h3>';
        } else {
            // Login
            print '<h3>Login Failed</h3>';
        }

        // Print all errors
        foreach($errorMsg as $e) {
            print '<p>'.$e.'</p>';
        }

        // Print end of div
        print '</div>';
    }
?>

<!-- Panels -->
<div>
    <div class="leftPanel">
        <h2 class="centerText">Login</h2>
        <form action="<?php print $phpSelf; ?>" method="post">
            <div>
                <div>
                    <h3 class="instruction">Username:</h3>
                    <input type="text" name="username" placeholder="Username" <?php print 'value='.$username; ?>>
                </div>
                <div>
                    <h3 class="instruction">Password:</h3>
                    <input type="password" name="password" placeholder="Password">
                </div>
            </div>

            <input type="submit" value="Login" id="login" name="login">
        </form>
    </div>

    <div class="rightPanel">
        <h2 class="centerText">Create Account</h2>
        <form action="<?php print $phpSelf; ?>" method="post">
            <div>
                <div>
                    <h3 class="instruction">Enter a Username:</h3>
                    <p class="details">A unique username with any characters.</p>
                    <input type="text" name="usernameA" placeholder="Username" <?php print 'value='.$username; ?>>
                    <input type="text" name="usernameB" placeholder="Confirm Username" class="secondInput">
                </div>

                <div>
                    <h3 class="instruction">Enter a Password:</h3>
                    <p class="details">A unique password with any characters.</p>
                    <input type="password" name="passwordA" placeholder="Password">
                    <input type="password" name="passwordB" placeholder="Confirm Password" class="secondInput">
                </div>

                <div>
                    <h3 class="instruction">Enter your Email:</h3>
                    <p class="details">Your email. It is used to allow contact between you and possible buyers.</p>
                    <input type="email" name="emailA" placeholder="Email" <?php print 'value='.$email; ?>>
                    <input type="email" name="emailB" placeholder="Confirm Email" class="secondInput">
                </div>
            </div>

            <input type="submit" value="Create Account" id="createAcc" name="createAcc">
        </form>
    </div>
</div>

<!-- Footer -->
<?php include ("assets/footer.php"); ?>