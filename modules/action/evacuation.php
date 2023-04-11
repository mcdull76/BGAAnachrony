<?php 

class evacuation extends action
{     
    public $needExosuit = true;
    public $multispace = true;
    
    public function canDo($worker, $index = "", $ignorefull = false)
    {
        $ok = self::getUniqueValueFromDB("select count(*) from path where player_id = {$this->player_id} and location like 'phevacuate%' ")==0;
        $ok = $ok && anachrony::$instance->getGameStateValue( 'after_impact') == 1;
        $ok = $ok && parent::canDo($worker, $index);
        $ok = $ok && ($this->player->path->canEvacuate() || $this->player->hasActiveSuperProject(14));
       return $ok;
    }
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $index = self::getUniqueValueFromDB("select count(*) from path where location like 'phevacuate%' ")+1;
        $nbplayers = self::getUniqueValueFromDB("select count(*) from player");
        
        self::DbQuery("INSERT INTO path (player_id, path, location) VALUES ({$this->player_id},{$this->player->path->type},'phevacuate{$index}')");
        $path = self::getObjectFromDB( "select * from path where location = 'phevacuate{$index}'");
        anachrony::$instance->notifyAllPlayers( "newpath", '', array(
            'path' => $path
        ) );
        
        $tot = 0;
        
        if($index == $nbplayers)
        {
            $tot -= 3;
            $this->player->pay(null,null, VP*3);            
        }
        
        $tot += $this->player->path->getGainEvacuate();        
        $this->player->gain(null,null, VP*$this->player->path->getGainEvacuate());
        
        $last = $this->player->path->getGainExtraEvacuate();
        if($tot+$last>= 30)
        {
            $last = 30 - $tot;
        }
        
        $this->player->vp+= $last;
        self::DbQuery("UPDATE player SET vp = {$this->player->vp} where player_id = {$this->player_id}");
        anachrony::$instance->notifyAllPlayers( "counter", '', array(
            'id' => "vp_".$this->player_id,
            'nb' => $this->player->vp
        ) );
        $this->player->updateScore();
        
        if($last>0)
        {
            anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} gains ${nb} <div id="mineres_0" class="mineres anaicon res12"></div>'), array(
                'player_id' => $this->player->player_id,
                'player_name' => $this->player->player_name,
                'nb' => $last
            ) );
        }
    }
    
    
    function log()
    {
        anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} uses Evacuation'),
            array(
                "player_id" => $this->player->player_id,
                "player_name" => $this->player->player_name,
            )
            );
    }
}