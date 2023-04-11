<?php 

class building515 extends building
{
    public $vp = -3;
    public $actionCost = 2*W;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->addPendingSub($this->player_id, $this->worker_id, get_class($this), "spay",null, 0);
    }
    
    public function argspay($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose how to pay');
        $ret['titleyou'] = clienttranslate('${you} must choose how to pay');
        
        $tab = [G,U,T];
        
        if($parg1 == 0)
        {
            $tab[] = N;
        }
        
        foreach($tab as $res)
        {
            if($this->player->checkCost($res))
            {
                $ret['buttons'][] = 'res'.$res;
                $ret['selectable'][$res] = array();
            }
        }
        
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        return $ret;
    }
    
    public function dospay($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->pay(null,null,$varg1);
        if($parg1<1 && $varg1 != "res".N)
        {
            anachrony::$instance->addPendingSub($this->player_id, $this->worker_id, get_class($this), "spay",null, $parg1+1);
        }
        else
        {            
            $worker = self::getObjectFromDB("select * from worker where id=".$this->worker_id);
            $building_id = explode("_", $worker['location'])[1];
            
            self::DbQuery("delete from worker where id = {$worker['id']}");
            anachrony::$instance->notifyAllPlayers( "remove", '', array(
                'id' => 'worker_'.$worker['id']
            ) );
            
            self::DbQuery("delete from building where id = {$building_id}");
            anachrony::$instance->notifyAllPlayers( "remove", '', array(
                'id' => 'building_'.$building_id
            ) );
            
            $this->player->updateScore();
        }
        
    }
    
    public function getVP()
    {
        return parent::getVP() + ($this->player->hasActiveBuilding(406)?2:0);
    }
}