<?php


class Process
{
    private $pid;
    private $command;

    public function __construct($cl=false)
    {
        if ($cl != false) {
            $this->command = $cl;
            $this->runCom();
        }
    }
    private function runCom()
    {
        $command = 'nohup '.$this->command.' > /dev/null 2>&1 & echo $!';
        exec($command, $op);
        $this->pid = (int)$op[0];
    }

    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function status()
    {
        $command = 'ps -p '.$this->pid;
        exec($command, $op);
        if (!isset($op[1])) {
            return false;
        } else {
            return true;
        }
    }

    public function start()
    {
        if ($this->command != '') {
            $this->runCom();
        } else {
            return true;
        }
    }

    public function stop()
    {
        $command = 'kill '.$this->pid;
        exec($command);
        if ($this->status() == false) {
            return true;
        } else {
            return false;
        }
    }
}



$branch_name = exec("git branch --show current");



$push = isset($argv[4]) ? $argv[4] : null;

$PPID = isset($argv[5]) ? $argv[5] : null;

$process = new Process();

$process.setPid($PPID);

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


    $process->runCom("git add composer.json && git commit -m  '{$commit}' && git push origin master ");

    sleep(2);
}
