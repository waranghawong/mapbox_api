<?php

class locations extends db{

    
protected function add_location($lat, $lng, $region_text, $province_text, $city_text, $barangay_text){
    $datetimetoday = date("Y-m-d H:i:s");
    $connection = $this->dbOpen();
    $stmt = $connection->prepare('INSERT INTO locations (lat, lng, region, province, city, barangay,created_at) VALUES (?,?,?,?,?,?,?)');

    if(!$stmt->execute([$lat, $lng,$region_text, $province_text, $city_text,$barangay_text, $datetimetoday])){
        $stmt = null;
        header ("location: ../user-map.php?errors=stmtfailed");
        exit();
    }
    else{
        header('location: ../user-map.php');
    }
   
}

protected function get_saved_locations(){
    $connection = $this->dbOpen();
    $stmt = $connection->prepare("SELECT lng, lat FROM locations");
    $stmt->execute();

    if($stmt->rowCount() > 0){
        return json_encode($stmt->fetchall());
    }


}
}





?>