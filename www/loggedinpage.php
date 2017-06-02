<?php

  session_start();

  if(array_key_exists("id", $_COOKIE)) {

    $_SESSION['id'] = $_COOKIE['id']; //if the user has a cookie, we update the session

  }

  if(array_key_exists("id", $_SESSION)) {

    echo "<p>Logged in! <a href='index.php?logout=1'>Log out</a></p>";

  } else {

      // redirect to homepage
      header ("Location: index.php");

  }

 ?>
