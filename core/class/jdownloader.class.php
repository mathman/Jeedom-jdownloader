<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';
require_once __DIR__  . '/../../core/php/jdownloader.inc.php';

class jdownloader extends eqLogic {
    /*     * *************************Attributs****************************** */
    
  /*
   * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
   * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
	public static $_widgetPossibility = array();
   */
    
    /*     * ***********************Methode static*************************** */
    
    public static function pull() {
		foreach (self::byType('jdownloader') as $eqLogic) {
			$eqLogic->updateDeviceInfos();
		}
	}

    public static function syncEqLogicWithJdownloader() {
        
		$j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
        $devices = $j->enumerateDevices();
        $devices = json_decode($devices, true);
        $j->disconnect();
        
        foreach ($devices['list'] as $device) {
            log::add('jdownloader', 'debug', "New Device id : " . $device['id']);
            log::add('jdownloader', 'debug', "New Device name : " . $device['name']);

            $newEqLogic = eqLogic::byLogicalId($device['id'], 'jdownloader');
            if (!is_object($newEqLogic)) {
                $newEqLogic = new jdownloader();
                $newEqLogic->setEqType_name('jdownloader');
                $newEqLogic->setIsEnable(0);
                $newEqLogic->setIsVisible(0);
                $newEqLogic->setName($device['name']);
                $newEqLogic->setLogicalId($device['id']);
                $newEqLogic->save();
            }
            $refresh = $newEqLogic->getCmd(null, 'refresh');
            if (!is_object($refresh)) {
                $refresh = new jdownloaderCmd();
            }
            $refresh->setName('Rafraichir');
            $refresh->setEqLogic_id($newEqLogic->getId());
            $refresh->setLogicalId('refresh');
            $refresh->setType('action');
            $refresh->setSubType('other');
            $refresh->setOrder(0);
            $refresh->save();
            
            $version = $newEqLogic->getCmd(null, "version");
            if (!is_object($version)) {
                $version = new jdownloaderCmd();
            }
            $version->setName("Version");
            $version->setEqLogic_id($newEqLogic->getId());
            $version->setLogicalId("version");
            $version->setType('info');
            $version->setSubType('string');
            $version->setOrder(1);
            $version->save();
            
            $javaVersion = $newEqLogic->getCmd(null, "javaVersion");
            if (!is_object($javaVersion)) {
                $javaVersion = new jdownloaderCmd();
            }
            $javaVersion->setName("Version Java");
            $javaVersion->setEqLogic_id($newEqLogic->getId());
            $javaVersion->setLogicalId("javaVersion");
            $javaVersion->setType('info');
            $javaVersion->setSubType('string');
            $javaVersion->setOrder(2);
            $javaVersion->save();
            
            $startupTime = $newEqLogic->getCmd(null, "startupTime");
            if (!is_object($startupTime)) {
                $startupTime = new jdownloaderCmd();
            }
            $startupTime->setName("Dernier redémarrage");
            $startupTime->setEqLogic_id($newEqLogic->getId());
            $startupTime->setLogicalId("startupTime");
            $startupTime->setType('info');
            $startupTime->setSubType('string');
            $startupTime->setOrder(3);
            $startupTime->save();
            
            $packageCollectorNb = $newEqLogic->getCmd(null, "packageCollectorNb");
            if (!is_object($packageCollectorNb)) {
                $packageCollectorNb = new jdownloaderCmd();
            }
            $packageCollectorNb->setName("Nombre de paquets en attente");
			$packageCollectorNb->setEqLogic_id($newEqLogic->getId());
			$packageCollectorNb->setLogicalId("packageCollectorNb");
			$packageCollectorNb->setType('info');
            $packageCollectorNb->setSubType('numeric');
            $packageCollectorNb->setTemplate('dashboard', 'line');
            $packageCollectorNb->setTemplate('mobile', 'line');
            $packageCollectorNb->setOrder(4);
            $packageCollectorNb->save();
            
            $linkCollectorNb = $newEqLogic->getCmd(null, "linkCollectorNb");
            if (!is_object($linkCollectorNb)) {
                $linkCollectorNb = new jdownloaderCmd();
            }
            $linkCollectorNb->setName("Nombre de liens en attente");
			$linkCollectorNb->setEqLogic_id($newEqLogic->getId());
			$linkCollectorNb->setLogicalId("linkCollectorNb");
			$linkCollectorNb->setType('info');
            $linkCollectorNb->setSubType('numeric');
            $linkCollectorNb->setTemplate('dashboard', 'line');
            $linkCollectorNb->setTemplate('mobile', 'line');
            $linkCollectorNb->setOrder(5);
            $linkCollectorNb->save();
            
            $packageDownloadNb = $newEqLogic->getCmd(null, "packageDownloadNb");
            if (!is_object($packageDownloadNb)) {
                $packageDownloadNb = new jdownloaderCmd();
            }
            $packageDownloadNb->setName("Nombre de paquets en téléchargement");
			$packageDownloadNb->setEqLogic_id($newEqLogic->getId());
			$packageDownloadNb->setLogicalId("packageDownloadNb");
			$packageDownloadNb->setType('info');
            $packageDownloadNb->setSubType('numeric');
            $packageDownloadNb->setTemplate('dashboard', 'line');
            $packageDownloadNb->setTemplate('mobile', 'line');
            $packageDownloadNb->setOrder(6);
            $packageDownloadNb->save();
            
            $linkDownloadNb = $newEqLogic->getCmd(null, "linkDownloadNb");
            if (!is_object($linkDownloadNb)) {
                $linkDownloadNb = new jdownloaderCmd();
            }
            $linkDownloadNb->setName("Nombre de liens en téléchargement");
			$linkDownloadNb->setEqLogic_id($newEqLogic->getId());
			$linkDownloadNb->setLogicalId("linkDownloadNb");
			$linkDownloadNb->setType('info');
            $linkDownloadNb->setSubType('numeric');
            $linkDownloadNb->setTemplate('dashboard', 'line');
            $linkDownloadNb->setTemplate('mobile', 'line');
            $linkDownloadNb->setOrder(7);
            $linkDownloadNb->save();
            
            $totalSpeed = $newEqLogic->getCmd(null, "totalSpeed");
            if (!is_object($totalSpeed)) {
                $totalSpeed = new jdownloaderCmd();
            }
            $totalSpeed->setName("Vitesse totale");
			$totalSpeed->setEqLogic_id($newEqLogic->getId());
			$totalSpeed->setLogicalId("totalSpeed");
			$totalSpeed->setType('info');
            $totalSpeed->setSubType('numeric');
            $totalSpeed->setUnite("ko/s");
            $totalSpeed->setTemplate('dashboard', 'line');
            $totalSpeed->setTemplate('mobile', 'line');
            $totalSpeed->setOrder(8);
            $totalSpeed->save();
            
            $package = $newEqLogic->getCmd(null,'package');
            if (!is_object($package)) {
                $package = new jdownloaderCmd();
            }
            $package->setName("Paquet");
            $package->setEqLogic_id($newEqLogic->getId());
            $package->setLogicalId("package");
            $package->setType('info');
            $package->setSubType('string');
            $package->setIsVisible(0);
            $package->setOrder(9);
            $package->save();
            
            $packageList = $newEqLogic->getCmd(null,'packageList');
            if (!is_object($packageList)) {
                $packageList = new jdownloaderCmd();
            }
            $packageList->setName("Liste paquets");
            $packageList->setEqLogic_id($newEqLogic->getId());
            $packageList->setLogicalId("packageList");
            $packageList->setType('action');
            $packageList->setSubType('select');
            $packageList->setValue($newEqLogic->getCmd(null,'package')->getId());
            $packageList->setOrder(10);
            $packageList->save();
            
            /* *****Commandes package***** */
            $enabledPackage = $newEqLogic->getCmd(null,'enabledPackage');
            if (!is_object($enabledPackage)) {
                $enabledPackage = new jdownloaderCmd();
            }
            $enabledPackage->setName("Paquet activé");
            $enabledPackage->setEqLogic_id($newEqLogic->getId());
            $enabledPackage->setLogicalId("enabledPackage");
            $enabledPackage->setType('info');
            $enabledPackage->setSubType('binary');
            $enabledPackage->setTemplate('dashboard', 'line');
            $enabledPackage->setTemplate('mobile', 'line');
            $enabledPackage->setOrder(11);
            $enabledPackage->save();

            $bytesTotalPackage = $newEqLogic->getCmd(null,'bytesTotalPackage');
            if (!is_object($bytesTotalPackage)) {
                $bytesTotalPackage = new jdownloaderCmd();
            }
            $bytesTotalPackage->setName("Taille totale du paquet");
            $bytesTotalPackage->setEqLogic_id($newEqLogic->getId());
            $bytesTotalPackage->setLogicalId("bytesTotalPackage");
            $bytesTotalPackage->setType('info');
            $bytesTotalPackage->setSubType('numeric');
            $bytesTotalPackage->setUnite("MB");
            $bytesTotalPackage->setTemplate('dashboard', 'line');
            $bytesTotalPackage->setTemplate('mobile', 'line');
            $bytesTotalPackage->setOrder(12);
            $bytesTotalPackage->save();
            
            $bytesLoadedPackage = $newEqLogic->getCmd(null,'bytesLoadedPackage');
            if (!is_object($bytesLoadedPackage)) {
                $bytesLoadedPackage = new jdownloaderCmd();
            }
            $bytesLoadedPackage->setName("Données téléchargé du paquet");
            $bytesLoadedPackage->setEqLogic_id($newEqLogic->getId());
            $bytesLoadedPackage->setLogicalId("bytesLoadedPackage");
            $bytesLoadedPackage->setType('info');
            $bytesLoadedPackage->setSubType('numeric');
            $bytesLoadedPackage->setUnite("MB");
            $bytesLoadedPackage->setTemplate('dashboard', 'line');
            $bytesLoadedPackage->setTemplate('mobile', 'line');
            $bytesLoadedPackage->setOrder(13);
            $bytesLoadedPackage->save();
            
            $progressPackage = $newEqLogic->getCmd(null,'progressPackage');
            if (!is_object($progressPackage)) {
                $progressPackage = new jdownloaderCmd();
            }
            $progressPackage->setName("Progression du paquet");
            $progressPackage->setEqLogic_id($newEqLogic->getId());
            $progressPackage->setLogicalId("progressPackage");
            $progressPackage->setType('info');
            $progressPackage->setSubType('numeric');
            $progressPackage->setUnite("%");
            $progressPackage->setTemplate('dashboard', 'line');
            $progressPackage->setTemplate('mobile', 'line');
            $progressPackage->setOrder(14);
            $progressPackage->save();
            
            $saveToPackage = $newEqLogic->getCmd(null,'saveToPackage');
            if (!is_object($saveToPackage)) {
                $saveToPackage = new jdownloaderCmd();
            }
            $saveToPackage->setName("Dossier de téléchargement");
            $saveToPackage->setEqLogic_id($newEqLogic->getId());
            $saveToPackage->setLogicalId("saveToPackage");
            $saveToPackage->setType('info');
            $saveToPackage->setSubType('string');
            $saveToPackage->setOrder(15);
            $saveToPackage->save();
            
            $hostsPackage = $newEqLogic->getCmd(null,'hostsPackage');
            if (!is_object($hostsPackage)) {
                $hostsPackage = new jdownloaderCmd();
            }
            $hostsPackage->setName("Hébergeurs du paquet");
            $hostsPackage->setEqLogic_id($newEqLogic->getId());
            $hostsPackage->setLogicalId("hostsPackage");
            $hostsPackage->setType('info');
            $hostsPackage->setSubType('string');
            $hostsPackage->setOrder(16);
            $hostsPackage->save();
            
            $childCountPackage = $newEqLogic->getCmd(null,'childCountPackage');
            if (!is_object($childCountPackage)) {
                $childCountPackage = new jdownloaderCmd();
            }
            $childCountPackage->setName("Nombre de liens");
            $childCountPackage->setEqLogic_id($newEqLogic->getId());
            $childCountPackage->setLogicalId("childCountPackage");
            $childCountPackage->setType('info');
            $childCountPackage->setSubType('numeric');
            $childCountPackage->setTemplate('dashboard', 'line');
            $childCountPackage->setTemplate('mobile', 'line');
            $childCountPackage->setOrder(17);
            $childCountPackage->save();
            
            $onlineCountPackage = $newEqLogic->getCmd(null,'onlineCountPackage');
            if (!is_object($onlineCountPackage)) {
                $onlineCountPackage = new jdownloaderCmd();
            }
            $onlineCountPackage->setName("Liens en ligne");
            $onlineCountPackage->setEqLogic_id($newEqLogic->getId());
            $onlineCountPackage->setLogicalId("onlineCountPackage");
            $onlineCountPackage->setType('info');
            $onlineCountPackage->setSubType('numeric');
            $onlineCountPackage->setTemplate('dashboard', 'line');
            $onlineCountPackage->setTemplate('mobile', 'line');
            $onlineCountPackage->setOrder(18);
            $onlineCountPackage->save();
            
            $offlineCountPackage = $newEqLogic->getCmd(null,'offlineCountPackage');
            if (!is_object($offlineCountPackage)) {
                $offlineCountPackage = new jdownloaderCmd();
            }
            $offlineCountPackage->setName("Liens hors ligne");
            $offlineCountPackage->setEqLogic_id($newEqLogic->getId());
            $offlineCountPackage->setLogicalId("offlineCountPackage");
            $offlineCountPackage->setType('info');
            $offlineCountPackage->setSubType('numeric');
            $offlineCountPackage->setTemplate('dashboard', 'line');
            $offlineCountPackage->setTemplate('mobile', 'line');
            $offlineCountPackage->setOrder(19);
            $offlineCountPackage->save();
            
            $unknownCountPackage = $newEqLogic->getCmd(null,'unknownCountPackage');
            if (!is_object($unknownCountPackage)) {
                $unknownCountPackage = new jdownloaderCmd();
            }
            $unknownCountPackage->setName("Liens inconnus");
            $unknownCountPackage->setEqLogic_id($newEqLogic->getId());
            $unknownCountPackage->setLogicalId("unknownCountPackage");
            $unknownCountPackage->setType('info');
            $unknownCountPackage->setSubType('numeric');
            $unknownCountPackage->setTemplate('dashboard', 'line');
            $unknownCountPackage->setTemplate('mobile', 'line');
            $unknownCountPackage->setOrder(20);
            $unknownCountPackage->save();
            
            $speedPackage = $newEqLogic->getCmd(null,'speedPackage');
            if (!is_object($speedPackage)) {
                $speedPackage = new jdownloaderCmd();
            }
            $speedPackage->setName("Vitesse paquet");
            $speedPackage->setEqLogic_id($newEqLogic->getId());
            $speedPackage->setLogicalId("speedPackage");
            $speedPackage->setType('info');
            $speedPackage->setSubType('numeric');
            $speedPackage->setUnite("ko/s");
            $speedPackage->setTemplate('dashboard', 'line');
            $speedPackage->setTemplate('mobile', 'line');
            $speedPackage->setOrder(21);
            $speedPackage->save();
            
            $statusPackage = $newEqLogic->getCmd(null,'statusPackage');
            if (!is_object($statusPackage)) {
                $statusPackage= new jdownloaderCmd();
            }
            $statusPackage->setName("Status paquet");
            $statusPackage->setEqLogic_id($newEqLogic->getId());
            $statusPackage->setLogicalId("statusPackage");
            $statusPackage->setType('info');
            $statusPackage->setSubType('string');
            $statusPackage->setOrder(22);
            $statusPackage->save();
            
            $runningPackage = $newEqLogic->getCmd(null,'runningPackage');
            if (!is_object($runningPackage)) {
                $runningPackage = new jdownloaderCmd();
            }
            $runningPackage->setName("Paquet en téléchargement");
            $runningPackage->setEqLogic_id($newEqLogic->getId());
            $runningPackage->setLogicalId("runningPackage");
            $runningPackage->setType('info');
            $runningPackage->setSubType('binary');
            $runningPackage->setTemplate('dashboard', 'line');
            $runningPackage->setTemplate('mobile', 'line');
            $runningPackage->setOrder(23);
            $runningPackage->save();
            
            $linkPackage = $newEqLogic->getCmd(null,'linkPackage');
            if (!is_object($linkPackage)) {
                $linkPackage = new jdownloaderCmd();
            }
            $linkPackage->setName("Lien");
            $linkPackage->setEqLogic_id($newEqLogic->getId());
            $linkPackage->setLogicalId("linkPackage");
            $linkPackage->setType('info');
            $linkPackage->setSubType('string');
            $linkPackage->setIsVisible(0);
            $linkPackage->setOrder(24);
            $linkPackage->save();
            
            $linkListPackage = $newEqLogic->getCmd(null,'linkListPackage');
            if (!is_object($linkListPackage)) {
                $linkListPackage = new jdownloaderCmd();
            }
            $linkListPackage->setName("Liste liens");
            $linkListPackage->setEqLogic_id($newEqLogic->getId());
            $linkListPackage->setLogicalId("linkListPackage");
            $linkListPackage->setType('action');
            $linkListPackage->setSubType('select');
            $linkListPackage->setValue($newEqLogic->getCmd(null,'linkPackage')->getId());
            $linkListPackage->setOrder(25);
            $linkListPackage->save();

            /* *****Commandes links***** */
            $enabledLink = $newEqLogic->getCmd(null,'enabledLink');
            if (!is_object($enabledLink)) {
                $enabledLink = new jdownloaderCmd();
            }
            $enabledLink->setName("Lien activé");
            $enabledLink->setEqLogic_id($newEqLogic->getId());
            $enabledLink->setLogicalId("enabledLink");
            $enabledLink->setType('info');
            $enabledLink->setSubType('binary');
            $enabledLink->setTemplate('dashboard', 'line');
            $enabledLink->setTemplate('mobile', 'line');
            $enabledLink->setOrder(26);
            $enabledLink->save();
			
			$addedDateLink = $newEqLogic->getCmd(null,'addedDateLink');
            if (!is_object($addedDateLink)) {
                $addedDateLink = new jdownloaderCmd();
            }
            $addedDateLink->setName("Date d'ajout");
            $addedDateLink->setEqLogic_id($newEqLogic->getId());
            $addedDateLink->setLogicalId("addedDateLink");
            $addedDateLink->setType('info');
            $addedDateLink->setSubType('string');
            $addedDateLink->setOrder(27);
            $addedDateLink->save();
			
			$bytesTotalLink = $newEqLogic->getCmd(null,'bytesTotalLink');
            if (!is_object($bytesTotalLink)) {
                $bytesTotalLink = new jdownloaderCmd();
            }
            $bytesTotalLink->setName("Taille totale du lien");
            $bytesTotalLink->setEqLogic_id($newEqLogic->getId());
            $bytesTotalLink->setLogicalId("bytesTotalLink");
            $bytesTotalLink->setType('info');
            $bytesTotalLink->setSubType('numeric');
            $bytesTotalLink->setUnite("MB");
            $bytesTotalLink->setTemplate('dashboard', 'line');
            $bytesTotalLink->setTemplate('mobile', 'line');
            $bytesTotalLink->setOrder(28);
            $bytesTotalLink->save();
            
            $bytesLoadedLink = $newEqLogic->getCmd(null,'bytesLoadedLink');
            if (!is_object($bytesLoadedLink)) {
                $bytesLoadedLink = new jdownloaderCmd();
            }
            $bytesLoadedLink->setName("Données téléchargé du lien");
            $bytesLoadedLink->setEqLogic_id($newEqLogic->getId());
            $bytesLoadedLink->setLogicalId("bytesLoadedLink");
            $bytesLoadedLink->setType('info');
            $bytesLoadedLink->setSubType('numeric');
            $bytesLoadedLink->setUnite("MB");
            $bytesLoadedLink->setTemplate('dashboard', 'line');
            $bytesLoadedLink->setTemplate('mobile', 'line');
            $bytesLoadedLink->setOrder(29);
            $bytesLoadedLink->save();
            
            $progressLink = $newEqLogic->getCmd(null,'progressLink');
            if (!is_object($progressLink)) {
                $progressLink = new jdownloaderCmd();
            }
            $progressLink->setName("Progression du lien");
            $progressLink->setEqLogic_id($newEqLogic->getId());
            $progressLink->setLogicalId("progressLink");
            $progressLink->setType('info');
            $progressLink->setSubType('numeric');
            $progressLink->setUnite("%");
            $progressLink->setTemplate('dashboard', 'line');
            $progressLink->setTemplate('mobile', 'line');
            $progressLink->setOrder(30);
            $progressLink->save();
			
			$hostLink = $newEqLogic->getCmd(null,'hostLink');
            if (!is_object($hostLink)) {
                $hostLink = new jdownloaderCmd();
            }
            $hostLink->setName("Hébergeur du lien");
            $hostLink->setEqLogic_id($newEqLogic->getId());
            $hostLink->setLogicalId("hostLink");
            $hostLink->setType('info');
            $hostLink->setSubType('string');
            $hostLink->setOrder(31);
            $hostLink->save();
			
			$urlLink = $newEqLogic->getCmd(null,'urlLink');
            if (!is_object($urlLink)) {
                $urlLink = new jdownloaderCmd();
            }
            $urlLink->setName("URL du lien");
            $urlLink->setEqLogic_id($newEqLogic->getId());
            $urlLink->setLogicalId("urlLink");
            $urlLink->setType('info');
            $urlLink->setSubType('string');
            $urlLink->setOrder(32);
            $urlLink->save();
			
			$availabilityLink = $newEqLogic->getCmd(null,'availabilityLink');
            if (!is_object($availabilityLink)) {
                $availabilityLink = new jdownloaderCmd();
            }
            $availabilityLink->setName("Disponibilité du lien");
            $availabilityLink->setEqLogic_id($newEqLogic->getId());
            $availabilityLink->setLogicalId("availabilityLink");
            $availabilityLink->setType('info');
            $availabilityLink->setSubType('string');
            $availabilityLink->setOrder(33);
            $availabilityLink->save();
			
			$speedLink = $newEqLogic->getCmd(null,'speedLink');
            if (!is_object($speedLink)) {
                $speedLink = new jdownloaderCmd();
            }
            $speedLink->setName("Vitesse lien");
            $speedLink->setEqLogic_id($newEqLogic->getId());
            $speedLink->setLogicalId("speedLink");
            $speedLink->setType('info');
            $speedLink->setSubType('numeric');
            $speedLink->setUnite("ko/s");
            $speedLink->setTemplate('dashboard', 'line');
            $speedLink->setTemplate('mobile', 'line');
            $speedLink->setOrder(34);
            $speedLink->save();
            
            $statusLink = $newEqLogic->getCmd(null,'statusLink');
            if (!is_object($statusLink)) {
                $statusLink= new jdownloaderCmd();
            }
            $statusLink->setName("Status lien");
            $statusLink->setEqLogic_id($newEqLogic->getId());
            $statusLink->setLogicalId("statusLink");
            $statusLink->setType('info');
            $statusLink->setSubType('string');
            $statusLink->setOrder(35);
            $statusLink->save();
            
            $runningLink = $newEqLogic->getCmd(null,'runningLink');
            if (!is_object($runningLink)) {
                $runningLink = new jdownloaderCmd();
            }
            $runningLink->setName("Lien en téléchargement");
            $runningLink->setEqLogic_id($newEqLogic->getId());
            $runningLink->setLogicalId("runningLink");
            $runningLink->setType('info');
            $runningLink->setSubType('binary');
            $runningLink->setTemplate('dashboard', 'line');
            $runningLink->setTemplate('mobile', 'line');
            $runningLink->setOrder(36);
            $runningLink->save();
        }
    }

