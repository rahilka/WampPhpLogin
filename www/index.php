<?php

  session_start();
  $error = "";

  if(array_key_exists("logout", $_GET)) {

    unset($_SESSION); //unset the session
    setcookie("id", "", time() - 60*60);  //set the cookie in the past
    $_COOKIE["id"] = "";  //it takes an extra refresh for the cookie variable to actually be destroyed

  } else if((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {

      header("Location: loggedinpage.php");

  }

  if (array_key_exists("submit", $_POST)) {

    // $link = mysqli_connect("localhost", "username", "password");

    // if(mysqli_connect_error()) {
    //   die("Database connection error");
    // }

    if(!$_POST['email']) {
      $error .= "An email address is required";
    }

    if(!$_POST['password']) {
      $error .= "An email address is required";
    }

    if($error != "") {
      $error = "<p>There were errors in your form</p>".$error;
    } else {

      // check if there is a user with that mail in our db
      // columns: id, email, password, diary

      if($_POST['signUp'] == '1') {

        $query = "SELECT id FROM `users` WHERE email = '".mysqli_real_escape_string($_POST['email'])."' LIMIT 1 ";

        $result = mysqli_query($link, $query);

        if(mysqli_num_rows($result) > 0) {
          $error = "That email addres is taken";
        } else {
          $query = "INSERT INTO `users` (`email`, `password`) VALUES ('".mysqli_real_escape_string($link, $_POST['email'])."', '".mysqli_real_escape_string($_POST['password'])."')";
        }

        if(!mysqli_query($link, $query)) {

          $error = "<p>Could not sign you up - Please try again later.</p>";

        } else {

          $query = "UPDATE `users` SET password = '".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id = ".mysqli_insert_id()." LIMIT 1 ";

          mysqli_query($Link, $query);

          $_SESSION['id'] = mysqli_insert_id($link);

          if($_POST['stayedLoggedIn'] == '1') {

            setcookie("id", mysqli_insert_id($link), time() + 60*60*24*365); //keep logged in for a year

          }

          header("Location: loggedinpage.php");

        }

      } else {

        $query = "SELECT * FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'";
        $result = mysql_query($link, $query);

        $row = mysqli_fetch_array($result);

        if(isset($row)) {

          $hashedPassword = md5(md5($row['id']).$_POST['password']);

          if($hashedPassword == $row['password']) {

            $_SESSION['id'] = $row['id'];

            if($_POST['stayedLoggedIn'] == '1') {

              setcookie("id", $row['id'], time() + 60*60*24*365); //keep logged in for a year

            }

            header("Location: loggedinpage.php");

          } else {

            $error = "That email/password combination could not be found";

          }

        } else {

          $error = "That email/password combination could not be found";

        }

      }
    }

  }

 ?>



 <!DOCTYPE html>
 <html lang="en">
   <head>
     <meta charset="utf-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
     <title>Bootstrap 101 Template</title>

     <!-- Bootstrap -->
     <link href="css/bootstrap.min.css" rel="stylesheet">

     <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
     <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
     <!--[if lt IE 9]>
       <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
       <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
     <![endif]-->

     <style type="text/css">

     .container {

       text-align: center;
       width:400px;
       margin-top: 100px;

     }

     html {
        background: url(image.jpg) no-repeat center center fixed;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
      }

      body {
        background: none;
        color: white;
      }

      #loginForm {
        display: none;
      }

      .toggleForms {
        font-weight: bold;
      }

     </style>


   </head>
   <body>

      <div class="container">

        <!-- <img src="/image.jpg" /> -->

        <h1><strong>Secret diary</strong></h1>

        <p>
          <strong>Store your thoughts permanently and securely</strong>
        </p>

        <div id="error">
          <?php echo $error; ?>
        </div>

        <form method="post" id="signUpForm">

          <p>
            Interested? Sign up now!
          </p>

          <fieldset class="form-group">

            <input class="form-group" type="email" name="email" placeholder="Your emal" />

          </fieldset>

          <fieldset class="form-group">

            <input class="form-group" type="password" name="password" placeholder="Your password" />

          </fieldset>

          <div class="checkbox">

            <label>

            <input type="checkbox" name="stayLoggedIn" value="1" />

            Stay logged in

            </label>

          </div>

            <input type="hidden" name="signUp" value="1" />

          <fieldset class="form-group">

            <input class="btn btn-success" type="submit" name="submit" value="Sign up" />

          </fieldset>

          <p>
            <a class="toggleForms">Log In</a>
          </p>

        </form>

        <form method="post" id="loginForm">

          <p>
            Log in using your username and password.
          </p>

          <fieldset class="form-group">

            <input class="form-group" type="email" name="email" placeholder="Your emal" />

          </fieldset>

          <fieldset class="form-group">

            <input class="form-group" type="password" name="password" placeholder="Your password" />

          </fieldset>

            <div class="checkbox">

              <label>

              <input type="checkbox" name="stayLoggedIn" value="1" />
              Stay logged in

            </label>

            </div>

            <input type="hidden" name="signUp" value="0" />

          <fieldset class="form-group">

            <input class="btn btn-success" type="submit" name="submit" value="Log in" />

          </fieldset>

          <p>
            <a class="toggleForms">Sign Up</a>
          </p>

        </form>

      </div>


     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
     <!-- Include all compiled plugins (below), or include individual files as needed -->
     <script src="js/bootstrap.min.js"></script>

     <script type="text/javascript">

        $(".toggleForms").click(function() {

            // here we will toggle each of the forms
            // both login and sign up forms

            $("#signUpForm").toggle();
            $("#loginForm").toggle();


        })

     </script>

   </body>
 </html>
