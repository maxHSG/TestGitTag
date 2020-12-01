<?php

$branch_name = exec("git branch --show current");

$last_commit = trim(exec("git reflog -1 | sed 's/^.*: //'"));

if ($branch_name === "master") {
    try {
        $tag =  exec("git describe --tags");

        $matches = [];

        preg_match("/^v?\d+(((\.\d+)?\.\d+)?\.\d+)/", $tag, $matches);

        if (!count($matches)) {
            throw new Exception("Tag não encontrada");
        }

        $tag = explode("-", $tag)[0];

        $commit = "Update to version {$tag}";

        if ($commit === $last_commit) {
            return;
        }

        $composer_path = "./composer.json";
        
        $composer_json = json_decode(file_get_contents($composer_path));
        
        $composer_json->version = $tag;
        
        file_put_contents($composer_path, json_encode($composer_json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));



        exec("git add composer.json");
        exec("git commit -m  '{$commit}' ");
        exec("git push origin master");
    } catch (\Throwable $th) {
        echo "\n\n\n";
        
        exec("git reset --soft HEAD~1");

        echo "Crie uma tag antes de enviar para a master";

        echo "\n\n\n";
    }
}
