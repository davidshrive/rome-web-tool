<?php
class region {

    // printable name
    public $name; 

    // id for db look up
    public $id;

    // region info
    public $isCapital; 
    public $isPort;
    public $totalSlots; 
    public $standardSlots; 
    public $trade;
    public $effects = array();

    // faction info (needed for auto-populating slots)
    public $factionid;

    // function to populate the region object with empty slots, takes SQL connection as arguments so it can connect to DB
    function populate($conn){

    	$this->totalSlots = $this->returnTotalSlots();
    	$this->standardSlots = $this->returnStandardSlots();

    	for ($i=0; $i < $this->totalSlots; $i++) { 
    		
    		$this->{'slot'.$i} = new slot();
    	}

        if ($this->isCapital){

        	// default 1st building
	        $query = "SELECT buildingid from building where ".$this->factionid." = '1' AND isCapital = '1' AND level = '1';";
	        $building = mysqli_query($conn, $query);
	        $building = $building->fetch_array();
	        $this->updateSlot($conn, $building['buildingid'], 0);

	        // remaing slots (-1 for port)
	        for ($i=1; $i < ($this->totalSlots)-1; $i++) { 
    		
    			$this->updateSlot($conn, 'empty', $i);
    		}
    	}
	    else{

			// default 1st building
	        $query = "SELECT buildingid from building where ".$this->factionid." = '1' AND isTown = '1' AND level = '1' AND resource = '".$this->trade."' ;";
	        $building = mysqli_query($conn, $query);
	        $building = $building->fetch_array();
	        $this->updateSlot($conn, $building['buildingid'], 0);

	        // remaing slots (-1 for port)
	        for ($i=1; $i < ($this->totalSlots)-1; $i++) { 
    		
    			$this->updateSlot($conn, 'empty', $i);
    		}
	    }

	    if ($this->isPort){

	        $query = "SELECT buildingid from building where ".$this->factionid." = '1' AND isPort = '1' AND level = '1';";
	        $building = mysqli_query($conn, $query);
	        $building = $building->fetch_array();
	        $this->updateSlot($conn, $building['buildingid'], ($this->totalSlots)-1);
	    }
	    else
	    {
	    	$this->updateSlot($conn, 'empty', ($this->totalSlots)-1);
	    }
    }

    // function to update slot, takes SQL connection, buildingid and slot number as input
    function updateSlot ($conn, $buildingid, $slot){

    	//sql look up
		$query = "SELECT * from building WHERE buildingid = '".$buildingid."';";
		$buildingInfo = mysqli_query($conn, $query);
		$buildingInfo = $buildingInfo->fetch_array();

		// add building info to slot object
		$this->{'slot'.$slot}->buildingid = $buildingInfo['buildingid'];
		$this->{'slot'.$slot}->buildingname = $buildingInfo['building'];
		$this->{'slot'.$slot}->buildingimagelink = 'http://www.davidshrive.co.uk/tomthing/images/buildings/icons/'.$buildingInfo['image_name'].'.png';
		$this->{'slot'.$slot}->level = $buildingInfo['level'];

		// update effects
		$this->calculateEffects($conn);
    }

    // function to calculate effects
    function calculateEffects ($conn) {

    	//delete all existing effects
    	$this->effects = array();

     	for ($i=0; $i < $this->totalSlots; $i++) { 
    		
     		$query = "SELECT * from effect where buildingid = '".$this->{'slot'.$i}->buildingid."';";
     		$effectList = mysqli_query($conn, $query);

     		if ($effectList->num_rows){

     			while($effect = $effectList->fetch_array()){

     				$ef['effect'] = $effect['effect'];
     				$ef['scope'] = $effect['scope'];
     				$ef['value'] = $effect['value'];

     				array_push($this->effects, $ef);
     			}
     		}
    	}
    }

    // function to return total number of slots
    function returnTotalSlots (){

        if ($this->isCapital){
        	if ($this->isPort){
        		return 6;
        	}
        	else{
        		return 5;
        	}
        }
        else{
        	if ($this->isPort){
        		return 5;
        	}
        	else{
        		return 4;
        	}
        }
    }

    // function to function to return number of standard slots (not main or port)
    function returnStandardSlots (){

        if ($this->isCapital){
        	if ($this->isPort){
        		return 4;
        	}
        	else{
        		return 4;
        	}
        }
        else{
        	if ($this->isPort){
        		return 3;
        	}
        	else{
        		return 3;
        	}
        }
    }
} 
?>