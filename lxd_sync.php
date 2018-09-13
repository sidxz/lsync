<?php
/**
*   HEADERS. This module is responsible for contacting the ldap server
*
* PHP Version 5
*
* @file     lxd-sync/main.php
* @category Sync two lxd servers
* @package  lxd-sync
* @author   Siddhant Rath <sid@tamu.edu>
* @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
*/

include_once('lxd_info.php');
include_once('transferd.php');
class LXD_SYNC {

/**
 * The Configuration Array
 */
	private $_CONFIG;
	private $lxd_info;
	private $transferd;

/**
** Read the configuration file **
*/

public function __construct() {
	$iniPath="/etc/lxd_sync.conf";
	if(!file_exists($iniPath)) die("LXD-SYNC:[FATAL] Configuration File not Found : {$iniPath}");
	$this->_CONFIG = parse_ini_file($iniPath, true);
	$this->lxd_info = new LXD_INFO();
	$this->transferd = new TRANSFERD();
}

public function getMountedPools() {
	$storage_pool = $this->_CONFIG['lxd']['poolname'];
	$cmd = "zfs list | grep '/var/lib/lxd/storage-pools/".$storage_pool."/containers/' | awk '{print $5}'";
	$output = shell_exec($cmd);
	$output = explode("\n", $output);
	var_dump($output);
}

public function generateSyncPaths() {
	$lxdPoolPath = $this->_CONFIG['lxd']['lxdPoolPath'];
	$storagePool = $this->_CONFIG['lxd']['poolname'];
	$path = $lxdPoolPath.$storagePool."/containers/";
	$runningContainers = $this->lxd_info->listRunningContainers();
	$fqPaths = array();
	foreach ($runningContainers as $container) {
		array_push($fqPaths, $path.$container."/rootfs/");
	}	
	return $fqPaths;
}

public function mountRemoteFS($container) {


}


public function startSync($fqPaths) {
	if ($fqPaths == null) return false;
	$this->transferd->startSync($fqPaths);

}

public function printConfig() {
var_dump($this->_CONFIG);

}
}


#$lsyn = new LXD_SYNC();
#$lsyn->generateSyncPaths();
#$lsyn->getMountedPools();


