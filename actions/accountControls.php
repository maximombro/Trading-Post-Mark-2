<?php
    // Class Function: All controls for manipulating user accounts

    // Includes the database connector if nothing else has
    include_once 'databaseConnector.php';

    // Builds the class extending the database connector
    class tryLoginApi extends databaseConnector {
        // Creates a new listing with supplied information
        public function createAccountPHP($name,$password,$email,$ip) {
            // Connects to the database connector to retrieve query data
            parent::databaseConnector();

            // // Hash Password
            // $password = password_hash($password, PASSWORD_DEFAULT);

            // Execute the submission query and return listing id
            return $this->createAccount($name,$password,$email,$ip);
        }

        // Checks to see if the username has already been put into the database
        // True = Taken, False = Not Taken
        public function checkIfUserTakenPHP($name) {
            // Connects to the database connector to retrieve query data
            parent::databaseConnector();

            // Execute the submission query and return listing id
            return $this->checkIfUserTaken($name);
        }

        // Checks if username and password link to a valid account
        public function tryLoginPHP($name,$password) {
            // Connects to the database connector to retrieve query data
            parent::databaseConnector();

            // // Hash Password
            // $password = password_hash($password, PASSWORD_DEFAULT);

            // Return an array to use with further PHP programming
            return $this->tryLoginInfo($name,$password);
        }

        // Gets the user's account id using thier name
        public function getAccountIdByNamePHP($name) {
            // Connects to the database connector to retrieve query data
            parent::databaseConnector();

            // Execute the submission query and return listing id
            return $this->getAccountIdByName($name);
        }

        // Gets the account data from the account id. Excludes password.
        public function getAccountInfoFromIdPHP($id) {
            // Connects to the database connector to retrieve query data
            parent::databaseConnector();

            // Execute the submission query and return listing id
            return $this->getAccountInfoFromId($id);
        }

        // Attempts to change the users password and compares old password to ensure security
        public function changeUserPasswordPHP($accountId,$oldPass,$newPass) {
            // Connects to the database connector to retrieve query data
            parent::databaseConnector();

            // // Hash Password
            // $oldPass = password_hash($oldPass, PASSWORD_DEFAULT);
            // $newPass = password_hash($newPass, PASSWORD_DEFAULT);

            // Execute the submission query and return listing id
            return $this->changeUserPassword($accountId,$oldPass,$newPass);
        }

        // Deletes the user's account
        public function deleteAccountPHP($accountId,$password) {
            // Connects to the database connector to retrieve query data
            parent::databaseConnector();

            // Execute the submission query and return listing id
            return $this->deleteAccount($accountId,$password);
        }
    }
    
    // Creates the initial object
    // Use '$node->FUNCTION' to use this object and call it's methods
    $node = new tryLoginApi();
?>