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
 * stats.inc.php
 *
 * anachrony game statistics description
 *
 */

$stats_type = array(

    // Statistics global to table
    "table" => array(

        "turns_number" => array("id"=> 10,
                    "name" => totranslate("Number of turns"),
                    "type" => "int" ), 
    ),
    
    // Statistics existing for each player
    "player" => array(
        
        "building" => array("id"=> 10,
            "name" => totranslate("Points gain with buildings"),
            "type" => "int" ),
        "superproject" => array("id"=> 11,
            "name" => totranslate("Points gain with superprojects"),
            "type" => "int" ),
        "anomalies" => array("id"=> 12,
            "name" => totranslate("Points gain with anomalies"),
            "type" => "int" ),
        "travel" => array("id"=> 13,
            "name" => totranslate("Points gain with time travels"),
            "type" => "int" ),
        "moral" => array("id"=> 14,
            "name" => totranslate("Points gain with moral"),
            "type" => "int" ),
        "bt" => array("id"=> 15,
            "name" => totranslate("Points gain with breakthroughs"),
            "type" => "int" ),
        "tokens" => array("id"=> 16,
            "name" => totranslate("Points gain with victory point tokens"),
            "type" => "int" ),
        "warp" => array("id"=> 17,
            "name" => totranslate("Points gain with warp tiles"),
            "type" => "int" ),
        "end" => array("id"=> 18,
            "name" => totranslate("Points gain with endgame conditions"),
            "type" => "int" ),
    
/*
        Examples:    
        
        
        "player_teststat1" => array(   "id"=> 10,
                                "name" => totranslate("player test stat 1"), 
                                "type" => "int" ),
                                
        "player_teststat2" => array(   "id"=> 11,
                                "name" => totranslate("player test stat 2"), 
                                "type" => "float" )

*/    
    )

);
