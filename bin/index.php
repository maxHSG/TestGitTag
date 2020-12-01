<?php

$branch_name = exec("git branch --show current");

if ($branch_name === "master") {
    $tag =  exec("git describe --tags");

    $tag = explode("-", $tag)[0];

    $commit = "Update to version {$tag}";

    $composer_path = "./composer.json";
        
    $composer_json = json_decode(file_get_contents($composer_path));
        
    $composer_json->version = $tag;
        
    file_put_contents($composer_path, json_encode($composer_json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    exec("git add composer.json");
    exec("git commit -m  '{$commit}' ");
}
