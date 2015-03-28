<!DOCTYPE html>
<html>
<head>

<link rel='stylesheet' type='text/css' href='http://www.davidshrive.co.uk/tomthing/stylesheet.css'/>
<script type='text/javascript' src='http://www.davidshrive.co.uk/tomthing/script.js'></script>

<!-- SERVER INFO --> 

<?php

	// Server info
	$servername = "localhost";
	$username = "rome";
	$password = "romesecretpassword";
	$db = 'rome';

	// Create connection
	$conn = new mysqli($servername, $username, $password, $db);

	// Check connection
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

		// SHOW REGIONS

		$query = "SELECT regionid, region from province WHERE provinceid = '".$provinceid."';";

		$regions = mysqli_query($conn, $query);

	    while($region = $regions->fetch_array()) {

		    $query = "SELECT * from region WHERE regionid = '".$region['regionid']."';";

			$regionInfo = mysqli_query($conn, $query);
			$regionInfo = $regionInfo->fetch_array();

	        echo "<h2>Region</h2>";

	        //var_dump($regionInfo);

	        echo "Name: ".$region['region'];

	        echo "<br>Capital: ";
	        
	        if ($regionInfo['isCapital'] == 1)
	        {
	        	echo 'Yes';
	        }
	        else
	        {
	        	echo 'No';
	        }

	        echo "<br>Port: ";

	        if ($regionInfo['isPort'] == 1)
	        {
	        	echo 'Yes';
	        }
	        else
	        {
	        	echo 'No';
	        }

	        echo "<br>Trade: ";

	        if ($regionInfo['Trade'])
	        {
	        	echo $regionInfo['Trade'];
	        }
	        else
	        {
	        	echo 'None';
	        }

	        echo "<br>Building Slots: ";

	        if ($regionInfo['isCapital'] == 1 && $regionInfo['isPort'] == 1) {$buildingslots = 6; $standardslots = 4; echo $buildingslots;}
	        if ($regionInfo['isCapital'] == 1 && $regionInfo['isPort'] == 0) {$buildingslots = 5; $standardslots = 4; echo $buildingslots;}
	        if ($regionInfo['isCapital'] == 0 && $regionInfo['isPort'] == 1) {$buildingslots = 4; $standardslots = 2; echo $buildingslots;}
	        if ($regionInfo['isCapital'] == 0 && $regionInfo['isPort'] == 0) {$buildingslots = 3; $standardslots = 3; echo $buildingslots;}

	        // BUILDING SLOTS
	        // Initial special slot

	        echo "<br> Slot 1:";

	        // Capital
	        if ($regionInfo['isCapital'])
	        {
	        	$query = "SELECT image_name, buildingid, building from building where ".$factionid." = '1' AND isCapital = '1' AND level = '1';";

	        	$buildings = mysqli_query($conn, $query);

	        	while ($row = $buildings->fetch_array())
				{
					echo $row['building'];
					echo '<img class="buildingicon" src="http://www.davidshrive.co.uk/tomthing/images/buildings/icons/'.$row['image_name'].'.png">';
				}
	        }

	        // Town
	        if (!$regionInfo['isCapital'])
	        {
	        	$query = "SELECT image_name, buildingid, building from building where ".$factionid." = '1' AND isTown = '1' AND level = '1' AND resource = '".$regionInfo['Trade']."' ;";

	        	$buildings = mysqli_query($conn, $query);

	        	while ($row = $buildings->fetch_array())
				{
					echo $row['building'];
					echo '<img class="buildingicon" src="http://www.davidshrive.co.uk/tomthing/images/buildings/icons/'.$row['image_name'].'.png">';
				}
	        }

	        //Standard Slots
	        for ($i=2; $i < ($standardslots+2); $i++) { 

	        	echo "<br> Slot ".($i).":";

	        	// IF CAPITAL
		  		if ($regionInfo['isCapital']) {
		  		    $query = "SELECT image_name, buildingid, building from building where ".$factionid." = '1' AND capitalBuildable = '1' AND level = '1';";
		  		}
		  		// IF TOWN
      		    if (!$regionInfo['isCapital']) {
		  		    $query = "SELECT image_name, buildingid, building from building where ".$factionid." = '1' AND townBuildable = '1' AND level = '1';";
		  		}
				
				$buildings = mysqli_query($conn, $query);

				while ($row = $buildings->fetch_array())
				{
					echo $row['building'].': ';
					echo '<img class="buildingicon" src="http://www.davidshrive.co.uk/tomthing/images/buildings/icons/'.$row['image_name'].'.png">';
				}
			}

			//Port Slot
			if ($regionInfo['isPort'])
	        {
	        	echo "<br> Slot ".$buildingslots.": ";

	        	$query = "SELECT image_name, buildingid, building from building where ".$factionid." = '1' AND isPort = '1' AND level = '1';";

	        	$buildings = mysqli_query($conn, $query);

	        	while ($row = $buildings->fetch_array())
				{
					echo $row['building'];
					echo '<img class="buildingicon" src="http://www.davidshrive.co.uk/tomthing/images/buildings/icons/'.$row['image_name'].'.png">';
				}
	        }
    	}	
	}
?>

</body>
</html>