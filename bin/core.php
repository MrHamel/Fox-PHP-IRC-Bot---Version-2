<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
set_time_limit(0);
echo("   __            _           _   \r\n");
echo("  / _|          | |         | |  \r\n");
echo(" | |_ ___ __  __| |__   ___ | |_ \r\n");
echo(" |  _/ _ \\\\ \\/ /| '_ \\ / _ \\| __|\r\n");
echo(" | || (_) |>  < | |_) | (_) | |_ \r\n");
echo(" |_| \___//_/\_\|_.__/ \___/ \__|\r\n");
echo("fox php bot - flatfile version!\r\n");
if ($config->debug) { echo("Debug:\r\n"); }
declare(ticks=1);
pcntl_signal(SIGTERM, "sig_handler");
pcntl_signal(SIGHUP, "sig_handler");
require_once("../db/flatfile.php");
$db = new Flatfile();
$db->datadir = "db/";

	class fox {
		var $socket;
		var $ex = array();

		function __construct()
		{	
			$this->connect();
			$this->b = "\2";
			$this->o = "\2";
			$this->main();
		}
		function main()
		{
			global $db;
			global $config;
			$this->ownerhost = $config->ownerhost;
			while (true)
			{
				$this->date = date("n j Y g i s a");
				if (!$this->socket) {
					die("server failed\n");
				}

				$data = fgets($this->socket, 4096);
				if ($config->debug) { echo("[~R] ".$data); }
				flush();
				$this->ex = explode(' ', $data);

				foreach ($this->ex as &$trim)
				{
					$trim = trim($trim);
				}

				if ($this->ex[0] == 'PING')
				{
					$this->send_data('PONG', $this->ex[1]);
				}

				if ($this->ex[1] == '376' || $this->ex[1] == '422')
				{
					$this->idandjoin();
				}
				// PRIVMSG ~~~~~~~~~~~~
				if ($this->ex[0] == 'ERROR') {
					if (!$this->restart) {
						sleep(5); $this->connect();
					}
				}
					if ($this->ex[1] == 'PRIVMSG') {
		//CTCP VERSION
		if(preg_match("/:(.+?)!(.+?)@(.+?) PRIVMSG ".$config->serv_nick." :\\001(VERSION|version)\\001/", $data, $matches)){
			$this->notice($matches[1], "\001VERSION ".$config->ctcp_version."\001");
		}
		//CTCP TIME
		elseif(preg_match("/:(.+?)!(.+?)@(.+?) PRIVMSG ".$config->serv_nick." :\\001(TIME|time)\\001/", $data, $matches)){
			$this->notice($matches[1], "\001TIME ".date("h:i A")."\001");
		}
		//CTCP PING
		elseif(preg_match("/:(.+?)!(.+?)@(.+?) PRIVMSG ".$config->serv_nick." :\\001(PING|ping) (.+?)\\001/", $data, $matches)){
			$nicktime = $matches[5];
			$nowtime = shell_exec("date +%N");
			$pingtime = ($nowtime - $nicktime);
			$output = substr($pingtime, 9, -2);
			$this->notice($matches[1], "\001PING ".$matches[5]."\001");
		        
                }

					preg_match("/^:(.*?)!(.*?)@(.*?)[\s](.*?)[\s](.*?)[\s]:(.*?)$/", $data, $rawdata);
					$nick = $rawdata[1];
       					$ident = $rawdata[2];
        				$host = $rawdata[3];
        				$msg_type = $rawdata[4];
        				$chan = $rawdata[5]; 
        				$args = trim($rawdata[6]);
					$this->nick[$data] = $rawdata[1];
					$this->ident[$data] = $rawdata[2];
					$this->host[$data] = $rawdata[3];
					$this->h[$this->nick[$data]] = $this->host[$data];
					# this is messy and wastes resources, yet necessary
					$this->channel[$data] = $rawdata[5]; 
					$this->args[$data] = trim($rawdata[6]);
	if (!$this->ignore[$this->h[$this->nick[$data]]]) {
					if (strtolower($this->ex[3]) == ":".$config->cp."ignore") {
						if ($this->ownerhost[strtolower($this->host[$data])]) {
							if ($this->ex[4]) {
								if ($this->h[$this->ex[4]]) {
									$this->ignore[$this->h[$this->ex[4]]] = true;
									$this->privmsg($this->channel[$data], $this->b."*!*@".$this->h[$this->ex[4]].$this->o." was added to the ignore list.");
								} else { $this->privmsg($this->channel[$data], "I do not recognize ".$this->b.$this->ex[4].$this->o."."); }			
							} else { $this->notice($this->nick[$data], "Incorrect syntax. ".$this->b.".ignore <nick>"); }			
						} else { $this->notice($this->nick[$data], "You are not a fox admin."); }					
					}	
					elseif (strtolower($this->ex[3]) == ":".$config->cp."unignore") {
						if ($this->ownerhost[strtolower($this->host[$data])]) {
							if ($this->ex[4]) {
								if ($this->h[$this->ex[4]]) {
									unset($this->ignore[$this->h[$this->ex[4]]]);
									$this->privmsg($this->channel[$data], $this->b."*!*@".$this->h[$this->ex[4]].$this->o." was removed from the ignore list.");
								} else { $this->privmsg($this->channel[$data], "I do not recognize ".$this->b.$this->ex[4].$this->o."."); }			
							} else { $this->notice($this->nick[$data], "Incorrect syntax. ".$this->b.".ignore <nick>"); }			
						} else { $this->notice($this->nick[$data], "You are not a fox admin."); }					
					}	
					elseif (strtolower($this->ex[3]) == ":".$config->cp."assign") {
						if ($this->ownerhost[strtolower($this->host[$data])]) {
							if ($this->ex[4]) {
								$this->assign(trim($this->ex[4]), $this->channel[$data], $this->nick[$data]);
							} else { $this->notice($this->nick[$data], "Incorrect syntax. ".$this->b.".assign <channel>"); }	
						}
						else { $this->notice($this->nick[$data], "You are not a fox admin."); }	
					}					       
					elseif (strtolower($this->ex[3]) == ":".$config->cp."calc") {
						$this->privmsg($this->channel[$data], "Google Calculator: ".$this->googlecalc(substr($this->args[$data], 5)));
					}
                                        elseif (strtolower($this->ex[3]) == ":".$config->cp."portscan") {
                                                $this->privmsg($this->channel[$data], $this->Portscan(substr($this->args[$data], 5), substr($this->args[$data], 5), $this->channel[$data]));
                                        }
					elseif (strtolower($this->ex[3]) == ":".$config->cp."weather") {
                                                $this->privmsg($this->channel[$data], $this->WunderGround(substr($this->args[$data], 5), $this->nick[$data]));
                                        }
					elseif (strtolower($this->ex[3]) == ":".$config->cp."acro") {
                                                $this->privmsg($this->channel[$data], $this->Acronyms(substr(explode(' ', $this->args[$data])), $this->channel[$data]));
                                        }
					elseif (strtolower($this->ex[3]) == ":".$config->cp."urban") {
                                                $this->privmsg($this->channel[$data], $this->UrbanDict(substr($this->args[$data], 5), $this->channel[$data]));
                                        }
					elseif (strtolower($this->ex[3]) == ":".$config->cp."wiki") {
                                                $this->privmsg($this->channel[$data], $this->Wikipedia(substr($this->args[$data], 5), $this->channel[$data]));
                                        }
					elseif (strtolower($this->ex[3]) == ":".$config->cp."ping") {
                                                 include_once("timer.php");
						 $nowtime = shell_exec("date +%N");
						$timer = new Timer();
						$this->privmsg($this->nick[$data], "\001PING ".$nowtime."\001");
						if ($this->ex[6] == $nowtime) {
						$calc = ($timer->elapsed() * 1000);
						$output = substr($calc, 0, -10);
							$this->privmsg($this->channel[$data], $this->nick[$data].", Your ping is ".$output." ms."); }
					 }

                                        elseif (strtolower($this->ex[3]) == ":".$config->cp."unassign") {
						if ($this->ownerhost[strtolower($this->host[$data])]) {
							if ($this->ex[4]) {
								$this->unassign(trim($this->ex[4]), $this->channel[$data], $this->nick[$data]);
							} else { $this->notice($this->nick[$data], "Incorrect syntax. ".$this->b.".unassign <channel>"); }	
						}
						else { $this->notice($this->nick[$data], "You are not a fox admin."); }	
					}
					elseif (strtolower($this->ex[3]) == ":".$config->cp."clear") {
						if ($this->ownerhost[strtolower($this->host[$data])]) {
							if (strtolower($this->ex[4]) == "quotes") {
								mysql_query("delete from quotes");
								$this->privmsg($this->channel[$data], "[\2Clear\2] All quotes have been deleted.");
							} elseif (strtolower($this->ex[4]) == "pics") {
								mysql_query("delete from pics");
								$this->privmsg($this->channel[$data], "[\2Clear\2] All pictures have been deleted.");
							} elseif (strtolower($this->ex[4]) == "vars") {
								unset($db);
								unset($this->h);
								$db = new Flatfile();
								$db->datadir = "db/";
								$this->usg = $this->b_convert(memory_get_usage());
								$this->rusg = $this->b_convert(memory_get_usage(true));
								$this->privmsg($this->channel[$data], "[\2Clear\2] All unnecessary variables have been unset. Memory usage is now ".$this->usg." of allocated ".$this->rusg);
								$this->clear();
								unset($this->usg); unset($this->rusg);
							} else {
								$this->privmsg($this->channel[$data], "Incorrect syntax. ".$this->b.".clear quotes|pics");
							}
							
						} else { $this->notice($this->nick[$data], "You are not a fox admin."); }					
					}
					elseif (strtolower($this->ex[3]) == ":".$config->cp."eval") {
						if ($this->ownerhost[strtolower($this->host[$data])]) {
							$eval = substr($this->args[$data], 6);
							$this->notice($this->nick[$data], $this->b."[Eval] ".$this->o.$eval);
							eval($eval);
						}
					}
					elseif (strtolower($this->ex[3]) == ":".$config->cp."help") {
						$this->privmsg($this->channel[$data], ".add <command> <response>".$this->b." | ".$this->o.".del <command>".$this->b." | ".$this->o.".info <command>".$this->b." | ".$this->o.".amnt".$this->b." | ".$this->o.".addme <command> <response>".$this->b." | ".$this->o.".addact <command> <response>".$this->b." | ".$this->o.".infoact <command>".$this->b." | ".$this->o.".addactme <command> <response>".$this->b." | ".$this->o.".delact <command>".$this->b." | ".$this->o.".q search|add|del|<id>"); $this->privmsg($this->channel[$data], ".pic add|del|<id>".$this->b." | ".$this->o.".eval <eval>".$this->b." | ".$this->o.".ignore <nick>".$this->b." | ".$this->o.".unignore <nick>".$this->b." | ".$this->o.".clear pics|quotes".$this->b." | ".$this->o.".assign <channel>".$this->b." | ".$this->o.".unassign <channel>".$this->b." | ".$this->o.".addwild <command> <response>".$this->b." | ".$this->o.".addwildme <command> <response> ".$this->b."|".$this->o." .restart"); $this->privmsg($this->channel[$data], "fox-ff (fox flatfile) version 1 - .delact, .addact, .infoact are incomplete (the former method fails)");
					}
					elseif (strtolower($this->ex[3]) == ":".$config->cp."invite") {
						if (isset($this->ex[4])) {
							$this->send_data("INVITE ".$this->ex[4]." ".$this->channel[$data]);
						} else { $this->privmsg($this->channel[$data], "Incorrect syntax. ".$this->b.".invite <nick>."); }
					}
					elseif (strtolower($this->ex[3]) == ":".$config->cp."amnt") {
						$num = 0;
						$check2 = new AndWhereClause();
						$check2->add(new SimpleWhereClause(8,'=',$config->network,'strcasecmp'));
						$check2->add(new SimpleWhereClause(7,'=',$this->channel[$data],'strcasecmp'));
						$check2 = $db->selectWhere('commands.db',$check2);
						foreach ($check2 as $check2) {
							$num++;
						}
						$this->privmsg($this->channel[$data], "I respond to ".$this->b.$num.$this->o." commands in ".$this->b.$this->channel[$data].$this->o.".");
					}
					elseif (strtolower($this->ex[3]) == ":".$config->cp."add") {
						$this->command[$data] = $this->ex[4];
						$this->command[$data] = str_replace("[]", " ", $this->command[$data]);
						$check = $db->selectUnique('channels.db', 1, strtolower($this->channel[$data]));
						$check2 = new AndWhereClause();
						$check2->add(new SimpleWhereClause(8,'=',$config->network,'strcasecmp'));
						$check2->add(new SimpleWhereClause(7,'=',$this->channel[$data],'strcasecmp'));
						$check2->add(new SimpleWhereClause(1,'=',$this->command[$data],'strcasecmp'));
						$check2 = $db->selectWhere('commands.db',$check2);

							if ($check) {
							if (!$check2) {
								if (isset($this->ex[4]) && isset($this->ex[5])) {
									$this->response[$data] = substr($this->args[$data], 6+strlen($this->ex[4]));
									$this->privmsg($this->channel[$data], "If someone says \"".$this->b.$this->command[$data].$this->o."\", I will now respond with \"".$this->b.$this->response[$data].$this->o."\" in ".$this->b.$this->channel[$data].$this->o.".");

									$db->insertWithAutoId('commands.db',0, array(
										0,
										1 => $this->command[$data],
										2 => $this->response[$data],
										3 => $this->nick[$data],
										4 => $this->date,
										5 => "false",
										6 => "false",
										7 => $this->channel[$data],
										8 => $config->network
										)
									);
								}
								else {
									$this->privmsg($this->channel[$data], "Incorrect syntax. ".$this->b.".add <command> <response>");
								}
							}
							else {
								$this->privmsg($this->channel[$data], "I already respond to that in ".$this->b.$this->channel[$data].$this->o.".");
							}
							}
							else {
								$this->privmsg($this->channel[$data], $this->b.$this->channel[$data].$this->o." is not in my database.");
							}
					}
					elseif (strtolower($this->ex[3]) == ":".$config->cp."addwild") {
						$this->command[$data] = $this->ex[4];
						$this->command[$data] = str_replace("[]", " ", $this->command[$data]);
						$check = $db->selectUnique('channels.db', 1, strtolower($this->channel[$data]));
						$check2 = new AndWhereClause();
						$check2->add(new SimpleWhereClause(8,'=',$config->network,'strcasecmp'));
						$check2->add(new SimpleWhereClause(7,'=',$this->channel[$data],'strcasecmp'));
						$check2->add(new SimpleWhereClause(1,'=',$this->command[$data],'strcasecmp'));
						$check2 = $db->selectWhere('commands.db',$check2);

							if ($check) {
							if (!$check2) {
								if (isset($this->ex[4]) && isset($this->ex[5])) {
									$this->response[$data] = substr($this->args[$data], 10+strlen($this->ex[4]));
									$this->privmsg($this->channel[$data], "If someone says \"".$this->b.$this->command[$data].$this->o."\", I will now respond with \"".$this->b.$this->response[$data].$this->o."\" in ".$this->b.$this->channel[$data].$this->o.".");
						$db->insertWithAutoId('commands.db',0, array(
										0,
										1 => $this->command[$data],
										2 => $this->response[$data],
										3 => $this->nick[$data],
										4 => $this->date,
										5 => "false",
										6 => "true",
										7 => $this->channel[$data],
										8 => $config->network
										)
									);
								}
								else {
									$this->privmsg($this->channel[$data], "Incorrect syntax. ".$this->b.".add <command> <response>");
								}
							}
							else {
								$this->privmsg($this->channel[$data], "I already respond to that in ".$this->b.$this->channel[$data].$this->o.".");
							}
							}
							else {
								$this->privmsg($this->channel[$data], $this->b.$this->channel[$data].$this->o." is not in my database.");
							}
					}
					elseif (strtolower($this->ex[3]) == ":".$config->cp."addwildme") {
						$this->command[$data] = $this->ex[4];
						$this->command[$data] = str_replace("[]", " ", $this->command[$data]);
						$check = $db->selectUnique('channels.db', 1, strtolower($this->channel[$data]));
						$check2 = new AndWhereClause();
						$check2->add(new SimpleWhereClause(8,'=',$config->network,'strcasecmp'));
						$check2->add(new SimpleWhereClause(7,'=',$this->channel[$data],'strcasecmp'));
						$check2->add(new SimpleWhereClause(1,'=',$this->command[$data],'strcasecmp'));
						$check2 = $db->selectWhere('commands.db',$check2);

							if ($check) {
							if (!$check2) {
								if (isset($this->ex[4]) && isset($this->ex[5])) {
									$this->response[$data] = substr($this->args[$data], 12+strlen($this->ex[4]));
									$this->privmsg($this->channel[$data], "If someone says \"".$this->b.$this->command[$data].$this->o."\" anywhere in their message, I will now respond with the action \"".$this->b.$this->response[$data].$this->o."\" in ".$this->b.$this->channel[$data].$this->o.".");

									$db->insertWithAutoId('commands.db',0, array(
										0,
										1 => $this->command[$data],
										2 => $this->response[$data],
										3 => $this->nick[$data],
										4 => $this->date,
										5 => "true",
										6 => "true",
										7 => $this->channel[$data],
										8 => $config->network
										)
									);
								}
								else {
									$this->privmsg($this->channel[$data], "Incorrect syntax. ".$this->b.".add <command> <response>");
								}
							}
							else {
								$this->privmsg($this->channel[$data], "I already respond to that in ".$this->b.$this->channel[$data].$this->o.".");
							}
							}
							else {
								$this->privmsg($this->channel[$data], $this->b.$this->channel[$data].$this->o." is not in my database.");
							}
					}
					elseif (strtolower($this->ex[3]) == ":".$config->cp."addme") {
						$this->command[$data] = $this->ex[4];
						$this->command[$data] = str_replace("[]", " ", $this->command[$data]);
						$check = $db->selectUnique('channels.db',1,strtolower($this->channel[$data]));
						$check2 = new AndWhereClause();
						$check2->add(new SimpleWhereClause(8,'=',$config->network,'strcasecmp'));
						$check2->add(new SimpleWhereClause(7,'=',$this->channel[$data],'strcasecmp'));
						$check2->add(new SimpleWhereClause(1,'=',$this->command[$data],'strcasecmp'));
						$check2 = $db->selectWhere('commands.db',$check2);

							if ($check) {
							if (!$check2) {
								if (isset($this->ex[4]) && isset($this->ex[5])) {

									$this->response[$data] = substr($this->args[$data], 8+strlen($this->ex[4]));
									$this->privmsg($this->channel[$data], "If someone says \"".$this->b.$this->command[$data].$this->o."\", I will now respond with the action \"".$this->b.$this->response[$data].$this->o."\" in ".$this->b.$this->channel[$data].$this->o.".");

									$db->insertWithAutoId('commands.db',0, array(
										0,
										1 => $this->command[$data],
										2 => $this->response[$data],
										3 => $this->nick[$data],
										4 => $this->date,
										5 => "true",
										6 => "false",
										7 => $this->channel[$data],
										8 => $config->network
										)
									);
								}
								else {
									$this->privmsg($this->channel[$data], "Incorrect syntax. ".$this->b.".add <command> <response>");
								}
							}
							else {
								$this->privmsg($this->channel[$data], "I already respond to that in ".$this->b.$this->channel[$data].$this->o.".");
							}
							}
							else {
								$this->privmsg($this->channel[$data], $this->b.$this->channel[$data].$this->o." is not in my database.");								$this->privmsg($this->channel[$data], $this->b.$this->channel[$data].$this->o." is not in my database.");
							}
					}
					elseif (strtolower($this->ex[3]) == ":".$config->cp."del") {
						$this->command[$data] = substr($this->args[$data], 5);
						$check = $db->selectUnique('channels.db', 1, strtolower($this->channel[$data]));
						$check2 = new AndWhereClause();
						$check2->add(new SimpleWhereClause(8,'=',$config->network,'strcasecmp'));
						$check2->add(new SimpleWhereClause(7,'=',$this->channel[$data],'strcasecmp'));
						$check2->add(new SimpleWhereClause(1,'=',$this->command[$data],'strcasecmp'));
						$check2 = $db->selectWhere('commands.db',$check2);
						if (isset($this->ex[4])) {
							if ($check) {
							if ($check2) {
								$this->privmsg($this->channel[$data], $this->b.$this->command[$data].$this->o." was deleted from the ".$this->b.$this->channel[$data].$this->o." command list.");
								$db->deleteWhere('commands.db', new AndWhereClause(
     								   new SimpleWhereClause(8, '=', $config->network, 'strcasecmp'),
       								   new SimpleWhereClause(7, '=', $this->channel[$data],'strcasecmp'),
       								   new SimpleWhereClause(1, '=', $this->command[$data],'strcasecmp')
								));
							}
							else {
								$this->privmsg($this->channel[$data], $this->b.$this->command[$data].$this->o." does not exist in ".$this->b.$this->channel[$data].$this->o.".");
							}
							}
							else {
								$this->privmsg($this->channel[$data], $this->b.$this->channel[$data].$this->o." is not in my database.");
							}
						}
						else {
							$this->privmsg($this->channel[$data], "Incorrect syntax. ".$this->b.".del <command>");
						}
					}
					elseif (strtolower($this->ex[3]) == ":".$config->cp."info") {
						$this->command[$data] = substr($this->args[$data],6);
						$check = $db->selectUnique('channels.db', 1, strtolower($this->channel[$data]));
						$check2 = new AndWhereClause();
						$check2->add(new SimpleWhereClause(8,'=',$config->network,'strcasecmp'));
						$check2->add(new SimpleWhereClause(7,'=',$this->channel[$data],'strcasecmp'));
						$check2->add(new SimpleWhereClause(1,'=',$this->command[$data],'strcasecmp'));
						$check2 = $db->selectWhere('commands.db',$check2);
						if (isset($this->ex[4])) {
							if ($check) { 
								if ($check2) {
									foreach ($check2 as $check2) {
										$this->privmsg($this->channel[$data], "Command = \"\2".$check2[1]."\2\" | Response = \"\2".$check2[2]."\2\" | Action = \2".$check2[5]."\2 | Wildcard = \2".$check2[6]."\2 | Added by \2".$check2[3]."\2 on \2".$this->convert_date($check2[4]));
									}
								} else { $this->privmsg($this->channel[$data], "\2".$this->command[$data]."\2 does not exist in \2".$this->channel[$data]."\2."); }
							} else { $this->privmsg($this->channel[$data], "\2".$this->channel[$data]."\2 is not in my database."); }
						} else { $this->privmsg($this->channel[$data], "Incorrect syntax. \2.info <command>\2"); }
					}
					elseif (strtolower($this->ex[3]) == ":".$config->cp."t") {
						$this->usg = $this->b_convert(memory_get_usage());
						$this->rusg = $this->b_convert(memory_get_usage(true));
						$uptime = trim(shell_exec("uptime"));
						$this->privmsg($this->channel[$data],"[\2Uptime\2] $uptime");
						$this->privmsg($this->channel[$data],"[\2Memory\2] ".$this->usg." of allocated ".$this->rusg." are being used.");
						unset($this->usg); unset($this->rusg);
					}
					elseif (strtolower($this->ex[3]) == ":".$config->cp."restart") {
						if ($this->ownerhost[strtolower($this->host[$data])]) {
							$file = __FILE__;
							$pid = getmypid();
							$this->restart = true;
							$this->privmsg($this->channel[$data],$this->nick[$data].': k');
							$this->send_data('QUIT :'.$config->quit);
							shell_exec('sleep 1; screen -dm php '.$_SERVER['PHP_SELF']);
						}
					}
					elseif (strtolower($this->ex[3]) == ":".$config->cp."q") {
						if (strtolower($this->ex[4]) == "add") {
							if (isset($this->ex[5])) {
								$this->quote[$data] = substr($this->args[$data], 7);
								$quotes = $db->selectAll('quotes.db');
									$quoteid = 0; foreach ($quotes as $quotes) {
										$quoteid++;
									}
								if (intval($quoteid) != 0) { $quoteid++; } else { $quoteid = 1; }
								$this->privmsg($this->channel[$data], $this->b."Added quote #".$quoteid."/".$quoteid.": ".$this->o.$this->quote[$data]);
								$quote[$data] = str_replace("", "", $quote[$data]);
								$db->insertWithAutoId('quotes.db',0,array(
										0,
										1 => $quoteid,
										2 => $this->quote[$data],
										3 => $this->channel[$data],
										4 => $this->nick[$data],
										5 => $this->date
									)
								);
							} else { $this->privmsg($this->channel[$data], "Incorrect syntax. ".$this->b.".q add <quote>"); }
						}
						elseif (strtolower($this->ex[4]) == "del") {
							if (isset($this->ex[5])) {
								if ($this->ownerhost[strtolower($this->host[$data])]) {
								$db->deleteWhere('quotes.db',new AndWhereClause(new SimpleWhereClause(1, '=', $this->ex[5],'strcasecmp'))); $this->privmsg($this->channel[$data],"[\2Delete\2] Deleted matches (if any)");		
								} else { $this->privmsg($this->channel[$data], "You are not a fox admin."); }
							} else { $this->privmsg($this->channel[$data], "Incorrect syntax. ".$this->b.".q del <quote id>"); }
						}
						elseif (strtolower($this->ex[4]) == "rand") {
							$this->quote_rand($this->channel[$data]);
						}
						elseif (strtolower($this->ex[4]) == "amnt") {
							$this->quote_amnt($this->channel[$data]);
						}
						elseif (strtolower($this->ex[4]) == "search") {
							if (isset($this->ex[5])) {
								$search = substr($data,strlen($this->ex[0])+strlen($this->ex[1])+strlen($this->ex[2])+strlen($this->ex[3])+strlen($this->ex[4])+4);
								$search = trim($search);
								$this->quote_search($this->channel[$data],$search);
								} else { $this->privmsg($this->channel[$data],'Incorrect syntax. '."\2.q search <query>\2"); }
						}
						else {
							if ($this->ex[4]) {
								$this->quote_view($this->ex[4], $this->channel[$data]);
							} else { $this->privmsg($this->channel[$data], "Incorrect syntax. ".$this->b.".q add|del|rand|amnt|search <quote>|<quote id>|<string>"); }
						}
					}
					elseif (strtolower($this->ex[3]) == ":".$config->cp."pic") {
						if (strtolower($this->ex[4]) == "add") {
							if (isset($this->ex[5])) {
								$this->quote[$data] = substr($this->args[$data], 9);
								$quotes = $db->selectAll('pics.db');
									$quoteid = 0; foreach ($quotes as $quotes) {
										$quoteid++;
									}
								if (intval($quoteid) != 0) { $quoteid++; } else { $quoteid = 1; }
								$this->privmsg($this->channel[$data], $this->b."Added pic #".$quoteid."/".$quoteid.": ".$this->o.$this->quote[$data]);
								$quote[$data] = str_replace("", "", $quote[$data]);
								$db->insertWithAutoId('pics.db',0,array(
										0,
										1 => $quoteid,
										2 => $this->quote[$data],
										3 => $this->channel[$data],
										4 => $this->nick[$data],
										5 => $this->date
									)
								);
							} else { $this->privmsg($this->channel[$data], "Incorrect syntax. ".$this->b.".q add <quote>"); }
						}
						elseif (strtolower($this->ex[4]) == "search") {
							if (isset($this->ex[5])) {
								$search = substr($data,strlen($this->ex[0])+strlen($this->ex[1])+strlen($this->ex[2])+strlen($this->ex[3])+strlen($this->ex[4])+4);
								$search = trim($search);
								$this->pic_search($this->channel[$data],$search);
							} else { $this->privmsg($this->channel[$data],'Incorrect syntax.'."\2.pic search <query>\2"); }
						}
						elseif (strtolower($this->ex[4]) == "del") {
							if (isset($this->ex[5])) {
								if ($this->ownerhost[strtolower($this->host[$data])]) {
									$db->deleteWhere('pics.db',new AndWhereClause(new SimpleWhereClause(1, '=', $this->ex[5],'strcasecmp'))); $this->privmsg($this->channel[$data],"[\2Delete\2] Deleted matches (if any)");					
								} else { $this->privmsg($this->channel[$data], "You are not a fox admin."); }
							} else { $this->privmsg($this->channel[$data], "Incorrect syntax. ".$this->b.".q del <quote id>"); }
						}
						elseif (strtolower($this->ex[4]) == "rand") {
							$this->pic_rand($this->channel[$data]);
						}
						elseif (strtolower($this->ex[4]) == "amnt") {
							$this->pic_amnt($this->channel[$data]);
						}
						else {
							if (isset($this->ex[4])) {
								$this->pic_view($this->ex[4], $this->channel[$data]);
							} else { $this->privmsg($this->channel[$data], "Incorrect syntax. ".$this->b.".pic search|add|del|rand|amnt <query>|<quote>|<quote id>"); }
						}
					}
					elseif (strtolower($this->ex[3]) == ":".strtolower($config->serv_nick).":") {
						if (strtolower($this->ex[4]) == "eval") {
							if ($this->ownerhost[strtolower($this->host[$data])]) {
								$eval = substr($this->args[$data],strlen($this->ex[3])+5);
								$this->privmsg($this->channel[$data],"[\2Eval\2] ".$eval);
								eval($eval);
							}
						}
						elseif (strtolower($this->ex[4]) == "print_r") {
							if ($this->ownerhost[strtolower($this->host[$data])]) {
								$this->privmsg($this->channel[$data],"[\2print_r\2] ".$this->ex[5]);
								$var = $this->ex[5];
								eval("print_r($var);");
							}
						}
						elseif (strtolower($this->ex[4]) == "say") {
							if ($this->ownerhost[strtolower($this->host[$data])]) {
								$say = substr($this->args[$data],strlen($this->ex[3])+4);
								$this->privmsg($this->channel[$data],$say);
							}
						}
						elseif (strtolower($this->ex[4]) == "tell") {
								if ($this->ownerhost[strtolower($this->host[$data])]) {
								$say = substr($this->args[$data],strlen($this->ex[3])+6+strlen($this->ex[5]));
								$this->privmsg($this->channel[$data],$this->ex[5].": ".$say);
							}
						}
					}
					else {
						$command = new AndWhereClause();
						$command->add(new SimpleWhereClause(8, '=', $config->network, 'strcasecmp'));
						$command->add(new SimpleWhereClause(7, '=', $this->channel[$data], 'strcasecmp'));
						$command->add(new SimpleWhereClause(1, '=', $this->args[$data], 'strcasecmp'));
						$command->add(new SimpleWhereClause(6, '=', "false", 'strcasecmp'));
						$command = $db->selectWhere('commands.db',$command);
						if ($command) {
							foreach ($command as $commandr) {
								$this->response[$data] = str_replace("\$nick", $this->nick[$data], $commandr[2]);
								$this->response[$data] = str_replace("\$capsnick", strtoupper($this->nick[$data]), $this->response[$data]);
								$this->response[$data] = str_replace("\$ident", $this->ident[$data], $this->response[$data]);
								$this->response[$data] = str_replace("\$host", $this->host[$data], $this->response[$data]);
								$this->response[$data] = str_replace("\$sexuality", $this->sexuality($this->nick[$data]), $this->response[$data]);
								$this->response[$data] = str_replace("\$lol", $this->lol(), $this->response[$data]);
								$this->response[$data] = str_replace("\$rand", rand(1,99), $this->response[$data]);


								if ($commandr[5] == "true") {
									$this->privmsg($this->channel[$data], "\001ACTION ".$this->response[$data]."\001");
								}
								else {
								$this->response[$data] = str_replace(" \$line ", "\nPRIVMSG ".$this->channel[$data]." :", $this->response[$data]);
								$this->privmsg($this->channel[$data], $this->response[$data]);
								}
							}
						} else { 
							$ex = explode(" ", $this->args[$data]);
							foreach ($ex as $word) { 
								$this->wild($word,$this->channel[$data], $this->nick[$data], $this->ident[$data], $this->host[$data]);
							}
		}		
}	
				$this->clear();
	}
				} // end privmsg
			}
		}

	// main functions

function sig_handler($signo) 
{

     switch ($signo) {
         case SIGTERM:
		getmypid();
                $this->send_data('QUIT :Received Shutdown Command from Console');
                exit();

             break;
         case SIGHUP:
		getmypid();
                $this->send_data('QUIT :Received Shutdown Command from Console');
                exit();

             break;
         default:
             // handle all other signals
     }

}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


function conclose () {
		getmypid();
                $this->send_data('QUIT :Received Shutdown Command from Console');
                exit();
}

/* Chanserv. */
function CHANSERV($msgContents){
        global $socket;
        $msgData = "CHANSERV ".$msgContents."\n";
        socket_send($socket, $msgData, strlen($msgData), 0);
        PrintData($msgData);
        }
 
/* Nickserv. */
function NICKSERV($msgContents){
        global $socket;
        $msgData = "PRIVMSG NICKSERV ".$msgContents."\n";
        socket_send($socket, $msgData, strlen($msgData), 0);
        PrintData($msgData);
        }
 
/* Random Facts. Chuck, Vin, Mrt. */
function RandomFacts($Person){
        $url = "http://4q.cc/index.php?pid=atom&person=".$Person;
        preg_match_all('/<summary>(.*?)<\/summary>/', file_get_contents($url), $matches);
        return html_entity_decode($matches[1][array_rand($matches[1])]);
        }
 
/* Random Facts. Jack Bauer. */
function JackBauer(){
        $url = "http://www.jackbauerfacts.com/fact/random";
        preg_match('/<div style=".*">Fact ID #\d{1,5}:[\s](.*?)<\/div>/', file_get_contents($url), $matches);
        return html_entity_decode($matches['1']);
        }
 
/* Urban Dictionary. */
function UrbanDict($urban_query, $chan){
        global $font;
        $url = "http://www.urbandictionary.com/define.php?term=" . urlencode($urban_query);
        $contents = file_get_contents($url);
        if (empty($urban_query)){
                PRIVMSG($chan, "Please provide a search query.");
        } elseif (strpos($contents, "<div id='not_defined_yet'>")){
                PRIVMSG($chan, $font['b'].$urban_query.$font['n']." isn't defined yet.");
        } elseif (strpos($contents, "Service Temporarily Unavailable")) {
                PRIVMSG($chan, "Service temporarily unavailable. Please try again later.");
        } else {
                preg_match_all("/<a.*href=.*defin.*term=.*>(.*?)<\/a>/", $contents, $matches);
                $limit = count($matches['0']) < 18 ? count($matches['0']) : 18;
                for($i=0; $i < $limit; $i++){
                        preg_match("/<a.*href=.*defin.*term=.*>(.*?)<\/a>/", $matches['0'][$i], $titles);
                        $urban_titles .= ", ".$titles['1'];
                        }
                $contents = trim(preg_replace('/[\r\n\t ]+/', ' ', $contents));
                preg_match_all("/<div class='definition'>(.*?)<div class='example'>/", $contents, $matches1);
                preg_match_all("/<div class='example'>(.*?)<div class='greenery'>/", $contents, $matches2);
                $num = array_rand($matches1[1]);
                PRIVMSG($chan, $font['b']."Urban Dictionary:".$font['n']." ".ucwords(strtolower($urban_query)));
                sleep(1);
                Truncate($font['b']."Definition:".$font['n']." ".html_entity_decode(strip_tags(trim($matches1[1][$num]))), $chan);
                sleep(1);
                Truncate($font['b']."Example:".$font['n']." ".html_entity_decode(strip_tags(trim($matches2[1][$num]))), $chan);
                sleep(1);
                PRIVMSG($chan, $font['b']."Nearby Titles:".$font['n']." ".substr($urban_titles, 2));
                }
        }
 
/* Weather Underground. */
function WunderGround($WeatherLocation, $user){
        $url = 'http://api.wunderground.com/auto/wui/geo/WXCurrentObXML/index.xml?query='.urlencode($WeatherLocation);
        $s = @simplexml_load_file($url);
        if ($s){
                if ($s->display_location->city != ''){
                        if ($s->observation_time != 'Last Updated on , '){ $lupd = ' Last Updated On ('.str_replace('Last Updated on ', '', $s->observation_time).')'; } else { $lupd = ''; }
                        if ($s->windchill_f != 'NA'){ $feelslike = ' feels like '.$s->windchill_f.'°F/'.$s->windchill_c.'°C'; } else { $feelslike = ''; }
                        if ($s->temp_f != ''){ $temp = ' Temperature is ('.$s->temp_f.'°F/'.$s->temp_c.'°C'.$feelslike.')'; } else { $temp = ''; }
                        if ($s->weather != ''){ $cond = ' Conditions ('.$s->weather.')'; } else { $cond = ''; }
                        if ($s->wind_string != ''){ $wind = ' Wind Temperature and Speed ('.trim($s->wind_string).')'; } else { $wind = ''; }
                        if ($s->relative_humidity != ''){ $hum = ' Humidity ('.$s->relative_humidity.')'; } else { $hum = ''; }
                        if ($s->dewpoint_f != ''){ $dewpnt = ' Dewpoint ('.$s->dewpoint_f.'°F/'.$s->dewpoint_c.'°C)'; } else { $dewpnt = ''; }
                        return $user.': from '.$s->display_location->full.'.'.$lupd.''.$temp.''.$cond.''.$wind.''.$hum.''.$dewpnt;
                } else {
                        return $user.': City Not Found.';
                        }
        } else {
                return $user.': '.$http_response_header[0];
                }
        }
 
/* Acronyms. */ 
function Acronyms($query, $chan){
        if ($query == null){ $this->privmsg($chan, "Please provide a search query.");
        } else{
                $url = "http://acronyms.thefreedictionary.com/".$query;
                preg_match_all('/<*td><td>(.*?)<\/td>/', file_get_contents($url), $matches);
                if (!$matches[1][0]){
                        $this->privmsg($chan, "There were no results for $query");
                } else {
                        $limit1 = count($matches['0']) < 5 ? count($matches['0']) : 5;
                        $limit2 = count($matches['0']) < 10 ? count($matches['0']) : 10;
                        for($i=0; $i < $limit1; $i++){ $result1 .= " | ".html_entity_decode(strip_tags($matches[1][$i])); }
                        for($i=$limit1; $i < $limit2; $i++){ $result2 .= " | ".html_entity_decode(strip_tags($matches[1][$i])); }
                        $this->privmsg($chan, substr($result1, 3)); if ($limit2 > 5) { $this->privmsg($chan, substr($result2, 3)); }
                        }
                }
        }
 
/* Google & Site Search. */
function GoogleSearch($query, $chan, $limit, $siteSearch = null){
        if ($query == null){ PRIVMSG($chan, "Please provide a search query.");
        } else {
                switch($siteSearch){
                        case "discogs":
                                $site = '+site%3Adiscogs.com';
                                break;
                        case "youtube":
                                $site = '+site%3Ayoutube.com';
                                break;
                        case "imdb":
                                $site = '+site%3Aimdb.com';
                                break;
                        case "php":
                                $site = '+site%3Aphp.net';
                                break;
                        default:
                                $site = '';
                                break;
                        }
                $url = 'http://www.google.com/search?q='.urlencode($query).$site;
                preg_match_all('/<h3 class=r>(.|[\r\n])*?<\/h3>/', file_get_contents($url), $matches);
                $limit = count($matches[0]) < $limit ? count($matches[0]) : $limit;
                for($i=0; $i < $limit; $i++){
                        preg_match('/href="(.*?)"/', $matches[0][$i], $matches1);
                        preg_match('/<h3 class=r>(.*?)<\/a>/', $matches[0][$i], $matches2);
                        PRIVMSG($chan , html_entity_decode(strip_tags(str_replace("&#39;", "'", $matches2[1])))." -> \x1f".$matches1[1]);
                        }
                }
        }
 
/* Port Scan. */
function Portscan($host, $port, $chan){
        $fp = @fsockopen($host, $port, $errno, $errstr, 10);
        if($fp){
                $this->privmsg($chan, $host.':'.$port.' OPEN'); 
        } else { 
                $this->privmsg($chan, $host.':'.$port.' CLOSED');
                }
        }
 
/* Google Calculator. */
function googlecalc($query){
        if (!empty($query)){
                $url = "http://www.google.com/search?q=".urlencode($query);
                preg_match('/<h2 class=r style="font-size:138%"><b>(.*?)<\/b><\/h2>/', file_get_contents($url), $matches);
                if (!$matches['1']){
                        return 'Your input could not be processed..';
                } else {
                        return str_replace(array("?^?", "<font size=-2> </font>", " &#215; 10", "<sup>", "</sup>"), array("", "", "e", "^", ""), $matches['1']);
                        }
                }
        }
 
/* Wikipedia. */
function Wikipedia($query, $chan){
        global $font;
        $url = "http://www.google.com/search?q=en.wikipedia.org+".urlencode($query);
        preg_match_all('/<h3 class=r>(.|[\r\n])*?<\/h3>/', @file_get_contents($url), $match);
        for($i=0; $i < 1; $i++){
            preg_match('/href="(.*?)"/', $match['0'][$i], $f_match);
                }
        if(strstr($f_match['1'], 'en.wikipedia.org') == true){ // else we didnt find a match
                $contents = @file_get_contents($f_match['1']);
                preg_match_all('/<p>(.*?)<\/p>/', $contents, $matches);
                $l=8;
                for($i=0; $i < $l; $i++){
                        preg_match('/<p>(.*?)<\/p>/', $matches['0'][$i], $matches1);
                        $matches1[$i] = strip_tags(str_replace(array("<b>", "</b>"), array($font['b'], $font['n']), $matches1[$i]));
                        $matches1[$i] = html_entity_decode(preg_replace("/\[\d{1,2}\]/", "", $matches1[$i]));
                        if (strlen($matches1[$i]) > 110){
                                $l=$i;
                                Truncate($matches1[$i], $chan);
                                }
                        }
                sleep(1);
                $this->privmsg($chan, $font['u']."".$f_match['1']);
        } else {
                $this->privmsg($chan, "No page with that title exists.");
                }
        }
 
/* Split Any Long Message Into Chunks. */
function Truncate($string, $chan, $order = null){
        global $MaxStrlen;
        if($order == 1){ $string = "..." . $string; }
        if (strlen($string) > $MaxStrlen){
                $msg1 = substr($string, 0, $MaxStrlen);
                $end = strrpos($msg1, " ");
                $msg1 = substr($msg1, 0, $end);
                $msg2 = substr($string, $end);
                PRIVMSG($chan, $msg1);
        } else {
                PRIVMSG($chan, $string);
                }
        if (strlen($msg2) > $MaxStrlen){
                Truncate($msg2, $chan, 1);
        } elseif (!empty($msg2)) {
                PRIVMSG($chan, "..." . trim($msg2));
                }
        }

		function wild ($word, $channel, $nick, $ident, $host) {
			global $db;
			global $config;
			if ($config->debug) { echo("checking word for wildcard in $channel: $word\n"); }
			$check = new AndWhereClause();
			$check->add(new SimpleWhereClause(8, '=', $config->network, 'strcasecmp'));
			$check->add(new SimpleWhereClause(7, '=', $channel, 'strcasecmp'));
			$check->add(new SimpleWhereClause(1, '=', $word, 'strcasecmp'));
			$check->add(new SimpleWhereClause(6, '=', 'true', 'strcasecmp'));
			$cr = $db->selectWhere('commands.db',$check);
					if ($cr) {
						foreach ($cr as $cr) {
							$response = str_replace("\$nick", $nick, $cr[2]);
							$response = str_replace("\$capsnick", strtoupper($nick), $response);
							$response = str_replace("\$ident", $ident, $response);
							$response = str_replace("\$host", $host, $response);
							$response = str_replace("\$sexuality", $this->sexuality($nick), $response);
							$response = str_replace("\$lol", $this->lol(), $response);
							$response = str_replace("\$rand", rand(1,99), $response);

							if ($cr[5] == "false") {
							$response = str_replace(" \$line ", "\nPRIVMSG ".$channel." :", $response);
							$this->privmsg($channel, $response);
							} else {
							$this->privmsg($channel,"\001ACTION ".$response."\001");
							}
						}
					return true;
					} else { return false; }
		}

		function assign ($channel, $rchannel, $nick) {
			global $config;
			global $db;
			$c = new AndWhereClause();
			$c->add(new SimpleWhereClause(1, '=', $channel, 'strcasecmp'));
			$c->add(new SimpleWhereClause(4, '=', $config->network, 'strcasecmp'));
			$c = $db->selectWhere('channels.db',$c);
			$check = str_split($channel);
				if ($check[0] == "#") {
					if (!$c) {
						$this->join($channel);
						$this->privmsg($rchannel, $this->b.$channel.$this->o." has been added to my database.");
				$db->insertWithAutoId('channels.db',0, array(
						0,
						1 => strtolower($channel),
						2 => $this->date,
						3 => $nick,
						4 => $config->network
					)
				);
					} else { $this->privmsg($rchannel, $this->b.$channel.$this->o." is already in my database."); }
				} else { $this->privmsg($rchannel, $this->b.$channel.$this->o." is not a valid channel name."); }
		}

		function unassign ($channel, $rchannel, $nick) {
			global $config;
			global $db;
			$c = $db->selectUnique('channels.db',1,strtolower($channel));
			$check = str_split($channel);
				if ($check[0] == "#") {
					if ($c) {
						$this->part($channel);
						$this->privmsg($rchannel, $this->b.$channel.$this->o." has been removed from my database.");
						$s = new AndWhereClause();
						$s->add(new SimpleWhereClause(1, '=', $channel, 'strcasecmp'));
						$db->deleteWhere('channels.db',$s);
					} else { $this->privmsg($rchannel, $this->b.$channel.$this->o." is not in my database."); }
				} else { $this->privmsg($rchannel, $this->b.$channel.$this->o." is not a valid channel name."); }
		}
		function quote_search ($channel,$query) {
			global $db;
                        $quotes = $db->selectAll('quotes.db');
			$this->privmsg($channel,"All quotes containing \"\2$query\2\":");
			$i = 0;
                        foreach ($quotes as $quote) {
				if (stripos($quote[2],$query) !== false) {
					$this->quote_view($quote[1],$channel);
					usleep(500000);
					$i++;
				}
                        }
			$this->privmsg($channel,"End search \"\2$query\2\". $i quotes were found.");

		}
                function pic_search ($channel,$query) {
                        global $db;
                        $quotes = $db->selectAll('pics.db');
                        $this->privmsg($channel,"All pictures containing \"\2$query\2\":");
                        $i = 0;
                        foreach ($quotes as $quote) {
                                if (stripos($quote[2],$query) !== false) {
                                        $this->pic_view($quote[1],$channel);
                                        usleep(500000);
                                        $i++;
                                }
                        }
                        $this->privmsg($channel,"End search \"\2$query\2\". $i pictures were found.");

                }
		function quote_view ($quote, $channel) {
			global $db;
			$totalquotes = 0;
			$quotes = $db->selectAll('quotes.db');
			foreach ($quotes as $quotes) {
				$totalquotes++;
			}
			if (intval($quote) != 0) {
			$q = $db->selectUnique('quotes.db',1,$quote);
				if ($q) {
					$this->privmsg($channel, $this->b."Quote #".$quote."/".$totalquotes.": ".$this->o.$q[2]);
				}
				else { $this->privmsg($channel, "Quote ".$this->b."#".$quote.$this->o." does not exist."); }
			 } else { $this->privmsg($channel, "The quote ID must be an integer."); }
		}
		function quote_rand ($channel) {
			global $db;
			$totalquotes = 0;
			$quotes = $db->selectAll('quotes.db');
			foreach ($quotes as $quotes) {
				$totalquotes++;
			}
			$quoteid = rand(1,$totalquotes);
			$this->quote_view($quoteid,$channel);
		}
		function quote_amnt ($channel) {
			global $db;
			$totalquotes = 0;
			$quotes = $db->selectAll('quotes.db');
			foreach ($quotes as $quotes) {
				$totalquotes++;
			}
			$this->privmsg($channel, "There are ".$this->b.$totalquotes.$this->o." quotes in my database.");
		}
		function pic_view ($quote, $channel) {
			global $db;
			$totalquotes = 0;
			$quotes = $db->selectAll('pics.db');
			foreach ($quotes as $quotes) {
				$totalquotes++;
			}
			if (intval($quote) != 0) {
			$q = $db->selectUnique('pics.db',1,$quote);
				if ($q) {
					$this->privmsg($channel, $this->b."Picture #".$quote."/".$totalquotes.": ".$this->o.$q[2]);
				}
				else { $this->privmsg($channel, "Picture ".$this->b."#".$quote.$this->o." does not exist."); }
			 } else { $this->privmsg($channel, "The picture ID must be an integer."); }
		}
		function pic_rand ($channel) {
			global $db;
			$totalquotes = 0;
			$quotes = $db->selectAll('pics.db');
			foreach ($quotes as $quotes) {
				$totalquotes++;
			}
			$quoteid = rand(1,$totalquotes);
			$this->pic_view($quoteid,$channel);
		}
		function pic_amnt ($channel) {
			global $db;
			$totalquotes = 0;
			$quotes = $db->selectAll('pics.db');
			foreach ($quotes as $quotes) {
				$totalquotes++;
			}
			$this->privmsg($channel, "There are ".$this->b.$totalquotes.$this->o." pictures in my database.");
		}

		function mode ($a, $b) {
			$this->send_data("MODE ".$a." ".$b);
		}

		function join ($channel, $password) {
			$this->send_data("JOIN :".$channel." ".$password);
		}

		function quit ($msg) {
			$this->send_data("QUIT :".$msg);
		}

		function part ($channel, $msg) {
			if ($msg) {
				$this->send_data("PART ".$channel." :".$msg);
			}
			else {
				$this->send_data("PART ".$channel);
			}
		}

		function privmsg($a, $b) {
			$this->send_data("PRIVMSG ".$a." :".$b);
		}

		function notice($a, $b) {
			$this->send_data("NOTICE ".$a." :".$b);
		}

		function convert_date ($date) {
			// month, day, year, hour, minute, second, am/pm
			$d = explode(" ", $date);
			$month = $d[0];
				if (intval($month) == 1) { $month = "January"; }
				elseif (intval($month) == 2) { $month = "February"; }
				elseif (intval($month) == 3) { $month = "March"; }
				elseif (intval($month) == 4) { $month = "April"; }
				elseif (intval($month) == 5) { $month = "May"; }
				elseif (intval($month) == 6) { $month = "June"; }
				elseif (intval($month) == 7) { $month = "July"; }
				elseif (intval($month) == 8) { $month = "August"; }
				elseif (intval($month) == 9) { $month = "September"; }
				elseif (intval($month) == 10) { $month = "October"; }
				elseif (intval($month) == 11) { $month = "November"; }
				elseif (intval($month) == 12) { $month = "December"; }
				else { $month = "N/A"; }
			$date = $month." ".$d[1].", ".$d[2]." at ".$d[3].":".$d[4].":".$d[5]." ".strtoupper($d[6]).$this->o.".";
			return $date;
		}

		function idandjoin () {
			global $config;
			global $db;
				$this->send_data("PRIVMSG NickServ :IDENTIFY ".$config->serv_nickpass);
				sleep(1);
					$s = new AndWhereClause();
					$s->add(new SimpleWhereClause(4, '=', $config->network, 'strcasecmp'));
					$channels = $db->selectWhere('channels.db',$s);
					foreach ($channels as $channel) {
						$this->join($channel[1]);
					}
		}
		function clear () {
			unset($this->command);
			unset($this->quote);
			unset($this->response);
			unset($this->channel);
			unset($this->nick);
			unset($this->ident);
			unset($this->host);
			unset($this->args);
		}
		function connect () {
			if (exec("whoami") == "root") { die("\r\n DO NOT RUN THIS PROGRAM AS ROOT!\r\n"); }
			global $config;
			$bindTo = $config->IPBind;
			$server = $config->server;
			$port = $config->serv_port;
			$this->socket = fsockopen($config->server,$config->serv_port);
			$this->send_data("USER", $config->serv_ident." * * :".$config->serv_realname);
			$this->send_data("NICK", $config->serv_nick);
		}

		function send_data($cmd, $msg = null) 
		{
			global $config;
			fputs($this->socket, trim($cmd.' '.$msg)."\r\n");
			if ($config->debug) { echo("[~S] ".trim($cmd.' '.$msg)."\r\n"); }
		}
	// other functions

		function b_convert($bytes)
		{
		    $ext = array('bytes', 'kb', 'mb', 'gb', 'tb', 'pb', 'eb', 'zb', 'yb');
		    $unitCount = 0;
		    for(; $bytes >= 1024; $unitCount++) $bytes /= 1024;
		    return $bytes ." ". $ext[$unitCount];
		}

		function sexuality ($nick) {
			$s = rand(1,5);
				if (strpos($nick,"starcoder") !== false) { return "asexual"; }
				if ($s == 1) {
					return "straight";
				}
				elseif ($s == 2) {
					return "gay";
				}
				elseif ($s == 3) {
					return "lesbian";
				}
				elseif ($s == 4) {
					return "bi";
				}
				elseif ($s == 5) {
					return "asexual";
				}
		}

		function lol () {
			$s = rand(1,6);
				if ($s == 1) {
					return "hoe";
				}
				elseif ($s == 2) {
					return "hooker";
				}
				elseif ($s == 3) {
					return "whore";
				}
				elseif ($s == 4) {
					return "bitch";
				}
				elseif ($s == 5) {
					return "slut";
				}
				elseif ($s == 6) {
					return "pimp";
				}
		}

	}
	$fox = new fox();
?>
