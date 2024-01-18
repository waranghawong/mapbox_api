<?php

include '../classes/db.php';
include '../classes/locations.classes.php';
include '../classes/locationscntrl.classes.php';


$saved_locations = new locationsCntrl();

$saved_locations->setlocations();

?>