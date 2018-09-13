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

private $_CONFIG;

public function __construct() {
	$iniPath="/etc/lxd_sync.conf";
	if(!file_exists($iniPath)) die("LXD-SYNC:[FATAL] Configuration File not Found : {$iniPath}");
	$this->_CONFIG = parse_ini_file($iniPath, true);
}

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

public function isContainerRunning($containerName) {
	# Basic Checks
	if ($containerName == null) die("Expected Container Name in isContainerRunning()");

	$cmd = "lxc list {$containerName} --format json";
  	$output = shell_exec($cmd);
	$array_output = json_decode($output, true);
	if (empty($array_output)) return false;
	return strcmp($array_output[0]['status'], "Running")==0?true:false;
}

public function generateRemoteContainetZFSDataset($containerName) {
	if ($containerName == null) die("Expected Container Name in generateRemoteContainetZFSDataset");
	return $this->_CONFIG['backup_server']['zfs_dataset'].'/containers/'.$containerName;
}


}

?>