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
	$storage_pool = $this->_CONFIG['primary_server']['poolName'];
	$cmd = "zfs list | grep '/var/lib/lxd/storage-pools/".$storage_pool."/containers/' | awk '{print $5}'";
	$output = shell_exec($cmd);
	$output = explode("\n", $output);
	return $output;
}

public function generateSyncFromPath($containerName) {
	if ($containerName == null) die("Expected Container Name in generateSyncFromPath()");
	$lxdPoolPath = $this->_CONFIG['primary_server']['lxdPoolPath'];
	$storagePool = $this->_CONFIG['primary_server']['poolName'];
	$path = $lxdPoolPath.$storagePool."/containers/";
	#$runningContainers = $this->lxd_info->listRunningContainers();
	return $path.$containerName."/rootfs/";
}

public function generateSyncToPath($containerName) {
	if ($containerName == null) die("Expected Container Name in generateSyncToPath()");
	$lxdPoolPath = $this->_CONFIG['backup_server']['lxdPoolPath'];
	$storagePool = $this->_CONFIG['backup_server']['poolName'];
	$path = $lxdPoolPath.$storagePool."/containers/";
	#$runningContainers = $this->lxd_info->listRunningContainers();
	return $path.$containerName."/rootfs/";
}



public function startSync() {
	#Get Running Containers
	$runningContainers = $this->lxd_info->listRunningContainers();
	$result = array();
	foreach($runningContainers as $containerName) {
		#Generate ZFS Datata Set Path
		$dataSet = $this->lxd_info->generateRemoteContainetZFSDataset($containerName);

		# mount remote zfs dataset
		if(!$this->transferd->mountRemoteZFS($dataSet)) break;

		#generate paths
		$syncFromPath = $this->generateSyncFromPath($containerName);
		$syncToPath = $this->generateSyncToPath($containerName);

		#sync
		$this->transferd->startSync($syncFromPath, $syncToPath);

	}

	

}

public function printConfig() {
var_dump($this->_CONFIG);

}
}

?>