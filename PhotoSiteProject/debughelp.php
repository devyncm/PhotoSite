<?php

# debughelp.php - useful debugging stuff for PHP
#
# To use this file, put "include_once('debughelp.php');" at the
# top of your PHP code.
# Call "debugformdata()" to see what's in a form.
# Call "dumpobject()" on any object to see what's in it.
#
# D Provine
# (revision of 24 March 2019)

# turn on all error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


# If "debug" was in the query, dump all the information from the form.
# If "debug=anything", then make it appear on the page instead of an
#     HTML comment
#
# NOTE: do not use this if there are large uploaded files.
#
function debugformdata ( $comment = True)
{
    if ( isset ($_REQUEST['debug']) ) {
        $comment = empty( $_REQUEST['debug'] );
        if ( $comment ) {
            echo "<!--\n";
        } else {
            echo "<pre>\n";
        }
        echo "----------- FORM DATA -----------\n";

        echo "Request Method: " . $_SERVER['REQUEST_METHOD'] . "\n";

        if ( $_SERVER['REQUEST_METHOD'] == "GET" ) {
            echo "QUERY STRING: " . $_SERVER['QUERY_STRING'] . "\n";
        } else {
            echo "CONTENT LENGTH: " . $_SERVER['CONTENT_LENGTH'] . "\n";
        }

        echo 'Dump of $_REQUEST: ' . "\n";
        
        print_r ($_REQUEST);

        echo "-------- END OF FORM DATA -------\n";
        if ( $comment ) {
            echo "-->\n";
        } else {
            echo "</pre>\n";
        }
    }
}


# dump an object
# for HTML comment: "dumpobject ( $object_name, __LINE__ )"
# for text in page: "dumpobject ( $object_name, __LINE__, False )"
#
function dumpobject( $thing, $line, $comment=True )
{
    if ( isset ( $thing ) ) {
        if ( $comment ) {
            echo "<!--\n";
        } else {
            echo "<pre>\n";
        }

        echo "dumpobject() called at\n" .
             $_SERVER['PHP_SELF'] . ": $line \n";

        print_r ($thing);
        echo "\n";

        if ( $comment ) {
            echo "-->\n";
        } else {
            echo "</pre>\n";
        }
    }
}