    /*     * *********************Méthodes d'instance************************* */
    
    function getAllFromJdownloader() {
        $j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
        $systemInfos = $j->getSystemInfos($this->getLogicalId());
        $systemInfos = json_decode($systemInfos, true);
        $uptime = $j->getUptime($this->getLogicalId());
        $uptime = json_decode($uptime, true);
        $packagesCollector = $j->queryPackagesFromCollector($this->getLogicalId());
        $packagesCollector = json_decode($packagesCollector, true);
        $packagesDownload = $j->queryPackagesFromDownloads($this->getLogicalId());
        $packagesDownload = json_decode($packagesDownload, true);
        $linksCollector = $j->queryLinksFromCollector($this->getLogicalId());
        $linksCollector = json_decode($linksCollector, true);
        $linksDownload = $j->queryLinksFromDownloads($this->getLogicalId());
        $linksDownload = json_decode($linksDownload, true);
        $versionInfo = $j->getCoreRevision($this->getLogicalId());
        $versionInfo = json_decode($versionInfo, true);
        $j->disconnect();
        log::add('jdownloader', 'debug', print_r($systemInfos, true));
        log::add('jdownloader', 'debug', print_r($uptime, true));
        log::add('jdownloader', 'debug', print_r($packagesCollector, true));
        log::add('jdownloader', 'debug', print_r($packagesDownload, true));
        log::add('jdownloader', 'debug', print_r($linksCollector, true));
        log::add('jdownloader', 'debug', print_r($linksDownload, true));
        return array(
            "systemInfos" => $systemInfos,
            "uptime" => $uptime,
            "packagesCollector" => $packagesCollector,
            "packagesDownload" => $packagesDownload,
            "linksCollector" => $linksCollector,
            "linksDownload" => $linksDownload,
            "versionInfo" => $versionInfo
        );
    }
    
