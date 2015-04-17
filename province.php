<?php
class province {

	public $id;
	public $name;

	// then an array consisting of all regions can be added
}

function createProvince($conn, $provinceid, $factionid)
{

		$query = "SELECT province from province where provinceid = '".$provinceid."';";
		$provinceInfo = mysqli_query($conn, $query);
		$provinceInfo = $provinceInfo->fetch_array();

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

		return $province;
}

function displayProvinceInfo($province)
{

	echo '<div class = "province">';
	echo '<div class = "provincetitle">';
	echo $province->name;
	echo '</div>';

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

		echo '<h3>Effects : </h3>';
		foreach ($region->effects as $effect) {
			echo 'Name: '.$effect['effect'].', Scope: '.$effect['scope'].', Value: '.$effect['value'].'<br>';
		}

		echo '</div>';
	}

	echo '</div>';
}

?>