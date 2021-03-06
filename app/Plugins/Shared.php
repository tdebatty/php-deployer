<?php

namespace App\Plugins;

use App\PluginInterface;
use App\Deployment;
use Symfony\Component\Process\Process;

/**
 * Manage shared folders between deployments.
 * Example deploy.yml
 * - plugin: App\Plugins\Shared
 *   params:
 *   - data
 */
class Shared implements PluginInterface {

    public function run(Deployment $deploy, $params) {

        foreach ($params as $folder) {
            $folder = trim($folder);
            $folder = trim($folder, "/");

            // Delete real folder
            $process = new Process("rm -Rf " . $folder);
            $process->setWorkingDirectory($deploy->getDeploymentRoot());
            $process->run();
            $deploy->addLog($process->getOutput());
            $deploy->addLog($process->getErrorOutput());

            // Create the shared folder if needed
            $shared_folder = $deploy->getProjectRoot()
                    . DIRECTORY_SEPARATOR . "shared"
                    . DIRECTORY_SEPARATOR . $folder;

            if (!file_exists($shared_folder)) {
                mkdir($shared_folder, 0775, true);
                chgrp($shared_folder, "www-data");
            }

            // Create symlink
            $link = $deploy->getDeploymentRoot()
                    . DIRECTORY_SEPARATOR . $folder;
            symlink($shared_folder, $link);
        }
    }

}
