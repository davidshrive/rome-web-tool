<!DOCTYPE html>
<html>
<head>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<script type='text/javascript' src='script.js'></script>
<link rel='stylesheet' type='text/css' href='stylesheet.css'/>

<?php

	//<-- SERVER INFO --> 

	// include php class files
	include 'region.php';
	include 'slot.php';
	include 'province.php';

	// server info
	$servername = "localhost";
	$username = "rome";
	$password = "romesecretpassword";
	$db = 'rome';

	// create connection
	$conn = new mysqli($servername, $username, $password, $db);

	// check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	//<-- SESSION VARIABLES --> 

	session_start();

	$provinces = array();
	$provinces = $_SESSION["provinces"];
	$faction = $_SESSION["faction"];

?>

</head>
<body>

<!-- TITLE --> 
<div class ='header'>
<div class ='title'>
<h1>Rome Web Tool</h1>
</div>

<!-- GET FACTION INFO --> 

<div class = 'faction'>

	<div class = 'selectfaction'>
		<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
		<label for='formFaction'>Select your faction:</label>
		<select name="formFaction">
		  <option value="">Select...</option>
		  <?php
			// Craft query
			$query = 'SELECT * from faction ORDER by faction;';

			// Query db
			$factions = mysqli_query($conn, $query);

			// Populate form
		    while($row = $factions->fetch_array()) {

		        echo "<option value=".$row['factionid'].">".$row['faction']."</option>"; 
		    }
		  ?>
		</select>
	</div>

	<div class = 'selectionbutton'>
		<input type="submit" name="factionSubmit" value="Select"> 
		</form>
	</div>

</div>

<div class = 'addprovince'>

	<div class = 'selectprovince'>
		<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
		<label for='formProvince'>Add a province:</label>
		<select name="formProvince">
		  <option value="">Select...</option>
		  <?php
		  	// Craft query
			$query = 'SELECT DISTINCT provinceid, province from province ORDER by province;';

			// Query db
			$provincelist = mysqli_query($conn, $query);

			// Populate form
		    while($row = $provincelist->fetch_array()) {

		        echo "<option value=".$row['provinceid'].">".$row['province']."</option>"; 
		    }
		  ?>
		</select>
	</div>

	<div class = 'selectionbutton'>
		<input type="submit" name="provinceSubmit" value="Add"> 
		</form>
	</div>
</div>

</div>

<!-- Generate region info and bundle it all up into an province object, then add to global variable --> 

<?php
	// grab post variables
	$provinceid = $_POST['formProvince'];
	$factionid = $_POST['formFaction'];

	// if faction set
	if(strlen($factionid) > 0) 
	{
		$_SESSION["faction"] = $factionid;
	}

	// if province set	
	if(strlen($provinceid) > 0) 
	{
		$factionid = $_SESSION["faction"];
		$province = createProvince($conn, $provinceid, $factionid);
		
		$provinces[] = $province;
		$_SESSION["provinces"] = $provinces;
	}
?>


<!--SHOW PROVINCE INFO-->

<?php

foreach ($provinces as $province) {
	
	displayProvinceInfo($province);

}

?>

</div>

</body>
</html>