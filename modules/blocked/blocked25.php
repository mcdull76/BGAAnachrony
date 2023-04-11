<?php 

class blocked25 extends blocked
{ 
    public function execute($arg)
    {
        if (strpos($arg, 'building') === 0)
        {
            $building_id = (int) filter_var($arg, FILTER_SANITIZE_NUMBER_INT);
            $building = self::getObjectFromDB( "select * from building where id=".$building_id );
            $slot = self::getUniqueValueFromDB( "select count(*) from building where category = {$building['category']} and player_id = {$this->player->player_id}")
            + self::getUniqueValueFromDB( "select count(*) from superproject where category = {$building['category']} and player_id = {$this->player->player_id}")*2;
            
            $this->player->gain(null,null,$slot*VP);
        }
    }
}