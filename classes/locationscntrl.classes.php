<?php 

class locationsCntrl extends locations{

    public function setlocations(){
        if(isset($_POST['add_location'])) {
            $lat = $_POST['lat'];
            $lng = $_POST['lng'];
            $region_text = $_POST['province_text'];
            $province_text = $_POST['province_text'];
            $city_text = $_POST['city_text'];
            $barangay_text = $_POST['barangay_text'];
            $this->add_location($lat, $lng, $region_text, $province_text, $city_text, $barangay_text);
    
        }
    }

    public function get_locations(){
        return $this->get_saved_locations();
    }
}


?>