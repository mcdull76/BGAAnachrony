<?php 

class building213 extends building
{
    public $vp = 2;
    public $workerTypeRequired = ENGINEER;
    
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
        if($parg1<1)
        {
            anachrony::$instance->addPendingSub($this->player_id, $this->worker_id, get_class($this), "spay",null, $parg1+1);
        }
        else
        {
            $this->player->gain(null,null,2*E);            
        }
        
    }
}