    function getPackageDatasFromJdownloader($packageInfos) {
        $packageInfosArray = explode("_", $packageInfos);
        if (count($packageInfosArray) == 2) {
            $j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
            if ($packageInfosArray[1] == 'collector') {
                $packageDatas = $j->queryPackagesFromCollector($this->getLogicalId(), array("packageUUIDs" => array($packageInfosArray[0])));
            }
            else if ($packageInfosArray[1] == 'download') {
                $packageDatas = $j->queryPackagesFromDownloads($this->getLogicalId(), array("packageUUIDs" => array($packageInfosArray[0])));
            }
            $packageDatas = json_decode($packageDatas, true);
            $j->disconnect();
            log::add('jdownloader', 'debug', print_r($packageDatas, true));
            return $packageDatas;
        }
        return array();
    }
    
    function getLinksDatasFromJdownloader($packageInfos) {
        $packageInfosArray = explode("_", $packageInfos);
        if (count($packageInfosArray) == 2) {
            $j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
            if ($packageInfosArray[1] == 'collector') {
                $linksDatas = $j->queryLinksFromCollector($this->getLogicalId(), array("packageUUIDs" => array($packageInfosArray[0])));
            }
            else if ($packageInfosArray[1] == 'download') {
                $linksDatas = $j->queryLinksFromDownloads($this->getLogicalId(), array("packageUUIDs" => array($packageInfosArray[0])));
            }
            $linksDatas = json_decode($linksDatas, true);
            $j->disconnect();
            log::add('jdownloader', 'debug', print_r($linksDatas, true));
            return $linksDatas;
        }
        return array();
    }
    
