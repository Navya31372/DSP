<?php

/*==================================================
        DIGITAL SKILL PASSPORT
              LOGOUT PAGE
===================================================*/


/*
|--------------------------------------------------------------------------
| Start the existing session
|--------------------------------------------------------------------------
*/

session_start();



/*
|--------------------------------------------------------------------------
| Prevent browser caching
|--------------------------------------------------------------------------
|
| This is important because otherwise the browser may show an old
| dashboard page using its cache after the user logs out.
|
*/

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");



/*
|--------------------------------------------------------------------------
| Remove all session variables
|--------------------------------------------------------------------------
*/

$_SESSION = [];



/*
|--------------------------------------------------------------------------
| Remove the session cookie
|--------------------------------------------------------------------------
*/

if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );

}



/*
|--------------------------------------------------------------------------
| Destroy the session
|--------------------------------------------------------------------------
*/

session_destroy();



/*
|--------------------------------------------------------------------------
| Start a new temporary session
|--------------------------------------------------------------------------
|
| This allows us to show a logout message on login.php if required.
|
*/

session_start();

$_SESSION["logout_message"] =
    "You have been logged out successfully.";



/*
|--------------------------------------------------------------------------
| Redirect user to login page
|--------------------------------------------------------------------------
*/

header("Location: login.php");

exit;

?>