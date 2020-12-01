<?php

$branch_name = exec("git branch --show current");

if ($branch_name === "master") {
    try {
        $tag =  exec("git describe --tags --abbrev=0 --exact-match");
        
        $composer_path = "./composer.json";
        
        $composer_json = json_decode(file_get_contents($composer_path));
        
        $composer_json->version = $tag;
        
        file_put_contents($composer_path, json_encode($composer_json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        exec("git add composer.json");
    } catch (\Throwable $th) {
        echo "Crie uma tag antes de enviar para a master";
    }
}
