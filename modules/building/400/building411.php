<?php 

class building411 extends building
{
    public $vp = 2;
    
    public function canDo($worker, $index = "", $ignoreFull = false)
    {
        return false;
    }
        
    function argsecondChoice($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may set the second Research die for 1 <div id="mineres_0" class="mineres anaicon res5"></div>');
        $ret['titleyou'] = clienttranslate('${you} may set the second Research die for 1 <div id="mineres_0" class="mineres anaicon res5"></div>');
                
        if (strpos($parg1, 'icon') === 0) {
            for($shape=1;$shape<=3;$shape++)
            {
                $ret['buttons'][] = 'shape'.$shape;
                $ret['selectable']['shape'.$shape] = array();
            }
        }
        else
        {
            for($icon=1;$icon<=5;$icon++)
            {
                $ret['buttons'][] = 'icon'.$icon;
                $ret['selectable']['icon'.$icon] = array();
            }
        }
        
        $ret['buttons'][] = 'Skip';
        $ret['selectable']['Skip'] = array();
        
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        
        return $ret;
    }
    
    function dosecondChoice($parg1, $parg2, $varg1, $varg2 = null)
    {
        $act = new research();
        $act->worker_id = $this->worker_id;
        $act->extra = $this->extra;
        if($varg1 == "Skip")
        {
            $act->do("skip411", $parg2, $parg1, $varg2 = null);
        }
        else
        {
            $act->dochoice($parg1, $parg2, $varg1, $varg2 = null);
        }
    }
    
    
    
}