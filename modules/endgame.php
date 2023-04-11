<?php 


class endgame extends APP_GameClass
{
    
    public function hasMost($players, $player_id, $endgame)
    {
        $name = "nb".$endgame;
        $current = $this->$name($player_id);
                
        foreach($players as $player)
        {
            if($current < $this->$name($player['player_id']))
            {
                return false;
            }
        }
        return true;
    }
    
    public function nb1($player_id)
    {
        return self::getUniqueValueFromDB("select count(*) from worker where player_id = {$player_id}");
    }
    
    public function nb2($player_id)
    {
        return self::getUniqueValueFromDB("select res5 from player where player_id = {$player_id}");
    }
    
    public function nb3($player_id)
    {
        return self::getUniqueValueFromDB("select count(*) from breakthrough where player_id = {$player_id}");
    }
    
    public function nb4($player_id)
    {
        $nb = 12;
        
        for($cat=1;$cat<=4;$cat++)
        {
            for($slot=1;$slot<=3;$slot++)
            {        
                if(self::getUniqueValueFromDB( "select count(*) from building where category = {$cat} and player_id = {$player_id} and location = {$slot}") == 0
                && self::getUniqueValueFromDB( "select count(*) from superproject where category = {$cat} and player_id = {$player_id} and (location = {$slot} or location = {$slot} - 1)") == 0)
                {
                    $nb--;
                }
            }
        }
        return $nb;
    }
    
    public function nb5($player_id)
    {
        return self::getUniqueValueFromDB("select moral from player where player_id = {$player_id}");
    }
    
    public function nb6($player_id)
    {
        $ret = 0;
        $ret += 1 * self::getUniqueValueFromDB("select count(*) from building where player_id = {$player_id} and type in ( 101,112,113,401 )");
        $ret += 2 * self::getUniqueValueFromDB("select count(*) from building where player_id = {$player_id} and type in ( 102,103,104,108,402)");
        $ret += 3 * self::getUniqueValueFromDB("select count(*) from building where player_id = {$player_id} and type in ( 105,106,107,109,111,114,115)");
        $ret += 4 * self::getUniqueValueFromDB("select count(*) from building where player_id = {$player_id} and type in ( 110)");      
        $ret += 3 * self::getUniqueValueFromDB("select count(*) from superproject where player_id = {$player_id} and type = 8");
        
        return $ret;
    }
    
    public function nb7($player_id)
    {
        return self::getUniqueValueFromDB("select count(*) from superproject where player_id = {$player_id}");;
    }
    
    public function nb8($player_id)
    {
        return self::getUniqueValueFromDB("select temporal from player where player_id = {$player_id}");;
    }

}