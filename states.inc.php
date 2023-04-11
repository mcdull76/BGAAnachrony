<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * anachrony implementation : © <Nicolas Gocel> <nicolas.gocel@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 * 
 * states.inc.php
 *
 * anachrony game states description
 *
 */

if ( !defined('STATE_PLAYER_TURN') )
{
    
    define("STATE_SELECTION",2);
    define("STATE_SELECTION_NEXT",3);
    
    define("STATE_SELECTION_LEADER",9);
    define("STATE_SELECTION_LEADER_NEXT",10);
    
    define("STATE_SETUP",4);
    define("STATE_PLAYER_TURN",5);
    define("STATE_PENDING",6);
    define("STATE_CLEANUP",7);
    define("STATE_VORTEX",8);
    define("STATE_DUMMY",98);
    define("STATE_END_GAME",99);
}
 
$machinestates = array(

    // The initial state. Please do not modify.
    1 => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array( "" => STATE_SELECTION )
    ),
    
    STATE_SELECTION => array(
        "name" => "selection",
        "description" => clienttranslate('${actplayer} must select a path'),
        "descriptionmyturn" => clienttranslate('${you} must select your path'),
        "type" => "activeplayer",
        "args" => "argSelection",
        "action" => "stTestAuto",
        "possibleactions" => array( "select"),
        "transitions" => array( "auto" => STATE_SELECTION_LEADER, "next" => STATE_SELECTION_NEXT, "zombiePass" => STATE_SELECTION_NEXT)
    ),
    
    STATE_SELECTION_NEXT => array(
        "name" => "selectioNext",
        "description" => '',
        "type" => "game",
        "action" => "stSelectionNext",
        "transitions" => array("next" => STATE_SELECTION, "setup"=>STATE_SETUP)
    ), 
    
    STATE_SETUP => array(
        "name" => "setup",
        "description" => '',
        "type" => "game",
        "action" => "stSetup",
        "args" => "argSetup",
        "transitions" => array("next" => STATE_SELECTION_LEADER )
    ),  
    
    STATE_SELECTION_LEADER => array(
        "name" => "selectionLeader",
        "description" => clienttranslate('${actplayer} must select a leader'),
        "descriptionmyturn" => clienttranslate('${you} must select your leader'),
        "type" => "activeplayer",
        "args" => "argSelectionLeader",
        "possibleactions" => array( "select"),
        "transitions" => array( "next" => STATE_SELECTION_LEADER_NEXT, "zombiePass" => STATE_SELECTION_LEADER_NEXT)
    ),
    
    STATE_SELECTION_LEADER_NEXT => array(
        "name" => "selectionLeaderNext",
        "description" => '',
        "type" => "game",
        "action" => "stSelectionLeaderNext",
        "transitions" => array("next" => STATE_SELECTION_LEADER, "setup"=>STATE_PENDING)
    ), 
    
    STATE_PENDING=> array(
        "name" => "pending",
        "description" => '',
        "type" => "game",
        "action" => "stPending",
        "updateGameProgression" => true,
        "transitions" => array("next" => STATE_CLEANUP, "player"=>STATE_PLAYER_TURN, "same" => STATE_PENDING, "vortex"=>STATE_VORTEX)
    ),
    
    STATE_PLAYER_TURN => array(
		"name" => "playerTurn",
		"description" => clienttranslate('${actplayer} must take an action or Pass'),
		"descriptionmyturn" => clienttranslate('${you} must take an action or pass'),
        "type" => "activeplayer",
        "args" => "argPlayerTurn",
        "possibleactions" => array( "select"),
        "transitions" => array( "next" => STATE_PENDING, "zombiePass" => STATE_PENDING)
    ),
    
    STATE_CLEANUP => array(
        "name" => "cleanup",
        "description" => '',
        "type" => "game",
        "action" => "stCleanup",
        "transitions" => array("next" => STATE_PENDING, "end"=> STATE_END_GAME)
    ),
    
    STATE_VORTEX =>  array (
        'name' => 'vortex',
        'type' => 'multipleactiveplayer',
        'description' => clienttranslate('4. Warp phase : Other players may choose up to 2 warp tiles'),
        'descriptionmyturn' => clienttranslate('4. Warp phase : ${you} may choose up to 2 warp tiles'),
        'possibleactions' => array ('vortex' ),
        'transitions' => array('next' => STATE_PENDING, "zombiePass" => STATE_PENDING)
    ),
   
    STATE_DUMMY => array(
        "name" => "dummy",
        "description" => clienttranslate('${actplayer} must take an action or Pass'),
        "descriptionmyturn" => clienttranslate('${you} must take an action or pass'),
        "type" => "activeplayer",
        "possibleactions" => array( "select"),
        "transitions" => array( "next" => STATE_PENDING, "zombiePass" => STATE_PENDING)
    ),
    
    // Final state.
    // Please do not modify (and do not overload action/args methods).
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )

);



