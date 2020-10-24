<?php 
namespace App\Repositories;

use App\Location;
use GuzzleHttp\Client;

use App\Interfaces\TrackableInterface;

class LocationRepository{

    private $stringAddress;
    private $location;

    public function __construct(){
        $this->location = new Location();
    }//end constructor

    public function address(string $address): LocationRepository{
        $this->stringAddress = $address;
        $this->location->unformatted_address = $address;
        return $this;
    }//end method address

    public function attach(TrackableInterface $trackable): Location{
        if(!$this->location->lat)
            $this->getCoordinates();
        
        $trackable->location()->save($this->location);

        return $this->location->refresh();
    }//end method create


    /**
     * Returns [longitude, lattitude]
     */
    private function getCoordinates(): LocationRepository{
        $client = new Client();

        $response;

        // try{
            //TODO: change link to https
            $response = $client->post("https://maps.googleapis.com/maps/api/geocode/json?key=".config("services.google.maps.key")."&address=$this->stringAddress", [
                "body" => json_encode([
                    "address" => $this->stringAddress,
                    "key" => config("services.google.maps.key")
                ]),
                "headers" => [
                    "Accept" => "application/json",
                    "Content-Type" => "application/json"
                ]
            ]);
        // }catch(\Exception $e){
        //     throw new \Exception($e->getMessage());
        // }

        $response = json_decode((string) $response->getBody());

        if($response->status == "OK"){
            $response = $response->results[0];
            $this->location->lat = $response->geometry->location->lat;
            $this->location->lng = $response->geometry->location->lng;
            $this->location->formatted_address = $response->formatted_address;
        }

        return $this;
    }//end function getCoordinate
}// end class LocationRepository