<?php
	//NOTE! SITE DOESNT USE HTTPS - REGISTERING IS FOR LEARNING PURPOSE!
	session_start();
	$error_message = "";

	if(array_key_exists('username',$_SESSION))
	{
		header("Location: index.php");
	}


	if($_POST)
	{

		//Form Validation
		function clean_input($data) 
		{
		  $data = trim($data);
		  $data = stripslashes($data);
		  $data = htmlspecialchars($data);
		  return $data;
		}

		$email = $_POST['email'];
		$password = clean_input($_POST['password']);
		$password_conf = clean_input($_POST['password-confirm']);
		$username = clean_input($_POST['username']);
		$registering_enabled = true;

		if($registering_enabled != true)
		{
			$error_message = " <h1> REGISTERING NOT ENABLED </h1> <br>";
		}

		if(strlen($email) >= 500 OR strlen($password) >= 500 OR strlen($password_conf >= 500) OR strlen($username) >= 500)
		{
			 $error_message .= "Field too long"; 
		}


		if (!filter_var($email,FILTER_VALIDATE_EMAIL))
		{
 		 	$error_message .= "Invalid email format.<br>"; 
		}

		if(!$email)
		{
			$error_message .= "Email address is required.<br>";
		}

		if(!$username) 
		{
			$error_message .= "Username is required.<br>"; 
		}

		if($username == "guest")
		{
			$error_message .= "Invalid username.<br>"; 
		}

		if (!$password)
		{
			$error_message .= "Password is required.<br>";
		}

		if (!preg_match("/^[a-zA-Z ]*$/",$username)) {
			$error_message .= "Only letters and whitespace is allowed.<br>"; 
		}

		if (!$password_conf)
		{
			$error_message .= "Password confirmation is required.<br>";
		}

		if($password != $password_conf)
		{			
			$error_message .= "Passwords doesnt match.<br>";
		}

		//check email or username already exists in database
		if($error_message == "")
		{

			$link = mysqli_connect("LOGIN PARAMS");

			if(mysqli_connect_errno())
			{
				echo "Error connecting to database";
				exit();
			}
				$query = "SELECT `id` FROM `users` WHERE `email` = '".mysqli_real_escape_string($link,$email)."' OR `username` = '".mysqli_real_escape_string($link,$username)."' LIMIT 1";
				$result = mysqli_query($link,$query);

				if(mysqli_num_rows($result) > 0 )
				{
					//query succesful
					$error_message = "That email or username has been taken.";
				}
				else{
					//No results for query
					$query = "INSERT INTO `users` (`email`, `username`,`password`) VALUES( 
						 '".mysqli_real_escape_string($link,$email)."',
						  '".mysqli_real_escape_string($link,$username)."',
							'".mysqli_real_escape_string($link,$password)."'
						  )";

					if(!mysqli_query($link,$query))
					{
						$error_message = "Error singing you up!";

					}
					else
					{
							//HASH CODE
							$hash = password_hash(mysqli_real_escape_string($link,$password), PASSWORD_DEFAULT);
							$query = "UPDATE `users` SET `role` = 'guest', `password` = '".$hash."' WHERE `username`= '".mysqli_real_escape_string($link,$username)."'  LIMIT 1" ; 
							mysqli_query($link,$query);
							$_SESSION['username'] = mysqli_real_escape_string($link,$username);

							/*
							if(isset($_POST['stayLoggedIn']) && $_POST['stayLoggedIn'] == "1")
							{
								setcookie("username",mysqli_real_escape_string($link,$username),time()+60*60*24);
							} */
							header("Location: index.php");
							echo "Success registering";
					}
			}

		}


		else
		{
			$error_message = "Error(s) in your form: ".$error_message;
		}

	}


?>
<!doctype html>
<html>
	<head>
		<title>CoopGamesList</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<header>
		</header>

		<form method="post">
			<input type="email" name="email" placeholder="Email">
			<input type="text" name="username" placeholder="Username">
			<input type="password" name="password" placeholder="Password">
			<input type="password" name="password-confirm" placeholder="Confirm Password">
			<input type="hidden" name"signup" value="1">
      		<input type="submit" name="submit" value="Submit">
   		</form>

   		<hr>
   		<br>



		</form>
		<div id="error-messages">
			<?php 
			if(count($_POST)>0)
				{
				if($error_message != "")
				{
					echo "<p>".$error_message."</p>";
				}
				else{
				}
			}
			?>
		</div>
		<a href="index.php">Login here </a>


		<footer>
		</footer>
	<body>

		<script src="code.js"></script>
</html>