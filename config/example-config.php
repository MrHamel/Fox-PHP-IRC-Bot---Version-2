// When you save this file, please save it as the network name you put below. 
// So you won't loose track of things you did per server. 
// Because this code can be executed many times and use different network configuration files.

<?php
        $config->network = ""; // IRC Network Name
        $config->server = ""; // IRC Server Address
        $config->serv_port = "6667"; // IRC Server Port (SSL Not Supported)
        $config->serv_nick = ""; // Bot Nickname
        $config->serv_ident = ""; // Bot Ident
        $config->serv_realname = ""; // IRC Real Name
        $config->serv_nickpass = ""; // NickServ/UserServ Password
        $config->cp            = "."; // Command Prefix
        $config->quit          = "BRB. Restart."; // Restart Command Quit Message
        $config->ctcp_version  = "Function Dev"; // CTCP Version Response
        $config->ownerhost = array(
                strtolower('RKHTECH.org') => true   // You must use your encrypted hostmask from that network itself. 
						    // You must "/whois yournick" and use the encrypted hostmask not the raw IP.
        );
        $config->storage = "db/";
        $config->debug = true;
                include("../bin/core.php");
?>