    function updatePackageCmd($packageDatas, $cmdId) {
        $cmd = $this->getCmd(null, $cmdId . 'Package');
        if (is_object($cmd)) {
            if (array_key_exists($cmdId, $packageDatas) && !empty($packageDatas[$cmdId])) {
                $cmd->setIsVisible(1);
                $cmd->save();
                if ($cmd->formatValue($packageDatas[$cmdId]) != $cmd->execCmd()) {
                    $cmd->setCollectDate('');
                    $cmd->event($packageDatas[$cmdId]);
                }
            }
            else {
                $cmd->setIsVisible(0);
                $cmd->save();
            }
        }
    }
	
	function updateLinkCmd($linkDatas, $cmdId) {
        $cmd = $this->getCmd(null, $cmdId . 'Link');
        if (is_object($cmd)) {
            if (array_key_exists($cmdId, $linkDatas) && !empty($linkDatas[$cmdId])) {
                $cmd->setIsVisible(1);
                $cmd->save();
                if ($cmd->formatValue($linkDatas[$cmdId]) != $cmd->execCmd()) {
                    $cmd->setCollectDate('');
                    $cmd->event($linkDatas[$cmdId]);
                }
            }
            else {
                $cmd->setIsVisible(0);
                $cmd->save();
            }
        }
    }
    
