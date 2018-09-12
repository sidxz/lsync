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
	$this->RSYNC = exec('which rsync');
}

public function startSync($syncPaths) {
	$backupServer = $this->_CONFIG['backup_server']['server_fqdn'];
	$sshUser = $this->_CONFIG['ssh']['username'];
	$sshKey = $this->_CONFIG['ssh']['key'];

	#create rsync commands
	foreach($syncPaths as $syncPath) {
	$cmd = $this->RSYNC.' -avh --progress -e "ssh -i '.$sshKey.'" '.$syncPath.' '.$sshUser.'@'.$backupServer.':'.$syncPath;
	echo $cmd;
	}
}

public function stratAsync($syncPaths) {
# TODO
}


} #END OF CLASS TRANSFERD

$transferD = new TRANSFERD();
$transferD->startSync(['/test/path/to/pool/cont1','/test/path/to/pool/cont1/test/path/to/pool/cont2']);
?>
