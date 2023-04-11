/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * anachrony implementation : © <Nicolas Gocel> <nicolas.gocel@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * anachrony.js
 *
 * anachrony user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
    "./modules/anahelper"
],
function (dojo, declare) {
    return declare("bgagame.anachrony", ebg.core.gamegui, {
        constructor: function(){

            this.height = dojo.marginBox("gboard").h;
            dojo.connect(window, "onresize", this, dojo.hitch(this, "adaptViewportSize"));
            this.helper = new bgagame.anahelper();
            this.setupdone = false;
        },
        
        adaptViewportSize : function() {
            var bodycoords = dojo.marginBox("ana-overall");
            var contentWidth = bodycoords.w;
            var rowWidth = 2000;
            if (contentWidth >= rowWidth) {
            	
            	var left = (contentWidth - rowWidth)/2 ;
                dojo.style("gboard",'transform','');
                dojo.style("gboard",'left',left+'px');
                dojo.style("gboard",'height', this.height+'px');
                return;
            }
                        
            var percentageOn1 = contentWidth / rowWidth;
            var left = (bodycoords.w - contentWidth )/2;
            dojo.style("gboard", "transform", "scale(" + percentageOn1 + ")");
             dojo.style("gboard",'left',left+'px');
             dojo.style("gboard",'height', (this.height*percentageOn1)+'px');
        },
        
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */
        
        setup: function( gamedatas )
        {
            this.players = gamedatas.players;
			dojo.query("#side").addClass("side"+gamedatas.side);
			dojo.query("#ana-selection").addClass("side"+gamedatas.side);

			
			if($('endgames').children.length == 0)
			{
			    for( var endgame_id in gamedatas.endgames )
	            {
	                var endgame = gamedatas.endgames[endgame_id];
	                endgame['posx'] = (endgame.type-1) * 10;
	                dojo.place(this.format_block('jstpl_endgame', endgame), $('endgames'));  
	                var html = '<div class="anatooltip">'+this.format_block('jstpl_endgame', endgame)+'<div>'+this.helper.endgames[endgame['type']]+'</div></div>';
	                this.addTooltipHtml( "endgame_"+endgame['id'], html,1000);
	            }
	            for( var superproject_id in gamedatas.superprojects )
	            {
	                var superproject = gamedatas.superprojects[superproject_id];
	                superproject['posx'] = ((superproject.type-1)%10) * 11.11;
	                superproject['posy'] = Math.floor((superproject.type-1)/10) * 100;
	                if(superproject.player_id != null)
	                {
	                	dojo.place(this.format_block('jstpl_superproject', superproject), $('phbuilding'+superproject.category+superproject.location+'_'+superproject.player_id));
	                }
	                else
	                {
	                	dojo.place(this.format_block('jstpl_superproject', superproject), $('phsuperprojectboard'+superproject['id']));	
	                }
	                var html = '<div class="anatooltip">'+this.format_block('jstpl_superproject', superproject)+'<div>'+_(this.helper.superprojects[superproject['type']])+'</div></div>';
	                this.addTooltipHtml( "superproject_"+superproject['id'], html,1000);
	            }
	                        
	            for( var building_id in gamedatas.buildings )
	            {
	                var building = gamedatas.buildings[building_id];
	                building['posy'] = (Math.floor(building.type/100)-1) * 25;
	                building['posx'] = (building.type%100 - 1) * -126;
	                var type = building['type'] ;
	                if(building['type'] == 515)
	                {
	                	building['type'] += "_"+building['id'];
	                }
	                
	                dojo.place(this.format_block('jstpl_building', building), $('phbuilding'+building.category+building.location+'_'+building.player_id), building.location_arg);  
	           
	                var html = '<div class="anatooltip">'+this.format_block('jstpl_building', building)+'<div>'+_(this.helper.buildings[type])+'</div></div>';
	                this.addTooltipHtml( "building_"+building['id'], html,1000);
	            }   
			}
			
			if(gamedatas.setupDone == 1)
			{
            // Setting up player boards
			var nbplayers = 0;
            for( var player_id in gamedatas.players )
            {
                var player = gamedatas.players[player_id];

    			dojo.query("#player"+player['id']).addClass("path"+player['path']);
    			dojo.query("#player"+player['id']).addClass("evacuation_side"+player['evacuation_side']);

				dojo.query("#player"+player['id']).addClass("leader"+player['leader']);
    			if(player['leader']>0)
    			{
            		dojo.place(this.format_block('jstpl_phpath', {id: "phleader"+player['path'] }), $('leader'+player['id']));
    			}
    			else
    			{
    				dojo.query("#leader"+player['id']).addClass("hidden");
                	this.addTooltipHtml('leader_'+player['id']+"_1", _(this.helper.leaders[player['path']+1]),1000); 
                	this.addTooltipHtml('leader_'+player['id']+"_2", _(this.helper.leaders[player['path']+2]),1000);    				
    			}
                dojo.place(dojo.clone( dojo.byId("ressources_"+player['id']) ),$('player_board_'+player['id']));
    			for(let i=1;i<=6;i++)
    			{
    	            dojo.query('.res_'+player['id']+'_'+i).forEach(function(selectTag){ selectTag.innerHTML = player['res'+i]; }); 
    			}
	            dojo.query('.vp_'+player['id']).forEach(function(selectTag){ selectTag.innerHTML = player['vp']; }); 
    			dojo.place($("temporal_"+player['id']), $("temporal_"+player['id']+"_"+player['temporal']));
    			dojo.place($("moral_"+player['id']), $("moral_"+player['id']+"_"+player['moral']));
    			
    			if($("eye_"+player['id']) == null)
    			{
    				dojo.place(this.format_block('jstpl_view', player), dojo.query('#player_board_'+player['id']+" .player_score")[0]);
    			}
    			
         		for(let i=1;i<=player.anomalies;i++)
    			{
                	dojo.place(this.format_block('jstpl_anomaly'), $('anomalies_'+player['id']));        			
    			}
         		
            	dojo.place(this.format_block('jstpl_path', player), $('chronologyTokens'+player['chronology'])); 
            	if(player['evacuation'] > 0)
         		{
                	dojo.place(this.format_block('jstpl_path', player), $('phevacuate'+player['evacuation']));          			
         		}
            	nbplayers++;
            	this.addTooltipHtml('leader'+player['id'], _(this.helper.leaders[player['path']+player['leader']]),1000);	
				var html = _(this.helper.paths[player['path']]);
			
				html += '<br/><br/><b>'+_('EVACUATION CONDITIONS')+'</b><br/><br/>'+_(this.helper.evacuations[player['evacuation_side']][player['path']]);
				
				if(gamedatas.side == 'B')
				{
					html += '<br/><br/><b>'+_('ASYMMETRIC PLAYER BOARDS — “B” SIDE')+'</b><br/><br/>'+ _(this.helper.boardsB[player['path']]);
				}
				this.addTooltipHtml('playerboard'+player['id'], html ,1000);
    				
    			
            }
        	dojo.place(this.format_block('jstpl_token3p'), $('phevacuate'+nbplayers)); 
            
            if(nbplayers<=3)
            {
             	dojo.query(".extrahexa").forEach(dojo.destroy); 
            }

            for( var vortex_id in gamedatas.vortexs )
            {
                var vortex = gamedatas.vortexs[vortex_id];
                if(vortex.location == 0)
                {
                	dojo.place(this.format_block('jstpl_vortex', vortex), $('vortexs_'+vortex.player_id));                   	
                }
                else
                {
                	dojo.place(this.format_block('jstpl_vortex', vortex), $('phchronology'+vortex.location+vortex.location_arg));                 	
                }
                var id = vortex['id'];
                vortex['id'] = vortex['id']+"tooltip"; 
                var html = '<div class="anatooltip">'+this.format_block('jstpl_vortex', vortex)+'<div>'+_(this.helper.warptiles)+'</div></div>';
                this.addTooltipHtml('vortex_'+id, html,1000);
            }
            for( var exosuit_id in gamedatas.exosuits )
            {
                var exosuit = gamedatas.exosuits[exosuit_id];
                dojo.place(this.format_block('jstpl_exosuit', exosuit), $(exosuit.location));  
            }

            for( var mineres_id in gamedatas.mineres )
            {
                var mineres = gamedatas.mineres[mineres_id];
                dojo.place(this.format_block('jstpl_res', mineres), $("phmineres"+mineres.id));  
            }
            for( var b_id in gamedatas.breakthroughs )
            {
                var breakthrough = gamedatas.breakthroughs[b_id];
                dojo.place(this.format_block('jstpl_breakthrough', breakthrough), $(breakthrough.location));  
            }
                 

            for( var worker_id in gamedatas.workers )
            {
                var worker = gamedatas.workers[worker_id];
                dojo.place(this.format_block('jstpl_worker', worker), $(worker.location));  
            }

            for( var path_id in gamedatas.paths )
            {
                var path = gamedatas.paths[path_id];
                dojo.place(this.format_block('jstpl_path', path), $(path.location));  
            }

			dojo.query("#token3p").addClass("after_impact"+gamedatas.after_impact);
			dojo.query("#evacuation").addClass("after_impact"+gamedatas.after_impact);
			

            for( var block_id in gamedatas.blocked )
            {
                var block = gamedatas.blocked[block_id];
                if(block.type == 0)
                {
                	block.type = 41;
                }
                block['posx'] = ((block.type-1)%10) * 20;
                block['posy'] = Math.floor((block.type-10)/10) * 33.33;
                
                dojo.style(block.location+"_blocked", {
         			"background-position": block['posx']+"% "+block['posy']+"%"
                 	});
            }
			
			for( var player_id in gamedatas.players )
            {
                var player = gamedatas.players[player_id];
	            this.refreshWorkers("sleeping_"+player['id']);
	            this.refreshWorkers("awake_"+player['id']);
            }
			
			if(gamedatas.score.length>0)
			{
				dojo.query("#score").removeClass("hidden");
				var score = gamedatas.score;
	        	for(let i = 0;i<score.length;i++)
	        	{
	        		for(let j=0;j<score[i].length;j++)
        			{
	        			if(j!=9)
	        			{
	        				dojo.place(this.format_block('jstpl_score', {top: 40+ 70 * j, left: 172 + 159 * i, text:score[i][j]}), $('score'));
	        			}
        			}
	        	}
			}

			for(const action of ["construct", "research", "recruit", "mine","council","trade","purify","evacuate"])
			{
				for(let j=1;j<=3;j++)
				{
					var html = _(this.helper.actions[action]);
					
					for( var block_id in gamedatas.blocked )
		            {
		                var block = gamedatas.blocked[block_id];
		                if(block.type != 0 && block.type%10 != 6 && block.location == 'ph'+action+'_'+j)
		                {
		                	html += "<br/><br/><b>"+_('COLLAPSING CAPITAL TILES')+"</b><br/>";
		                	html += _(this.helper.blockeds[block.type]);
		                }
		            }
					
					
					this.addTooltipHtml('ph'+action+'_'+j, html ,1000);
				}
				this.addTooltipHtml('capitalaction_'+action, _(this.helper.actions[action]),1000);
				this.addTooltipHtml('action_'+action, _(this.helper.actions[action]),1000);
				this.addTooltipHtml('ph'+action, _(this.helper.actions[action]),1000);
			}
			
			this.addTooltipHtml('phevacuation', _(this.helper.actions["evacuate"]),1000);
			this.addTooltipHtmlToClass('supply', _(this.helper.actions["supply"]),1000);
			this.addTooltipHtmlToClass('force', _(this.helper.actions["force"]),1000);
			this.addTooltipHtml('phfirstplayer', _("First player"),1000);
			
       	 	dojo.query("#newRound").forEach(function(selectTag){ selectTag.innerHTML = _("New turn"); });       	 	           

       	 	dojo.query(".hexagon").connect('onclick', this, 'onSelect' ); 
       	 	dojo.query(".vortex").connect('onclick', this, 'onSelect' ); 
       	 	dojo.query(".worker").connect('onclick', this, 'onSelect' ); 
       	 	dojo.query(".building").connect('onclick', this, 'onSelect' ); 
       	 	dojo.query(".superproject").connect('onclick', this, 'onSelect' ); 
       	 	dojo.query(".phbuilding").connect('onclick', this, 'onSelect' ); 
       	 	dojo.query(".breakthrough").connect('onclick', this, 'onSelect' ); 
       	 	dojo.query(".mineres").connect('onclick', this, 'onSelect' ); 
       	 	dojo.query(".capitalaction").connect('onclick', this, 'onSelect' ); 
       	 	dojo.query(".phworker").connect('onclick', this, 'onSelect' ); 
       	 	dojo.query(".phpath").connect('onclick', this, 'onSelect' ); 
       	 	dojo.query(".chronology").connect('onclick', this, 'onSelect' ); 
       	 	dojo.query(".selectleader .leader").connect('onclick', this, 'onSelect' ); 
       	 	

			dojo.query("#side").removeClass("hidden");
			dojo.query("#gboard").removeClass("hidden");
			dojo.query("#ana-selection").addClass("hidden");
			}
			else
			{
				for( var player_id in gamedatas.players )
	            {
					if(gamedatas.players[player_id].path != 0)
					{
						dojo.query("#selectrow"+gamedatas.players[player_id].path).removeClass("hidden");	
						if(gamedatas.players[player_id].leader != 0)
						{
							dojo.query("#selectrow"+gamedatas.players[player_id].path).addClass("grayed");							
						}
					}
	            }
				
				for(let i=1;i<=4;i++)
				{
					for(let j=1;j<=2;j++)
					{
						this.addTooltipHtml('leader'+i+j, _(this.helper.leaders[i*10+j]),1000);						
					}
					
					var html = _(this.helper.paths[i]);
					html += '<br/><br/><b>'+_('EVACUATION CONDITIONS - SIDE A')+'</b><br/><br/>'+_(this.helper.evacuations[1][i]);
					html += '<br/><br/><b>'+_('OR EVACUATION CONDITIONS - SIDE B')+'</b><br/><br/>'+_(this.helper.evacuations[2][i]);
					if(gamedatas.side == 'B')
					{
						html += '<br/><br/><b>'+_('ASYMMETRIC PLAYER BOARDS — “B” SIDE')+'</b><br/><br/>'+ _(this.helper.boardsB[i]);
					}
					this.addTooltipHtml('playerboard'+i, html ,1000);
					
				}

				if(!gamedatas.setupAuto)
				{
					dojo.query(".selectrow").removeClass("hidden");
				}
				dojo.query("#side").addClass("hidden");
    			dojo.query("#gboard").addClass("hidden");
    			dojo.query("#ana-selection").removeClass("hidden");
	       	 	dojo.query(".selectrow").connect('onclick', this, 'onSelect' ); 				
			}

            // Setup game notifications to handle (see "setupNotifications" method below)
            if(!this.setupdone)
            {
            	this.setupNotifications(); 
            }
        },
       

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
        	this.stateName = stateName;
			dojo.query(".selectable").removeClass("selectable");
			dojo.query(".selected").removeClass("selected");
            switch( stateName )
            {

            case "setup":
            	this.setup(args.args);
            	break;
            	
            case "selectionLeader":
            	if( this.isCurrentPlayerActive() )
	    		{
	            	this.args = args.args;
	            	this.selected = null;
        			dojo.query("#selectleader"+this.player_id).removeClass("hidden");    
        			dojo.query("#selectleader"+this.player_id+" .leader").addClass("selectable");        		
	    		}
            	break;

            case 'selection':
            	if( this.isCurrentPlayerActive() )
	    		{
	            	this.args = args.args;
	            	this.selected = null;
	            	for( var sid in args.args.selectable )
	                {
	        			dojo.query("#"+sid).addClass("selectable");
	                }
	    		}
            	break; 

            case 'playerTurn':
	            if( this.isCurrentPlayerActive() )
	    		{
	            	if(args.args.titleyou != null)
	            	{
	            		$('pagemaintitletext').innerHTML = 	this.format_string_recursive(_(args.args.titleyou).replace('${you}', this.divYou()), args.args);  
	    			}
	            	if(args.args.selectable != null)
	            	{
		            	for( var sid in args.args.selectable )
		                {
		        			dojo.query("#"+sid).addClass("selectable");
		                }
	            	}
	            	this.args = args.args;
	            	this.selected = null;
	            	
	            	if(args.args.highlight != null)
	            	{
		            	dojo.query("#"+args.args.highlight).addClass("selected");		                
	            	}
	    		}
	            else
	    		{
	            	if(args.args.title != null)
	            	{
	    			$('pagemaintitletext').innerHTML = this.format_string_recursive(_(args.args.title).replace('${actplayer}', this.divActPlayer()), args.args);  
	            	}
	            }
            	break;

             case 'vortex': 
            	 if( this.isCurrentPlayerActive() )
	 	    		{
            		  dojo.query("#vortexs_"+this.player_id+" .vortex").addClass("selectable");
		                
	 	    		}
            	 break;

        	case 'client_selectTarget':
	    		if(this.isCurrentPlayerActive())
	    		{
	    			$('pagemaintitletext').innerHTML = 	this.format_string_recursive(_(this.args.selectable[this.selected].titleyou).replace('${you}', this.divYou()), args.args);  
	    			dojo.query("#"+this.selected).addClass("selected");
	    			for( var t_id in this.args.selectable[this.selected]['target'] )
	                {
	    				var id = this.args.selectable[this.selected]['target'][t_id];
	        			dojo.query("#"+id).addClass("selectable");
	                }
	    		}
	    		break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            
            switch( stateName )
            {
           
           
            case 'dummmy':
                break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
        	 if( this.isCurrentPlayerActive() )
             {            
                 switch( stateName )
                 {

 	                case 'playerTurn':   
 		                for( var nb in args.buttons )
 		                {
 		                	if(args.buttons[nb].startsWith("res"))
 		                	{
 		                		var full =  parseInt(args.buttons[nb].replace(/[^0-9]/g, ''));
 		                		var lbl = "";
 		                		for(var i=1;i<=12;i++)
 		                		{
 		                			var isolated = Math.floor(full/Math.pow(10,i))%10;
 		                			if(isolated>0)
 		                			{
 		                				lbl += isolated+this.format_block('jstpl_res', { id: 0, type:i }); ;
 		                			}
 		                		}
 		                		this.addActionButton( args.buttons[nb], lbl, 'onButton', null, null, col); 
 		                	}
 		                	else if(args.buttons[nb].startsWith("shape") || args.buttons[nb].startsWith("icon"))
 		                	{
 		                		this.addActionButton( args.buttons[nb], this.format_block('jstpl_dice', {dice:args.buttons[nb]}), 'onButton', null, null,'gray');  		                		
 		                	}
 		                	else
 		                	{
	 		                	var col = 'gray';
	 		                	if(args.buttons[nb] != "Pass" && args.buttons[nb] != "Undo"  && args.buttons[nb] != "Skip" )
	 		                	{
	 		                		col = 'blue';
	 		                	}
	 		        			this.addActionButton( args.buttons[nb], _(this.helper.buttons[args.buttons[nb]]), 'onButton', null, null, col); 
 		                	}
 		                }
 		                break;

 	                case 'client_selectTarget':
 	                	for( var t_id in this.args.selectable[this.selected]['target'] )
 		                {
 		    				var id = this.args.selectable[this.selected]['target'][t_id];
 		    				if(id.startsWith("workertype"))
 		    				{
 		                		var typ =  parseInt(id.replace(/[^0-9]/g, ''));
 		                		var lbl = this.format_block('jstpl_res', { id: 0, type:typ+6 }) ;
 		                		this.addActionButton( id, lbl, 'onButton'); 
 		    				}
 		                }
 	                	
 	                	this.addActionButton( 'cancel', _("Cancel") ,'onOpCancel', null, null, 'gray' );
 	                    break;

 	                case 'vortex':
 	                    this.addActionButton( 'finishSelect', _("Finish Selection") ,'onFinishSelect'); 
 	                    break;
 	                    
 	                case 'selection':
 	                    this.addActionButton( 'seeSelect', '<i class="fa fa-eye"></i>&nbsp;'+_("See board") ,'onSelection'); 
 	                    break;
                 }
             }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
                
        refreshWorkers:function(zoneid)
        {       	
        	
        	for(const i of [3,1,2,4])
        	{
	        	dojo.query('#'+zoneid+' .worker'+i).forEach(function(selectTag){
	                dojo.place(selectTag.id, zoneid, 'first');
	        	});
        	}        	
        	
            var height = dojo.marginBox(zoneid).h;
        	var workerHeight = 84;
        	
        	var totWorkers = dojo.query('#'+zoneid+' .worker').length;
        	if(totWorkers != 0)
        	{
		        var spacing = 8;
	        	var spaceLeft = height - totWorkers * (workerHeight + spacing);
	        	if(spaceLeft>0)
	        	{	
		        	var index = 0;
		        	dojo.query('#'+zoneid+' .worker').forEach(function(selectTag){

		        		var topx = (index * ( workerHeight + spacing ));
		        		dojo.style(selectTag.id, {
		        			'z-index': totWorkers-index,
			                 bottom: topx + "px"
			             });
		        		index++;
		        	});
	        	}
	        	else
	        	{
	        		var margin = (spaceLeft + workerHeight) / (totWorkers);			
		        	var index = 0;
		        	dojo.query('#'+zoneid+' .worker').forEach(function(selectTag){
		        		
		        		var topx = (index * (workerHeight + margin));
		        		dojo.style(selectTag.id, {
		        			'z-index': totWorkers-index,
			                 bottom: topx + "px"
			             });
		        		index++;
		        	});
	        	}
        	}
        	
        },



        divYou : function() {
            var color = this.players[this.player_id].color;
            var color_bg = "";
            var you = "<span style=\"font-weight:bold;color:#" + color + ";" + color_bg + "\">" + _("You") + "</span>";
            return you;
        },

        replacePlayerName : function(log) {
        	
        	for(var key in this.players)
        	{
        		var player = this.players[key];
        		var color = player.player_color;
                var name = player.player_name;
                var color_bg = "";
                log = log.replace(name, "<span style=\"font-weight:bold;color:#" + color + ";" + color_bg + "\">" + name + "</span>");
        	}
            
            return log;
        },
        divActPlayer : function() {        	
            var color = this.players[this.getActivePlayerId()].color;
            var name = this.players[this.getActivePlayerId()].name;
            var color_bg = "";
            var you = "<span style=\"font-weight:bold;color:#" + color + ";" + color_bg + "\">" + name + "</span>";
            return you;
        },
        
        format_string_recursive : function(log, args) {
            try {
                if (log && args && !args.processed) {
                    args.processed = true;

                    if(args['cost'] != null)
                    {
                    	var full =  parseInt(args['cost']);
                		var lbl = "";
                		for(var i=1;i<=14;i++)
                		{
                			var isolated = Math.floor(full/Math.pow(10,i))%10;
                			if(isolated>0)
                			{
                				lbl += isolated+this.format_block('jstpl_res', { id: 0, type:i }); ;
                			}
                		}
                    	args['cost'] = lbl;
                    }

                    if(args['worker_name'] != null)
                    {
                    	args['worker_name'] = _(this.helper.workers[args['worker_name']]);
                    }
                    if(args['dice'] != null)
                    {
                    	args['dice'] = this.format_block('jstpl_dice', {dice:args['dice']});
                    }
                    if(args['dice2'] != null)
                    {
                    	args['dice2'] = this.format_block('jstpl_dice', {dice:args['dice2']});
                    }
                    if(args['paradox'] != null)
                    {
                    	var t = "";
                    	var split = args['paradox'].split(",");
                    	for(var nb in split)
                    	{
                    		t += this.format_block('jstpl_paradox', {nb:split[nb]});
                    	}
                    	
                    	args['paradox'] = t;
                    }
                }
            } catch (e) {
                console.error(log,args,"Exception thrown", e.stack);
            }
            return this.inherited(arguments);
        },
        
        attachToNewParentNoDestroy: function (mobile_in, new_parent_in, relation, place_position) {

            const mobile = $(mobile_in);
            const new_parent = $(new_parent_in);

            var src = dojo.position(mobile);
            if (place_position)
                mobile.style.position = place_position;
            dojo.place(mobile, new_parent, relation);
            mobile.offsetTop;//force re-flow
            var tgt = dojo.position(mobile);
            var box = dojo.marginBox(mobile);
            var cbox = dojo.contentBox(mobile);
            var left = box.l + src.x - tgt.x;
            var top = box.t + src.y - tgt.y;

            mobile.style.position = "absolute";
            mobile.style.left = left + "px";
            mobile.style.top = top + "px";
            box.l += box.w - cbox.w;
            box.t += box.h - cbox.h;
            mobile.offsetTop;//force re-flow
            return box;
        },
        
        ///////////////////////////////////////////////////
        //// Player's action
        onSelection: function(evt)
        {        	 
             // Preventing default browser reaction
             dojo.stopEvent( evt );
             

  			if(dojo.hasClass("gboard","hidden"))
  			{
  	 			dojo.query("#gboard").removeClass("hidden");
  	 			dojo.query("#ana-selection").addClass("hidden");  				
  			}
  			else
  			{
  	 			dojo.query("#gboard").addClass("hidden");
  	 			dojo.query("#ana-selection").removeClass("hidden");  				
  			}
 			
        },
        
        
        onSelect: function(evt)
        {
        	 
             // Preventing default browser reaction
             dojo.stopEvent( evt );
             
             if( !this.isCurrentPlayerActive() || !(evt.currentTarget.classList.contains('selectable')) )
             {   return; }
                          
              if(this.stateName == "vortex")
              {
            	dojo.query("#"+event.currentTarget.id).toggleClass("selected"); 
              }
              else
              {
            	            
             
              if(this.selected != null)
            	  {
      			dojo.query(".selectable").removeClass("selectable");
            	  this.ajaxcall( "/anachrony/anachrony/actSelect.html", { 
                      lock: true,
                      arg1: this.selected,
                      arg2: event.currentTarget.id
                   }, 
                   this, function( result ) {}, function( is_error) {} ); 
            	  }
              else
            	  {
            	  
	             	this.selected = event.currentTarget.id;
	             	 
	             	 if(this.args.selectable[this.selected]['target'] == null)
	             	{
	         			dojo.query(".selectable").removeClass("selectable");
	                	 this.ajaxcall( "/anachrony/anachrony/actSelect.html", { 
	                         lock: true,
	                         arg1: this.selected
	                      }, 
	                      this, function( result ) {}, function( is_error) {} ); 
	             	}
	             	 else
	             	{
	         			dojo.query(".selectable").removeClass("selectable");
	    	             this.setClientState("client_selectTarget", {
	    	 				descriptionmyturn: _(this.args.selectable[this.selected]['titleyou']),
	    	 				args: {}
	    	 			});
	             	}
            	  }
              
              }  
            	 
        },

        onButton:function(event)
        {
            dojo.stopEvent( event );  
            if(this.isCurrentPlayerActive() && this.checkAction( "select" ) ) {
            	 if(this.selected != null)
	           	  {
         			dojo.query(".selectable").removeClass("selectable");
	           	  this.ajaxcall( "/anachrony/anachrony/actSelect.html", { 
	                     lock: true,
	                     arg1: this.selected,
	                     arg2: event.currentTarget.id
	                  }, 
	                  this, function( result ) {}, function( is_error) {} ); 
	           	  }
            	 else
            		 {
         			dojo.query(".selectable").removeClass("selectable");
	               	 this.ajaxcall( "/anachrony/anachrony/actSelect.html", { 
		                    lock:true,
		                    arg1:event.currentTarget.id,
		                },this, function( result ) {
		                }, function( is_error ) { } );
            		 }
            }
        },
        
        onFinishSelect:function(event)
        {
            dojo.stopEvent( event );  
            if(this.isCurrentPlayerActive() && this.checkAction( "vortex" ) ) {
            	
            	var chosen = "";
            	var nb = 0;
           	 	dojo.query(".selected").forEach(function(selectTag){ nb++; chosen += ","+selectTag.id.substring(7); });
           	 	if(nb>2)
           	 	{
           	 		this.showMessage(_('You can only select up to 2 vortex tiles'), 'error');
           	 	}
           	 	else
           	 	{
           	 	chosen = chosen.substring(1);
               	 this.ajaxcall( "/anachrony/anachrony/actVortex.html", { 
	                    lock:true,
	                    arg1:chosen,
	                },this, function( result ) {
	                }, function( is_error ) { } );
           	 	}
            }
        },
        
        onOpCancel: function(evt)
        {
            this.restoreServerGameState();
        },

        
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your anachrony.game.php file.
        
        */
        setupNotifications: function()
        {
            dojo.subscribe( 'newturn', this, "notif_newturn" );
            dojo.subscribe( 'newworker', this, "notif_newworker" );
            dojo.subscribe( 'newpath', this, "notif_newpath" );
            dojo.subscribe( 'counter', this, "notif_counter" );
            dojo.subscribe( 'counterid', this, "notif_counterid" );
            dojo.subscribe( 'move', this, "notif_move" );
            dojo.subscribe( 'remove', this, "notif_remove" );
            dojo.subscribe( 'setparadox', this, "notif_setparadox" );
            dojo.subscribe( 'revealbuilding', this, "notif_revealbuilding" );
            dojo.subscribe( 'newanomaly', this, "notif_newanomaly" );
            dojo.subscribe( 'impact', this, "notif_impact" );
            dojo.subscribe( 'returnblocked', this, "notif_returnblocked" );
            dojo.subscribe( 'chooseleader', this, "notif_chooseleader" );
            dojo.subscribe( 'choosepath', this, "notif_choosepath" );
            dojo.subscribe( 'finalscore', this, "notif_finalscore" );
            this.notifqueue.setSynchronous( 'finalscore', 1000 );  
            this.setupdone = true;
        },  
        
        notif_finalscore: function( notif )
        {
			dojo.query("#score").removeClass("hidden");
        	dojo.place(this.format_block('jstpl_score', {top: 40+ 70 * notif.args.j, left: 172 + 159 * notif.args.i, text:notif.args.score}), $('score'));
        },

        notif_choosepath: function( notif )
        {
			dojo.query("#selectrow"+notif.args.pathnb).addClass("grayed");

			dojo.style(dojo.query("#player_name_"+notif.args.player_id+" a")[0], {
     			"color": "#"+notif.args.color
             	}); 
			dojo.style(dojo.query("#player"+notif.args.player_id+" .pname")[0], {
     			"color": "#"+notif.args.color
             	}); 
			this.players[notif.args.player_id].color = notif.args.color;
        },
        
        notif_chooseleader: function( notif )
        {
			dojo.query(".selectleader").addClass("hidden");
			dojo.query("#player"+notif.args.player_id).removeClass("leader0");
			dojo.query("#player"+notif.args.player_id).addClass("leader"+notif.args.leadernb);
			dojo.place(this.format_block('jstpl_phpath', {id: "phleader"+notif.args.path}), $('leader'+notif.args.player_id));
			dojo.query("#leader"+notif.args.player_id).removeClass("hidden");
        	this.addTooltipHtml('leader'+notif.args.player_id, _(this.helper.leaders[notif.args.path+notif.args.leadernb]),1000);	
			
        },
        
        notif_returnblocked: function( notif )
        {

        	var block = notif.args.blocked;
            block['posx'] = ((block.type-1)%10) * 20;
            block['posy'] = Math.floor((block.type-10)/10) * 33.33;

            dojo.style(block.location+"_blocked", {
     			"background-position": block['posx']+"% "+block['posy']+"%"
             	}); 
        },
        
        notif_newturn: function( notif )
        {
        	var element = document.getElementById("newRound");
        	element.classList.remove("popit");
        	element.classList.remove("hidden");
        	 void element.offsetWidth;
        	 element.classList.add("popit");
        	 
        	 if(notif.args.superproject != null)
        	{
	        	 var superproject = notif.args.superproject;
	             var posx = ((superproject.type-1)%10) * 11.11;
	             var posy = Math.floor((superproject.type-1)/10) * 100;
	             dojo.style("superproject_"+notif.args.superproject.id, {
	     			"background-position": posx+"% "+posy+"%"
	             	});

	             superproject.posx = posx;
	             superproject.posy = posy;
                var html = '<div class="anatooltip">'+this.format_block('jstpl_superproject', superproject)+'<div>'+_(this.helper.superprojects[superproject['type']])+'</div></div>';
                this.addTooltipHtml( "superproject_"+superproject['id'], html,1000);
                dojo.query("#superproject_"+notif.args.superproject.id+" .phworkersuperproject").forEach(function(selectTag){ selectTag.id = "phsuperproject"+notif.args.superproject.type; }); 
                
        	}

         	dojo.query(".phmineres .anaicon").forEach(dojo.destroy); 
         	for( var mineres_id in notif.args.mineres )
            {
                var mineres = notif.args.mineres[mineres_id];
                dojo.place(this.format_block('jstpl_res', mineres), $("phmineres"+mineres.id));  
            }

         	dojo.query(".phrecruitworker .worker").forEach(dojo.destroy); 
            for( var worker_id in notif.args.workers )
            {
                var worker = notif.args.workers[worker_id];
                dojo.place(this.format_block('jstpl_worker', worker), $(worker.location));  
            }

       	 	dojo.query(".phrecruitworker .worker").connect('onclick', this, 'onSelect' );
       	 	dojo.query(".mineres").connect('onclick', this, 'onSelect' ); 
        },
        
        notif_revealbuilding: function( notif )
        {        	 
        	 var building = notif.args.building;
        	 building['posy']  = (building.category-1) * 25;
        	 building['posx'] = (building.type - 1 - (building.category*100)) * -126;
             dojo.style("building_"+building.id, {
     			"background-position":  building['posx']+"px "+ building['posy'] +"%",
                "transition":"0s"
             	});
             var html = '<div class="anatooltip">'+this.format_block('jstpl_building', building)+'<div>'+_(this.helper.buildings[building.type])+'</div></div>';
             this.addTooltipHtml( "building_"+building['id'], html,1000);
             
             dojo.query("#building_"+building['id']+" .phworker")[0].id = "phbuilding"+building.type;
        },

        notif_counter: function(notif)
        {
       	 	dojo.query("."+notif.args.id).forEach(function(selectTag){ selectTag.innerHTML = notif.args.nb; });
        },
        
        notif_counterid: function(notif)
        {
       	 	dojo.query("#"+notif.args.id).forEach(function(selectTag){ selectTag.innerHTML = notif.args.nb; });
        },

        notif_newworker: function(notif)
        {
            dojo.place(this.format_block('jstpl_worker', notif.args.worker), $(notif.args.worker.location)); 
       	 	dojo.query("#worker_"+notif.args.worker.id).connect('onclick', this, 'onSelect' ); 
            this.refreshWorkers(notif.args.worker.location); 
        }, 

        notif_newpath: function(notif)
        {
            dojo.place(this.format_block('jstpl_path', notif.args.path), $(notif.args.path.location)); 
        }, 
        
        notif_newanomaly: function(notif)
        {
            var building = notif.args.anomaly;
            building['posy'] = (Math.floor(building.type/100)-1) * 25;
            building['posx'] = (building.type%100 - 1) * -126;
            if(building['type'] == 515)
            {
            	building['type'] += "_"+building['id'];
            }
            dojo.place(this.format_block('jstpl_building', building), $('phbuilding'+building.category+building.location+'_'+building.player_id), building.location_arg);  

       	 	dojo.query("#phbuilding515_"+building['id']).connect('onclick', this, 'onSelect' ); 

            var html = '<div class="anatooltip">'+this.format_block('jstpl_building', building)+'<div>'+_(this.helper.buildings[515])+'</div></div>';
            this.addTooltipHtml( "building_"+building['id'], html,1000);
        }, 
        
        notif_setparadox: function(notif)
        {
        	dojo.query("#anomalies_"+notif.args.player_id+" .anomaly").forEach(dojo.destroy); ;
        	for(let i=1;i<=notif.args.nb;i++)
			{
            	dojo.place(this.format_block('jstpl_anomaly'), $('anomalies_'+notif.args.player_id));        			
			}
        	
        },        

        notif_remove: function( notif )
        {
        	this.fadeOutAndDestroy(notif.args.id);
        },
        
        notif_move: function( notif )
        {
        	var targetrefresh = null;
        	if(notif.args.parent.startsWith("awake") || notif.args.parent.startsWith("sleeping"))
        	{
        		targetrefresh = notif.args.parent;
        	}
        	else {

            	var element = document.getElementById(notif.args.mobile);
            	if(element.parentNode.id.startsWith("awake") || element.parentNode.id.startsWith("sleeping"))
            	{
            		targetrefresh = element.parentNode.id;
            	}
        	}

        	this.attachToNewParentNoDestroy( notif.args.mobile, notif.args.parent, notif.args.position );
            
        	element = document.getElementById(notif.args.mobile);
        	void element.offsetWidth;
        	dojo.style(notif.args.mobile, {
        			"left":"0px",
	                 "top": "0px",
	                 "transition":"0.5s"
             });
        	
        	setTimeout(() => {
            	dojo.style(notif.args.mobile, {
            		"position":"",
        			"left":"",
	                 "top": "",
	                 "bottom": "",
	                 "right": ""
             });
            	if(targetrefresh != null) {
            		this.refreshWorkers(targetrefresh);
            	}
            	
            	
        		}, "600");
        },
        
        notif_impact: function( notif )
        {
        	dojo.query("#evacuation").addClass("after_impact1");
        	dojo.query("#token3p").addClass("after_impact1");
        	for( var block_id in notif.args.blocked )
            {
                var block = notif.args.blocked[block_id];
                if(block.type == 0)
                {
                	block.type = 41;
                }
                block['posx'] = ((block.type-1)%10) * 20;
                block['posy'] = Math.floor((block.type-10)/10) * 33.33;

                dojo.style(block.location+"_blocked", {
         			"background-position": block['posx']+"% "+block['posy']+"%"
                 	}); 
                
                if(block.type != 41 && block.type%10 != 6)
                {
	                var action = block.location.split("_")[0].substring(2);
	                
	                var html = _(this.helper.actions[action]);
                	html += "<br/><br/><b>"+_('COLLAPSING CAPITAL TILES')+"</b><br/>";
                	html += _(this.helper.blockeds[block.type]);
					this.addTooltipHtml(block.location, html ,1000);
                }
                
            }
        },
   });             
});
