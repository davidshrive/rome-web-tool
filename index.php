<!DOCTYPE html>
<html>
<head>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<script type='text/javascript' src='script.js'></script>
<link rel='stylesheet' type='text/css' href='stylesheet.css'/>

<!-- SERVER INFO --> 

<?php

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

?>

</head>
<body>

<!-- TITLE --> 
<div class ='header'>
<div class ='title'>
<h1>Rome Web Tool</h1>
</div>

<!-- GET FACTION INFO --> 

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
<div class = 'selectfaction'>

<label for='formProvince'>Select your first province:</label>
<select name="formProvince">
  <option value="">Select...</option>
  <?php

  	// Craft query
	$query = 'SELECT DISTINCT provinceid, province from province ORDER by province;';

	// Query db
	$provinces = mysqli_query($conn, $query);

	// Populate form
    while($row = $provinces->fetch_array()) {

        echo "<option value=".$row['provinceid'].">".$row['province']."</option>"; 
    }

  ?>
</select>
</div>

<div class = 'selectionbutton'>
<input type="submit" name="formSubmit" value="Select"> 
</form>
</div>
</div>

<!-- Generate region info and bundle it all up into an province object --> 

<?php
	// grab global variables
	$factionid = $_POST['formFaction'];
	$provinceid = $_POST['formProvince'];
	
	if(strlen($factionid) > 0 && strlen($provinceid) > 0 ) 
	{
		$province = addProvince($conn, $provinceid, $factionid);	
	}
?>

<!--DISPLAY PROVINCE INFO-->

<?php

displayProvinceInfo($province);
displayProvinceInfo($province);

?>

</div>

</body>
</html>