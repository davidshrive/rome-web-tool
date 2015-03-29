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

<!-- Generate region info and bundle it all up into an province object --> 

<?php
	// grab global variables
	$factionid = $_POST['formFaction'];
	$provinceid = $_POST['formProvince'];
	
	if(strlen($factionid) > 0 && strlen($provinceid) > 0 ) 
	{

		$query = "SELECT faction from faction where factionid = '".$factionid."';";
		$factionInfo = mysqli_query($conn, $query);
		$factionInfo = $factionInfo->fetch_array();

		echo("<p>You selected ".$factionInfo['faction']."</p>");

		$query = "SELECT province from province where provinceid = '".$provinceid."';";
		$provinceInfo = mysqli_query($conn, $query);
		$provinceInfo = $provinceInfo->fetch_array();

		echo("<p>You selected ".$provinceInfo['province']."</p>");

		// Create province class
		$province = new province();

		$province->id = $provinceid;
		$province->name = $provinceInfo['province'];

		// Query DB for region info
		$query = "SELECT regionid, region from province WHERE provinceid = '".$provinceid."';";
		$regionsList = mysqli_query($conn, $query);

		$regions = array();

		// Create region classes
		$i = 0;
	    while($region = $regionsList->fetch_array()) {

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

	        // Add to region list
	        array_push($regions, $reg);
	        $i++;
		}
		// add regions array to province class
		$province->regions = $regions;
	}
	else{
		echo "You didn't select anything....";
	}

?>

<!--DISPLAY REGION INFO-->

<div class = "province">

<h2><?php echo $province->name; ?></h2>

<?php

foreach ($province->regions as $region) {

	echo '<div class = "region">';
	
	echo '<h3>'.$region->name.'</h3>';
	echo 'Capital: '.$region->isCapital.'<br>';
	echo 'Port: '.$region->isPort.'<br>';
	echo 'Trade: '.$region->trade.'<br>';

	for ($i=0; $i < $region->totalSlots; $i++) { 
		
		echo 'Slots '.($i+1).':'.$region->{'slot'.$i}->buildingname.'<br>';
	}

	// Icons

	for ($i=0; $i < $region->totalSlots; $i++) { 
		
		echo '<img class="buildingicon" src="'.$region->{'slot'.$i}->buildingimagelink.'">';
	}

	echo '<h2>Effects : </h2>';
	foreach ($region->effects as $effect) {
		echo 'Name: '.$effect['effect'].', Scope: '.$effect['scope'].', Value: '.$effect['value'].'<br>';
	}

	echo '</div>';
}

?>

</div>

</body>
</html>