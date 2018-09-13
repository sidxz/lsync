<?php
/**
*   HEADERS. This module is responsible for contacting the ldap server
*
* PHP Version 5
*
* @file     lxd-sync/transferd.php
* @category Transfer Services
* @package  lxd-sync
* @author   Siddhant Rath <sid@tamu.edu>
* @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
*/

include_once('lxd_info.php');
class TRANSFERD {

/**
 * The Configuration Array
 */
        private $_CONFIG;
	private $RSYNC;
/**
** Read the configuration file **
*/

public function __construct() {
        $iniPath="/etc/lxd_sync.conf";
        if(!file_exists($iniPath)) die("LXD-SYNC:[FATAL] Configuration File not Found : {$iniPath}");
        $this->_CONFIG = parse_ini_file($iniPath, true);
}

public function getRemoteRsyncPath() {
	$backupServer = $this->_CONFIG['backup_server']['server_fqdn'];
        $sshUser = $this->_CONFIG['ssh']['username'];
        $sshKey = $this->_CONFIG['ssh']['key'];
	$cmd = 'ssh -i '.$sshKey.' '.$sshUser.'@'.$backupServer.' which rsync';
	$remoteRsyncPath= exec($cmd);
	printf("[INFO] Remote rsync is at ".$remoteRsyncPath.PHP_EOL);
	return $remoteRsyncPath; 	
}

public function getServerRsyncPath() {
	$cmd = "which rsync";
	$serverRsyncPath = exec($cmd);
	printf("[INFO] Server rsync is at ".$serverRsyncPath.PHP_EOL);
	return $serverRsyncPath;
	
}
public function startSync($syncPaths) {
	$backupServer = $this->_CONFIG['backup_server']['server_fqdn'];
	$sshUser = $this->_CONFIG['ssh']['username'];
	$sshKey = $this->_CONFIG['ssh']['key'];
	$serverRsync = $this->getServerRsyncPath();
	$remoteRsync = $this->getRemoteRsyncPath();

	#create rsync commands
	foreach($syncPaths as $syncPath) {
	printf("[SYNCING] ".$syncPath.PHP_EOL);
	$cmd = $serverRsync.' -ah -e "ssh -i '.$sshKey.'" --rsync-path="sudo '.$remoteRsync.'" '.$syncPath.' '.$sshUser.'@'.$backupServer.':'.$syncPath;
	
	exec($cmd);
	printf("[DONE]".PHP_EOL);
	}
}

public function stratAsync($syncPaths) {
# TODO
}


} #END OF CLASS TRANSFERD

#$transferD = new TRANSFERD();
#$transferD->getRemoteRsyncPath();
#$transferD->startSync(['/test/cont1/','/test/cont2/']);
?>
