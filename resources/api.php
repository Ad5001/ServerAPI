<?php

if(isset($_GET["command"])) {
    if(strpos($_GET["command"], "get") !== false) {
        exe($_GET["command"]);
    } elseif(isset($_GET["username"]) and isset($_GET["password"])) {
        $logins = explode("///", file_get_contents("pass"));
        if(sha1($_GET["username"]) === $logins[0] and sha1($_GET["password"] === $logins[1])) {
            file_put_contents("command", $_GET["command"]);
            sleep(1.5);
            echo json_encode(unserialize(file_get_contents('result')));
        }
    } else {
        echo json_encode(["Please enter a valid command or an username and a password"]);
    }
}

function exe($command) {
    switch(strtolower($command)) {
        case "getonlineplayers":
        echo json_encode(["api_players"]);
        break;
        case "getpmversion":
        case "getimversion":
        case "getpocketmineversion":
        case "getimagicalmineversion":
        echo json_encode(["api_pmversion"]);
        break;
        case "getserverversion":
        case "getconnectversion":
        echo json_encode(["api_version"]);
        break;
        case "getserversoftwarename":
        case "getsoftwarename":
        echo json_encode(["api_name"]);
        break;
        case "getport":
        case "getconnectport":
        case "getserverport":
        echo json_encode(["api_port"]);
        break;
        case "getmotd":
        echo json_encode(["api_motd"]);
        break;
        case "getapiversion":
        echo json_encode(["api_plugin_api_code"]);
        break;
        case "getmaxplayers":
        echo json_encode(["api_maxplayers"]);
        break;
    }
}