    function updateDeviceCmds($JdownloaderDatas) {
        $version = $this->getCmd(null, "version");
        if (is_object($version)) {
            if ($version->formatValue($JdownloaderDatas['versionInfo']['data']) != $version->execCmd()) {
                $version->setCollectDate('');
                $version->event($JdownloaderDatas['versionInfo']['data']);
            }
        }
        $javaVersion = $this->getCmd(null, "javaVersion");
        if (is_object($javaVersion)) {
            if ($javaVersion->formatValue($JdownloaderDatas['systemInfos']['data']['javaVersionString']) != $javaVersion->execCmd()) {
                $javaVersion->setCollectDate('');
                $javaVersion->event($JdownloaderDatas['systemInfos']['data']['javaVersionString']);
            }
        }
        $startupTime = $this->getCmd(null, "startupTime");
        if (is_object($startupTime)) {
            if ($startupTime->formatValue(date('d/m/Y H:i:s', ($JdownloaderDatas['systemInfos']['data']['startupTimeStamp']/1000))) != $startupTime->execCmd()) {
                $startupTime->setCollectDate('');
                $startupTime->event(date('d/m/Y H:i:s', ($JdownloaderDatas['systemInfos']['data']['startupTimeStamp']/1000)));
            }
        }
        $packageCollectorNb = $this->getCmd(null, "packageCollectorNb");
        if (is_object($packageCollectorNb)) {
            if ($packageCollectorNb->formatValue(count($JdownloaderDatas['packagesCollector']['data'])) != $packageCollectorNb->execCmd()) {
                $packageCollectorNb->setCollectDate('');
                $packageCollectorNb->event(count($JdownloaderDatas['packagesCollector']['data']));
            }
        }
        $linkCollectorNb = $this->getCmd(null, "linkCollectorNb");
        if (is_object($linkCollectorNb)) {
            if ($linkCollectorNb->formatValue(count($JdownloaderDatas['linksCollector']['data'])) != $linkCollectorNb->execCmd()) {
                $linkCollectorNb->setCollectDate('');
                $linkCollectorNb->event(count($JdownloaderDatas['linksCollector']['data']));
            }
        }
        $packageDownloadNb = $this->getCmd(null, "packageDownloadNb");
        if (is_object($packageDownloadNb)) {
            if ($packageDownloadNb->formatValue(count($JdownloaderDatas['packagesDownload']['data'])) != $packageDownloadNb->execCmd()) {
                $packageDownloadNb->setCollectDate('');
                $packageDownloadNb->event(count($JdownloaderDatas['packagesDownload']['data']));
            }
        }
        $linkDownloadNb = $this->getCmd(null, "linkDownloadNb");
        if (is_object($linkDownloadNb)) {
            if ($linkDownloadNb->formatValue(count($JdownloaderDatas['linksDownload']['data'])) != $linkDownloadNb->execCmd()) {
                $linkDownloadNb->setCollectDate('');
                $linkDownloadNb->event(count($JdownloaderDatas['linksDownload']['data']));
            }
        }
        $totalSpeed = $this->getCmd(null,'totalSpeed');
        if (is_object($totalSpeed)) {
            $value = 0;
            foreach ($JdownloaderDatas['packagesDownload']['data'] as $package) {
                $value = $value + $package['speed'];
            }
            if ($totalSpeed->formatValue(round(($value/1000), 2)) != $totalSpeed->execCmd()) {
                $totalSpeed->setCollectDate('');
                $totalSpeed->event(round(($value/1000), 2));
            }
        }
        $packageList = $this->getCmd(null,'packageList');
        if (is_object($packageList)) {
            $list = "";
            foreach ($JdownloaderDatas['packagesCollector']['data'] as $package) {
                $list = $list . $separator . $package['uuid'] . '_collector' . '|' . $package['name'];
                $separator = ';';
            }
            foreach ($JdownloaderDatas['packagesDownload']['data'] as $package) {
                $list = $list . $separator . $package['uuid'] . '_download' . '|' . $package['name'];
                $separator = ';';
            }
            $packageList->setConfiguration('listValue', $list);
            $packageList->save();
        }
    }
    
