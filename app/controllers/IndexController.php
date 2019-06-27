<?php

namespace App\Controllers;

/**
 * Class IndexController
 * @Anonymous
 * @package App\Controllers
 */
class IndexController extends Controller
{
    public function indexAction()
    {
        $meminfo = file_get_contents('/proc/meminfo');
        preg_match('/MemTotal:\s*(\d+)/', $meminfo, $totalmatch);
        preg_match('/MemAvailable:\s*(\d+)/', $meminfo, $usedmatch);
        $this->response->setJsonContent([
            'OS'      => trim(file_get_contents('/etc/issue')),
            'Server'  => $_SERVER ['SERVER_SOFTWARE'],
            'PHP'     => PHP_VERSION,
            'CPU'     => trim(exec('cat /proc/cpuinfo | grep name | cut -f2 -d: | uniq -c')),
            'Disk'    => [
                'Total' => disk_total_space('/'),
                'Free'  => disk_free_space('/'),
            ],
            'Memory'  => [
                'Total'     => $totalmatch[1],
                'Available' => $usedmatch[1],
            ],
            'LoadAvg' => trim(file_get_contents('/proc/loadavg')),
        ]);
    }
}