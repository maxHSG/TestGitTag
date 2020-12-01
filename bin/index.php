<?php

require("./bin/Process.php");

$branch_name = exec("git branch --show current");

$push = isset($argv[4]) ? $argv[4] : null;

$PPID = isset($argv[5]) ? $argv[5] : null;

unset($argv[0],$argv[5]);

$command = join(" ", $argv);


if ($branch_name === "master" && $push) {
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

    $process = new Process("git add composer.json && git commit -m  '{$commit}' &&  git push origin master && git tag -f {$tag}");

    $process->setPid($PPID);

    $process->start();
}