    function updatePackageCmds($packageDatas, $linksDatas) {
        $this->updatePackageCmd($packageDatas, 'enabled');
        $this->updatePackageCmd($packageDatas, 'childCount');
        $bytesTotal = $this->getCmd(null,'bytesTotalPackage');
        if (is_object($bytesTotal)) {
            if (array_key_exists('bytesTotal', $packageDatas)) {
                $bytesTotal->setIsVisible(1);
                $bytesTotal->save();
                if ($bytesTotal->formatValue(round(($packageDatas['bytesTotal']/1000000), 2)) != $bytesTotal->execCmd()) {
                    $bytesTotal->setCollectDate('');
                    $bytesTotal->event(round(($packageDatas['bytesTotal']/1000000), 2));
                }
            }
            else {
                $bytesTotal->setIsVisible(0);
                $bytesTotal->save();
            }
        }
        $bytesLoaded = $this->getCmd(null,'bytesLoadedPackage');
        if (is_object($bytesLoaded)) {
            if (array_key_exists('bytesLoaded', $packageDatas)) {
                $bytesLoaded->setIsVisible(1);
                $bytesLoaded->save();
                if ($bytesLoaded->formatValue(round(($packageDatas['bytesLoaded']/1000000), 2)) != $bytesLoaded->execCmd()) {
                    $bytesLoaded->setCollectDate('');
                    $bytesLoaded->event(round(($packageDatas['bytesLoaded']/1000000), 2));
                }
            }
            else {
                $bytesLoaded->setIsVisible(0);
                $bytesLoaded->save();
            }
        }
        $progress = $this->getCmd(null,'progressPackage');
        if (is_object($progress)) {
            if (array_key_exists('bytesTotal', $packageDatas) && array_key_exists('bytesLoaded', $packageDatas)) {
                $progress->setIsVisible(1);
                $progress->save();
                if ($progress->formatValue(round((($packageDatas['bytesLoaded']/$packageDatas['bytesTotal'])*100), 1)) != $progress->execCmd()) {
                    $progress->setCollectDate('');
                    $progress->event(round((($packageDatas['bytesLoaded']/$packageDatas['bytesTotal'])*100), 1));
                }
            }
            else {
                $progress->setIsVisible(0);
                $progress->save();
            }
        }
        $this->updatePackageCmd($packageDatas, 'saveTo');
        $hosts = $this->getCmd(null,'hostsPackage');
        if (is_object($hosts)) {
            if (array_key_exists('hosts', $packageDatas)) {
                $hosts->setIsVisible(1);
                $hosts->save();
                $list = "";
                foreach ($packageDatas['hosts'] as $host) {
                    $list = $list . $separator . $host;
                    $separator = ',';
                }
                if ($hosts->formatValue($list) != $hosts->execCmd()) {
                    $hosts->setCollectDate('');
                    $hosts->event($list);
                }
            }
            else {
                $hosts->setIsVisible(0);
                $hosts->save();
            }
        }
        $this->updatePackageCmd($packageDatas, 'onlineCount');
        $this->updatePackageCmd($packageDatas, 'offlineCount');
        $this->updatePackageCmd($packageDatas, 'unknownCount');
        $speed = $this->getCmd(null,'speedPackage');
        if (is_object($speed)) {
            if (array_key_exists('speed', $packageDatas)) {
                $speed->setIsVisible(1);
                $speed->save();
                if ($speed->formatValue(round(($packageDatas['speed']/1000), 2)) != $speed->execCmd()) {
                    $speed->setCollectDate('');
                    $speed->event(round(($packageDatas['speed']/1000), 2));
                }
            }
            else {
                $speed->setIsVisible(0);
                $speed->save();
            }
        }
        $this->updatePackageCmd($packageDatas, 'status');
        $this->updatePackageCmd($packageDatas, 'running');
        $linkList = $this->getCmd(null,'linkListPackage');
        if (is_object($linkList)) {
            if (count($linksDatas) > 0) {
                $linkList->setIsVisible(1);
                $separator = "";
                $list = "";
                foreach ($linksDatas as $link) {
                    $list = $list . $separator . $link['uuid'] . '|' . $link['name'];
                    $separator = ';';
                }
                $linkList->setConfiguration('listValue', $list);
            }
            else {
                $linkList->setIsVisible(0);
            }
            $linkList->save();
        }
    }
    
