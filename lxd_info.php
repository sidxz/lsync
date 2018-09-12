<?php
/**
*   HEADERS. This module is responsible for contacting the ldap server
*
* PHP Version 5
*
* @file     lxd-sync/lxd_info.php
* @category Extract information from lxd
* @package  lxd-sync
* @author   Siddhant Rath <sid@tamu.edu>
* @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
*/


class LXD_INFO {

public function listRunningContainers() {

        $cmd = "lxc list --format json";
        $output = shell_exec($cmd);
	$array_output = json_decode($output, true);
	$runningContainers = array();	
	#var_dump($array_output); 
	foreach ( $array_output as $container ) {
		if ($container['status'] == "Running") {
			array_push($runningContainers, $container['name']);
		}
	}
	#var_dump($runningContainers);
	return $runningContainers;
}
}

#$lxd_info = new LXD_INFO();
#$lxd_info-> listRunningContainers();
