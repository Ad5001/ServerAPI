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
        echo json_encode([""]);
        break;
        case "getpmversion":
        case "getimversion":
        case "getpocketmineversion":
        case "getimagicalmineversion":
        echo json_encode(["1.5"]);
        break;
        case "getserverversion":
        case "getconnectversion":
        echo json_encode(["v0.15.0.x alpha"]);
        break;
        case "getserversoftwarename":
        case "getsoftwarename":
        echo json_encode(["ImagicalMine"]);
        break;
        case "getsoftwarecodename":
        echo json_encode(["19132"]);
        break;
        case "getmotd":
        echo json_encode(["Minecraft PE Server"]);
        break;
        case "getapiversion":
        echo json_encode(["2.0.0"]);
        break;
        case "getmaxplayers":
        echo json_encode(["20"]);
        break;
    }
}