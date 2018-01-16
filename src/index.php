<?php
	//NOTE! SITE DOESNT USE HTTPS - REGISTERING IS FOR LEARNING PURPOSE!
	session_start();
	$error_message = "";

	if(!array_key_exists('username',$_SESSION))
	{
		include("login.php"); //check for logging in
	}

	//Get game data
	$link = mysqli_connect("PARAMS HERE");

	if(mysqli_connect_errno())
	{
		echo "Error connecting to database";
		exit();
	}


		 if(array_key_exists('username', $_SESSION) AND $_SESSION['username'] != "guest")
			{
				$query = "SELECT `role` FROM `users` where username = '".$_SESSION['username']."'";
				$result = mysqli_query($link,$query);
				$row = mysqli_fetch_array($result);
				$role = $row['role'];
			} 
	
	if(isset($_POST['submit-add']))
	{
		if($role != "user")
		{
			$error_message .= "Error: You dont have rights to modify the list.";
		}

		else
		{
			$isCleared = false;
			if(isset($_POST['cleared']))
			{
				$isCleared = true;
			}

			$add_title = htmlspecialchars(mysqli_real_escape_string($link,$_POST['name']));
			$add_price =  mysqli_real_escape_string($link,$_POST['price']);
			$add_homepage = htmlspecialchars(mysqli_real_escape_string($link,$_POST['homepage']));

			if(!ctype_digit($add_price))
			{
				$error_message .= "Error: Price must be number.";
			}

			else
			{
				$query = "INSERT INTO games (name,price,link,is_played) VALUES ('".$add_title."','".$add_price."','".$add_homepage."','".$isCleared."')";
				if($result = mysqli_query($link,$query))
				{
					echo "<h1> Succsefull insert nmew game: <span> $add_title </span> </h1>";
					unset($_POST['submit-add']);
				}

				else
				{
					echo "<h1> FAIL INSERT </h1>";
				}
		}
		}
	}


	if(isset($_POST['remove-game']))
	{


		if($role != "user")
		{
			$error_message .= "Error: You dont have rights to modify the list.";
		}

		else if(isset($_POST['remove-id']))
		{
			$id_to_remove = $_POST['remove-id'];
			$query = "DELETE FROM `games` WHERE `id` = $id_to_remove LIMIT 1";
			$result = mysqli_query($link,$query);
			echo "<h1> Removed Game! </h1>";
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


		<?php 
		if(!array_key_exists('username',$_SESSION))
		{
		?>
			<h1> Login </h1>
			<p> You're not logged in. Log in to edit the list. </p>
			 <form method="post">
				<input type="text" name="username" placeholder="Username">
				<input type="password" name="password" placeholder="Password">
				<input type="hidden" name="login" value="0">
				<input type="checkbox" name="stayLoggedIn" value= "1">
	      		<input type="submit" name="submit" value="Submit">
	   		</form>


	   		<div id="topbar-container">

	   		<a href="questlogin.php" id="guest-login">Login as Guest</a>

	   		 <a href="register.php">Register here </a>
	   		</div>

	   	<?php
		}

		else
		{ 
			?>



			 <?php echo '<p> Logged in as: <span id="login-name">'.$_SESSION['username'].'</span> <a href="logout.php?logout=1">Log Out</a>  </p>'; 

			 if($_SESSION['username'] == 'guest')
			 {
			 	$role = 'guest';
			 }

			 echo " <p>Your privileges: ".$role."</p>";

			 if($role == "guest")
			 {
			 	echo "<p>As a guest, you are not able to make changes to the list. Sorry. </p>";
			 }


			?>
			<?php
		}
   		?>

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


		<div id="content">

		<h1> Coop Game List</h1>
		<?php 
		if(array_key_exists('username',$_SESSION)) //LOGGED IN
		{?>
			<form method="post">
				<input type="text" name="name" placeholder="Game Name" <?php if($role != "user"){ echo "disabled";}?>>
				<input type="text" name="price" placeholder="59.99"  <?php if($role != "user"){ echo "disabled";}?>>
				<input type="text" name="homepage" placeholder="home page"  <?php if($role != "user"){ echo "disabled";}?>>
				<input type="checkbox" name="cleared" value="1">
				<input type="submit" name="submit-add" value="Add Game"  <?php if($role != "user"){ echo "disabled";}?>>
			</form>


<table>
		  <tr>
		    <th>Game</th>
		    <th>Price</th>
		<!--    <th>Steampage<th> -->
		    <th>Cleared</th>
		  </tr>

		<?php


		$query = "SELECT *  FROM `games` ORDER BY `name` ASC";
		$result = mysqli_query($link,$query);
		$rowCount = mysqli_num_rows($result);

		while($row = mysqli_fetch_array($result))
		{
			$title = mysqli_real_escape_string($link,$row['name']);
			$price =  mysqli_real_escape_string($link,$row['price']);
			$steamlink = mysqli_real_escape_string($link,$row['link']);
			$isPlayed =  mysqli_real_escape_string($link,$row['is_played']);
			$gameid =  mysqli_real_escape_string($link,$row['id']);





			?>
				<tr id="<?php echo $gameid; ?>"
				<?php 
				 if($isPlayed == 1){ echo "class='played'";}?>>
					<td> <?php
						 echo '<a href="'.$steamlink.'">'.$title.'</a>';
					 ?> </td>
					<td> <?php echo "$price €"; ?> </td>

			<?php 
			if($isPlayed)
			{
				echo "<td>Cleared</td>";
			}
			else
			{
				echo "<td> </td>";
			} ?>

			<td> <form method="post">
			 <?php 
			 	echo '<input type="hidden" name="remove-id" value="'.$gameid.'">';

			 	 if($role != "user")
			 	 { 
					echo '<input type="submit" name="remove-game" id="'.$gameid.'" disabled class="remove-button-disabled" value="X">';
			 	 }
			 	 else
			 	 {
			 	 	echo '<input type="submit" name="remove-game" id="'.$gameid.'" class="remove-button" value="X">';
			 	 }
			 
			  ?> 
			</form></td>
				</tr>
			<?php

		}
		?>






		<?php
			mysqli_close($link);
		?>

		</table>







		<?php
		}


		?>


		
	</div> <!-- content end -->
		<footer>
		</footer>
	<body>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="code.js"></script>
</html>