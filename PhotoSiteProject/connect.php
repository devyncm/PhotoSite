<?php
/* connect.php -- connect to MySQL and select webuser database
 *
 * Darren Provine, 8 March 2011
 */

// ConnectDB() - takes no arguments, returns database handle
// USAGE: $dbh = ConnectDB();
function ConnectDB() {

    /*** mysql server info ***/
    $hostname = '127.0.0.1';
    $username = 'melend53';
    $password = '';
    $dbname   = 'melend53';

    try {
        $dbh = new PDO("mysql:host=$hostname;dbname=$dbname",
                       $username, $password);
    }
    catch(PDOException $e)
    {
        die ('PDO error in "ConnectDB()": ' . $e->getMessage() );
    }

    return $dbh;
}

?>

