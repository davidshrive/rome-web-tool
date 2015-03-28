<!DOCTYPE html>
<html>
<head>

<link rel='stylesheet' type='text/css' href='http://www.davidshrive.co.uk/tomthing/stylesheet.css'/>
<script type='text/javascript' src='http://www.davidshrive.co.uk/tomthing/script.js'></script>

<!-- SERVER INFO --> 

<?php

	// include php class files
	include 'region.php';
	include 'slot.php';

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

<h1>Tom Rome awesome epic thing</h1>

<p>

<!-- GET FACTION & PROVINCE INFO --> 

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
<br>
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
<br>
<label for='formProvince'>Select your province:</label>
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
<br>
<input type="submit" name="formSubmit" value="Select"> 
</form>

<!-- SHOW FACTION AND PROVINCE INFO --> 

<?php
	if(isset($_POST['formSubmit'])) 
	{
		// grab global variables

		$factionid = $_POST['formFaction'];
		
		if(strlen($factionid) == 0) 
		{
			echo("<p>You didn't select a faction!</p>\n");
		} 
		else 
		{
			$query = "SELECT faction from faction where factionid = '".$factionid."';";
			$faction = mysqli_query($conn, $query);
			$faction = $faction->fetch_array();

			echo("<p>You selected ".$faction['faction']."</p>");
		}

		$provinceid = $_POST['formProvince'];
		
		if(strlen($provinceid) == 0) 
		{
			echo("<p>You didn't select a province!</p>\n");
		} 
		else 
		{
			$query = "SELECT province from province where provinceid = '".$provinceid."';";
			$province = mysqli_query($conn, $query);
			$province = $province->fetch_array();

			echo("<p>You selected ".$province['province']."</p>");
		}

		// Create region classes
		$query = "SELECT regionid, region from province WHERE provinceid = '".$provinceid."';";

		$regionsInfo = mysqli_query($conn, $query);

		$i = 0;
		$regions = array();

	    while($region = $regionsInfo->fetch_array()) {

		    $query = "SELECT * from region WHERE regionid = '".$region['regionid']."';";

			$regionInfo = mysqli_query($conn, $query);
			$regionInfo = $regionInfo->fetch_array();

	        $reg = new region();
	        
	        $reg->id = $region['regionid'];
	        $reg->name = $region['region'];
	        $reg->isCapital = $regionInfo['isCapital'];
	        $reg->isPort = $regionInfo['isPort'];
	        $reg->trade = $regionInfo['Trade'];
	        $reg->factionid = $factionid;

	        $reg->populate($conn);

	        ${'region'.$i} = $reg;

	        array_push($regions, ${'region'.$i});
	        $i++;
    	}


    	//DISPLAY REGION INFO

    	echo 'REGIONS <p>';

    	foreach ($regions as $region) {

    		echo '<p>';
    		
    		echo 'Name: '.$region->name.'<br>';
    		echo 'Capital: '.$region->isCapital.'<br>';
    		echo 'Port: '.$region->isPort.'<br>';
    		echo 'Trade: '.$region->trade.'<br>';

	    	for ($i=0; $i < $region->totalSlots; $i++) { 
	    		
	    		echo 'Slots '.($i+1).':'.$region->{'slot'.$i}->buildingname.'<br>';
	    	}
    	}
	}
?>

</body>
</html>