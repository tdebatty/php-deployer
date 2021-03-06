<?php

namespace App\Plugins;

use App\PluginInterface;
use App\Deployment;
use Symfony\Component\Process\Process;

class Composer implements PluginInterface {

    public function run(Deployment $deploy, $params) {
        $process = new Process("composer install");
        $process->setTimeout(300);
        $process->setWorkingDirectory($deploy->getDeploymentRoot());
        $process->run();
        $deploy->addLog($process->getOutput());
        $deploy->addLog($process->getErrorOutput());
    }

}