    function updateLinksCmds($linkDatas) {
        log::add('jdownloader', 'debug', print_r($linkDatas, true));
		$this->updateLinkCmd($linkDatas, 'enabled');
		$bytesTotal = $this->getCmd(null,'bytesTotalLink');
        if (is_object($bytesTotal)) {
            if (array_key_exists('bytesTotal', $linkDatas)) {
                $bytesTotal->setIsVisible(1);
                $bytesTotal->save();
                if ($bytesTotal->formatValue(round(($linkDatas['bytesTotal']/1000000), 2)) != $bytesTotal->execCmd()) {
                    $bytesTotal->setCollectDate('');
                    $bytesTotal->event(round(($linkDatas['bytesTotal']/1000000), 2));
                }
            }
            else {
                $bytesTotal->setIsVisible(0);
                $bytesTotal->save();
            }
        }
        $bytesLoaded = $this->getCmd(null,'bytesLoadedLink');
        if (is_object($bytesLoaded)) {
            if (array_key_exists('bytesLoaded', $linkDatas)) {
                $bytesLoaded->setIsVisible(1);
                $bytesLoaded->save();
                if ($bytesLoaded->formatValue(round(($linkDatas['bytesLoaded']/1000000), 2)) != $bytesLoaded->execCmd()) {
                    $bytesLoaded->setCollectDate('');
                    $bytesLoaded->event(round(($linkDatas['bytesLoaded']/1000000), 2));
                }
            }
            else {
                $bytesLoaded->setIsVisible(0);
                $bytesLoaded->save();
            }
        }
        $progress = $this->getCmd(null,'progressLink');
        if (is_object($progress)) {
            if (array_key_exists('bytesTotal', $linkDatas) && array_key_exists('bytesLoaded', $linkDatas)) {
                $progress->setIsVisible(1);
                $progress->save();
                if ($progress->formatValue(round((($linkDatas['bytesLoaded']/$linkDatas['bytesTotal'])*100), 1)) != $progress->execCmd()) {
                    $progress->setCollectDate('');
                    $progress->event(round((($linkDatas['bytesLoaded']/$linkDatas['bytesTotal'])*100), 1));
                }
            }
            else {
                $progress->setIsVisible(0);
                $progress->save();
            }
        }
		$this->updateLinkCmd($linkDatas, 'host');
		$this->updateLinkCmd($linkDatas, 'url');
		$this->updateLinkCmd($linkDatas, 'availability');
		$speedLink = $this->getCmd(null,'speedLink');
        if (is_object($speedLink)) {
            if (array_key_exists('speed', $linkDatas)) {
                $speedLink->setIsVisible(1);
                $speedLink->save();
                if ($speedLink->formatValue(round(($linkDatas['speed']/1000), 2)) != $speedLink->execCmd()) {
                    $speedLink->setCollectDate('');
                    $speedLink->event(round(($linkDatas['speed']/1000), 2));
                }
            }
            else {
                $speedLink->setIsVisible(0);
                $speedLink->save();
            }
        }
        $this->updateLinkCmd($linkDatas, 'status');
        $this->updateLinkCmd($linkDatas, 'running');
		$addedDateLink = $this->getCmd(null, "addedDateLink");
        if (is_object($addedDateLink)) {
			if (array_key_exists('addedDate', $linkDatas)) {
				$addedDateLink->setIsVisible(1);
                $addedDateLink->save();
				if ($addedDateLink->formatValue(date('d/m/Y H:i:s', ($linkDatas['addedDate']/1000))) != $addedDateLink->execCmd()) {
					$addedDateLink->setCollectDate('');
					$addedDateLink->event(date('d/m/Y H:i:s', ($linkDatas['addedDate']/1000)));
				}
			}
			else {
                $addedDateLink->setIsVisible(0);
                $addedDateLink->save();
            }
        }
    }
    
