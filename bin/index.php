<?php

$branch_name = exec("git branch --show current");

$push = isset($argv[4]) ? $argv[4] : null;


if ($branch_name === "master" && !$push) {
    $tag =  exec("git describe --tags");

    $tag = explode("-", $tag)[0];

    $commit = "Update version to {$tag}";

    $composer_path = "./composer.json";
        
    $composer_json = json_decode(file_get_contents($composer_path));

    if ($composer_json->version === $tag) {
        return;
    }
        
    $composer_json->version = $tag;
        
    file_put_contents($composer_path, json_encode($composer_json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    exec("git add composer.json");
    exec("git commit -m  '{$commit}' ");
    //exec("git push origin master");
}
