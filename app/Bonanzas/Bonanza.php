<?php
namespace App\Bonanzas;

use App\Exceptions\BonanzaException;
use Carbon\Carbon;

class Bonanza{
    const BASE_CONFIG_PATH = "app_bonanza";
    const ACTIVE_PATH = "active";
    const TYPE_PATH = "type";
    const COUNT_PATH = "count";
    const TIME_DURATION_PATH = "time_duration";
    const TIME_DURATION_UNIT_PATH = "time_unit";
    const START_DATE_PATH = "start_date";
    const END_DATE_PATH = "end_date";
    
    private $bonanzaKey;
    private $modelClass;
    private $compareVal;

    public function __construct(string $bonanzaKey, $modelClass = null){
        if(!array_key_exists($bonanzaKey, config(static::BASE_CONFIG_PATH)))
            throw new BonanzaException("The bonanza key '{$bonanzaKey}' is invalid");
        $this->bonanzaKey = $bonanzaKey;

        if($modelClass)
            $this->modelClass = $modelClass;
    }//end constructor method

    public function compareVal(int $compareVal): Bonanza{
        $this->compareVal = $compareVal;
        return $this;
    }//end method compareVal

    public function hasPriviledge(): bool{
        if(!$this->isActive())
            return false;
        
        if(!$this->getStartDate())
            throw new BonanzaException("The start date must be specified");

        $startDate = Carbon::create((string) $this->getStartDate());
        $endDate = "";

        // Get the endDate
        if($this->getTimeDuration() != 0){
            $timeDurationUnit = $this->getTimeDurationUnit();
            
            if(!\in_array($timeDurationUnit, ["days", "weeks", "months", "years"]))
                throw new BonanzaException("Invalid Time duration limit");
            
            if($timeDurationUnit == "days")
                $endDate = Carbon::create((string) $startDate)->addDays($this->getTimeDuration());
            elseif($timeDurationUnit == "weeks")
                $endDate = Carbon::create((string) $startDate)->addWeeks($this->getTimeDuration());
            elseif($timeDurationUnit == "months")
                $endDate = Carbon::create((string) $startDate)->addMonths($this->getTimeDuration());
            elseif($timeDurationUnit == "years")
                $endDate = Carbon::create((string) $startDate)->addYears($this->getTimeDuration());
        }else{
            if(!$this->getEndDate()){
                throw new BonanzaException("The time duration must be set or the end date");
            }

            $endDate = Carbon::create((string) $this->getEndDate());
        }

        $startDate->settime(0,0,0);
        $endDate->settime(23,59,59);

        $isWithinTimeLimit = now()->between($startDate, $endDate);

        if(!$isWithinTimeLimit)
            return false;

        if($this->getType() == "time"){
            return $isWithinTimeLimit;
        }


        // If type is count
        if($this->getType() == "count" && $this->getCount() && $this->getCount() > 0){
            $maxCount = $this->getCount();

            if($this->modelClass){
                $currentNumber = $this->modelClass::whereBetween("created_at", [$startDate->format("Y-m-d") . " 00:00:00", $endDate->format('Y-m-d')." 23:59:59"])->count();

                return $currentNumber < $maxCount;
            }
            elseif($this->compareVal){
                return $compareVal < $maxCount;
            }

            throw new BonanzaException("Model class or Compare val must be set");
        }

        throw new BonanzaException("If type is count set count; else a wrong type was set");
    }//end method hasPriviledge

    private function makePathString(string $relativePath = ""): string{
        return static::BASE_CONFIG_PATH . "." . $this->bonanzaKey . ($relativePath ? "." . $relativePath : "");
    }//end method makePathString

    public function isActive(): bool{
        return config($this->makePathString(static::ACTIVE_PATH));
    }//end method isActive

    public function getType(): string{
        return config($this->makePathString(static::TYPE_PATH));
    }//end method getType

    public function getCount(): int{
        return config($this->makePathString(static::COUNT_PATH));
    }//end method getCount

    public function getTimeDuration(): int{
        return config($this->makePathString(static::TIME_DURATION_PATH));
    }//end method getTimeDuration

    public function getTimeDurationUnit(): string{
        return config($this->makePathString(static::TIME_DURATION_UNIT_PATH));
    }//end method getTimeDurationUnit

    public function getStartDate(): string{
        return (string) config($this->makePathString(static::START_DATE_PATH));
    }//end method getStartDate

    public function getEndDate(): string{
        return (string) config($this->makePathString(static::END_DATE_PATH));
    }//end method getEndDate
}//end class Bonanza