    public function updateDeviceInfos() {
        $JdownloaderDatas = $this->getAllFromJdownloader();
        $this->updateDeviceCmds($JdownloaderDatas);
        $package = $this->getCmd(null,'package');
        if (is_object($package)) {
            $packages = array_merge($JdownloaderDatas['packagesCollector']['data'], $JdownloaderDatas['packagesDownload']['data']);
            $packageUuid = str_replace(array("_collector", "_download"), "", $package->execCmd());
            foreach ($packages as $package) {
                if ($packageUuid == $package['uuid']) {
                    $packageFound = $package;
                    break;
                }
            }
            if (!isset($packageFound)) {
                if (count($packages) > 0) {
                    $packageFound = $packages[0];
                    if (array_key_exists("tempUnknownCount", $packageFound)) {
                        $this->savePackageValue($packageFound['uuid'] . '_collector');
                    }
                    else {
                        $this->savePackageValue($packageFound['uuid'] . '_download');
                    }
                    
                }
                else {
                    $this->savePackageValue("");
                    $this->saveLinkValue("");
                    $this->refreshWidget();
                    return;
                }
            }
            $linksDatas = array();
            $links = array_merge($JdownloaderDatas['linksCollector']['data'], $JdownloaderDatas['linksDownload']['data']);
            foreach ($links as $link) {
                if ($link['packageUUID'] == $packageFound['uuid']) {
                    $linksDatas[] = $link;
                }
            }
            $this->updatePackageCmds($packageFound, $linksDatas);
            $linkCmd = $this->getCmd(null, 'linkPackage');
            if (is_object($linkCmd)) {
                foreach ($linksDatas as $link) {
                    if ($linkCmd->execCmd() == $link['uuid']) {
                        $linkFound = $link;
                        break;
                    }
                }
                if (!isset($linkFound)) {
                    if (count($linksDatas) > 0) {
                        $linkFound = $linksDatas[0];
                        $this->saveLinkValue($linkFound['uuid']);
                    }
                    else {
                        $this->saveLinkValue("");
                        $this->refreshWidget();
                        return;
                    }
                }
                $this->updateLinksCmds($linkFound);
            }
        }
        $this->refreshWidget();
    }
    
    public function updatePackageInfos($packageInfos) {
        $this->savePackageValue($packageInfos);
        $packageDatas = $this->getPackageDatasFromJdownloader($packageInfos);
        $linksDatas = $this->getLinksDatasFromJdownloader($packageInfos);
        $this->updatePackageCmds($packageDatas['data'][0], $linksDatas['data']);
        if (count($linksDatas['data']) > 0) {
            $this->saveLinkValue($linksDatas['data'][0]['uuid']);
            $this->updateLinksCmds($linksDatas['data'][0]);
        }
        $this->refreshWidget();
    }
    
    public function updateLinkInfos($linkUuid) {
        $this->saveLinkValue($linkUuid);
        $package = $this->getCmd(null,'package');
        if (is_object($package)) {
            $linksDatas = $this->getLinksDatasFromJdownloader($package->execCmd());
            foreach ($linksDatas['data'] as $link) {
                if ($link['uuid'] == $linkUuid) {
                    $this->updateLinksCmds($link);
                    break;
                }
            }
        }
        $this->refreshWidget();
    }
    
    public function savePackageValue($value) { 
        $package = $this->getCmd(null,'package');
        if (is_object($package)) {
            if ($package->formatValue($value) != $package->execCmd()) {
                $package->setCollectDate('');
                $package->event($value);
            }
        }
    }
    
    public function saveLinkValue($value) {
        $link = $this->getCmd(null,'linkPackage');
        if (is_object($link)) {
            if ($link->formatValue($value) != $link->execCmd()) {
                $link->setCollectDate('');
                $link->event($value);
            }
        }
    }
    
 // Fonction exécutée automatiquement avant la création de l'équipement 
    public function preInsert() {
        
    }

 // Fonction exécutée automatiquement après la création de l'équipement 
    public function postInsert() {
        
    }

 // Fonction exécutée automatiquement avant la mise à jour de l'équipement 
    public function preUpdate() {
        
    }

 // Fonction exécutée automatiquement après la mise à jour de l'équipement 
    public function postUpdate() {
        
    }

 // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement 
    public function preSave() {
        
    }

 // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement 
    public function postSave() {
        
    }

 // Fonction exécutée automatiquement avant la suppression de l'équipement 
    public function preRemove() {
        
    }

 // Fonction exécutée automatiquement après la suppression de l'équipement 
    public function postRemove() {
        
    }

    /*
     * Non obligatoire : permet de modifier l'affichage du widget (également utilisable par les commandes)
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire : permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire : permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class jdownloaderCmd extends cmd {
    /*     * *************************Attributs****************************** */
    
    /*
      public static $_widgetPossibility = array();
    */
    
    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

  // Exécution d'une commande  
     public function execute($_options = array()) {
        $eqLogic = $this->getEqLogic();
        if (!is_object($eqLogic) || $eqLogic->getIsEnable() != 1) {
            throw new Exception(__('Equipement desactivé impossible d\éxecuter la commande : ' . $this->getHumanName(), __FILE__));
        }
		log::add('jdownloader','debug','command: '.$this->getLogicalId().' parameters: '.json_encode($_options));
        switch ($this->getLogicalId()) {
			case "refresh":
                $eqLogic->updateDeviceInfos();
				return true;
            case "packageList":
                $eqLogic->updatePackageInfos($_options['select']);
                return true;
            case "linkListPackage":
                $eqLogic->updateLinkInfos($_options['select']);
                return true;
            default:
                return false;
		}
     }

    /*     * **********************Getteur Setteur*************************** */
}


