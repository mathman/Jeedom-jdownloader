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
            $newEqLogic->updateCmds();
        }
    }

    /*     * *********************Méthodes d'instance************************* */
    
    function updateCmds() {
        $refresh = $this->getCmd(null, 'refresh');
        if (!is_object($refresh)) {
            $refresh = new jdownloaderCmd();
        }
        $refresh->setName('Rafraichir');
        $refresh->setEqLogic_id($this->getId());
        $refresh->setLogicalId('refresh');
        $refresh->setType('action');
        $refresh->setSubType('other');
        $refresh->setOrder(0);
        $refresh->save();
            
        $version = $this->getCmd(null, "version");
        if (!is_object($version)) {
            $version = new jdownloaderCmd();
        }
        $version->setName("Version");
        $version->setEqLogic_id($this->getId());
        $version->setLogicalId("version");
        $version->setType('info');
        $version->setSubType('string');
        $version->setOrder(1);
        $version->save();
		
		$updateVersion = $this->getCmd(null, "updateVersion");
        if (!is_object($updateVersion)) {
            $updateVersion = new jdownloaderCmd();
        }
        $updateVersion->setName("Mise à jour disponible");
        $updateVersion->setEqLogic_id($this->getId());
        $updateVersion->setLogicalId("updateVersion");
        $updateVersion->setType('info');
        $updateVersion->setSubType('string');
        $updateVersion->setOrder(2);
		$updateVersion->setDisplay('icon', '<i class="fas fa-info-circle"></i>');
        $updateVersion->setDisplay('showIconAndNamedashboard', '1');
        $updateVersion->setDisplay('showIconAndNamemobile', '1');
        $updateVersion->save();
            
        $javaVersion = $this->getCmd(null, "javaVersion");
        if (!is_object($javaVersion)) {
            $javaVersion = new jdownloaderCmd();
        }
        $javaVersion->setName("Version Java");
        $javaVersion->setEqLogic_id($this->getId());
        $javaVersion->setLogicalId("javaVersion");
        $javaVersion->setType('info');
        $javaVersion->setSubType('string');
        $javaVersion->setOrder(3);
        $javaVersion->save();
        
        $startupTime = $this->getCmd(null, "startupTime");
        if (!is_object($startupTime)) {
            $startupTime = new jdownloaderCmd();
        }
        $startupTime->setName("Dernier redémarrage");
        $startupTime->setEqLogic_id($this->getId());
        $startupTime->setLogicalId("startupTime");
        $startupTime->setType('info');
        $startupTime->setSubType('string');
        $startupTime->setOrder(4);
        $startupTime->save();
        
        $restart = $this->getCmd(null,'restart');
        if (!is_object($restart)) {
            $restart = new jdownloaderCmd();
        }
        $restart->setName("Redémarrer");
        $restart->setEqLogic_id($this->getId());
        $restart->setLogicalId("restart");
        $restart->setType('action');
        $restart->setSubType('other');
        $restart->setOrder(5);
        $restart->setConfiguration('actionConfirm', '1');
        $restart->setDisplay('icon', '<i class="fas fa-sync"></i>');
        $restart->setDisplay('showIconAndNamedashboard', '1');
        $restart->setDisplay('showIconAndNamemobile', '1');
        $restart->save();
        
        $packageCollectorNb = $this->getCmd(null, "packageCollectorNb");
        if (!is_object($packageCollectorNb)) {
            $packageCollectorNb = new jdownloaderCmd();
        }
        $packageCollectorNb->setName("Nombre de paquets en attente");
        $packageCollectorNb->setEqLogic_id($this->getId());
        $packageCollectorNb->setLogicalId("packageCollectorNb");
        $packageCollectorNb->setType('info');
        $packageCollectorNb->setSubType('numeric');
        $packageCollectorNb->setTemplate('dashboard', 'line');
        $packageCollectorNb->setTemplate('mobile', 'line');
        $packageCollectorNb->setOrder(6);
        $packageCollectorNb->save();
        
        $linkCollectorNb = $this->getCmd(null, "linkCollectorNb");
        if (!is_object($linkCollectorNb)) {
            $linkCollectorNb = new jdownloaderCmd();
        }
        $linkCollectorNb->setName("Nombre de liens en attente");
        $linkCollectorNb->setEqLogic_id($this->getId());
        $linkCollectorNb->setLogicalId("linkCollectorNb");
        $linkCollectorNb->setType('info');
        $linkCollectorNb->setSubType('numeric');
        $linkCollectorNb->setTemplate('dashboard', 'line');
        $linkCollectorNb->setTemplate('mobile', 'line');
        $linkCollectorNb->setOrder(7);
        $linkCollectorNb->save();
        
        $packageDownloadNb = $this->getCmd(null, "packageDownloadNb");
        if (!is_object($packageDownloadNb)) {
            $packageDownloadNb = new jdownloaderCmd();
        }
        $packageDownloadNb->setName("Nombre de paquets en téléchargement");
        $packageDownloadNb->setEqLogic_id($this->getId());
        $packageDownloadNb->setLogicalId("packageDownloadNb");
        $packageDownloadNb->setType('info');
        $packageDownloadNb->setSubType('numeric');
        $packageDownloadNb->setTemplate('dashboard', 'line');
        $packageDownloadNb->setTemplate('mobile', 'line');
        $packageDownloadNb->setOrder(8);
        $packageDownloadNb->save();
        
        $linkDownloadNb = $this->getCmd(null, "linkDownloadNb");
        if (!is_object($linkDownloadNb)) {
            $linkDownloadNb = new jdownloaderCmd();
        }
        $linkDownloadNb->setName("Nombre de liens en téléchargement");
        $linkDownloadNb->setEqLogic_id($this->getId());
        $linkDownloadNb->setLogicalId("linkDownloadNb");
        $linkDownloadNb->setType('info');
        $linkDownloadNb->setSubType('numeric');
        $linkDownloadNb->setTemplate('dashboard', 'line');
        $linkDownloadNb->setTemplate('mobile', 'line');
        $linkDownloadNb->setOrder(9);
        $linkDownloadNb->save();
        
        $totalSpeed = $this->getCmd(null, "totalSpeed");
        if (!is_object($totalSpeed)) {
            $totalSpeed = new jdownloaderCmd();
        }
        $totalSpeed->setName("Vitesse totale");
        $totalSpeed->setEqLogic_id($this->getId());
        $totalSpeed->setLogicalId("totalSpeed");
        $totalSpeed->setType('info');
        $totalSpeed->setSubType('numeric');
        $totalSpeed->setUnite("ko/s");
        $totalSpeed->setTemplate('dashboard', 'line');
        $totalSpeed->setTemplate('mobile', 'line');
        $totalSpeed->setOrder(10);
        $totalSpeed->save();
		
		$state = $this->getCmd(null, "state");
        if (!is_object($state)) {
            $state = new jdownloaderCmd();
        }
        $state->setName("Etat");
        $state->setEqLogic_id($this->getId());
        $state->setLogicalId("state");
        $state->setType('info');
        $state->setSubType('string');
        $state->setOrder(11);
        $state->save();

        $start = $this->getCmd(null,'start');
        if (!is_object($start)) {
            $start = new jdownloaderCmd();
        }
        $start->setName("Démarrer téléchargements");
        $start->setEqLogic_id($this->getId());
        $start->setLogicalId("start");
        $start->setType('action');
        $start->setSubType('other');
        $start->setOrder(12);
        $start->setDisplay('icon', '<i class="fas fa-play"></i>');
        $start->setDisplay('showIconAndNamedashboard', '1');
        $start->setDisplay('showIconAndNamemobile', '1');
        $start->save();
        
        $stop = $this->getCmd(null,'stop');
        if (!is_object($stop)) {
            $stop = new jdownloaderCmd();
        }
        $stop->setName("Arrêter téléchargements");
        $stop->setEqLogic_id($this->getId());
        $stop->setLogicalId("stop");
        $stop->setType('action');
        $stop->setSubType('other');
        $stop->setOrder(13);
        $stop->setDisplay('icon', '<i class="fas fa-stop"></i>');
        $stop->setDisplay('showIconAndNamedashboard', '1');
        $stop->setDisplay('showIconAndNamemobile', '1');
        $stop->save();
        
        $pause = $this->getCmd(null,'pause');
        if (!is_object($pause)) {
            $pause = new jdownloaderCmd();
        }
        $pause->setName("Mettre en pause");
        $pause->setEqLogic_id($this->getId());
        $pause->setLogicalId("pause");
        $pause->setType('action');
        $pause->setSubType('other');
        $pause->setOrder(14);
        $pause->setDisplay('icon', '<i class="fas fa-pause"></i>');
        $pause->setDisplay('showIconAndNamedashboard', '1');
        $pause->setDisplay('showIconAndNamemobile', '1');
        $pause->save();
        
        $package = $this->getCmd(null,'package');
        if (!is_object($package)) {
            $package = new jdownloaderCmd();
        }
        $package->setName("Paquet");
        $package->setEqLogic_id($this->getId());
        $package->setLogicalId("package");
        $package->setType('info');
        $package->setSubType('string');
        $package->setIsVisible(0);
        $package->setOrder(15);
        $package->save();
        
        $packageList = $this->getCmd(null,'packageList');
        if (!is_object($packageList)) {
            $packageList = new jdownloaderCmd();
        }
        $packageList->setName("Liste paquets");
        $packageList->setEqLogic_id($this->getId());
        $packageList->setLogicalId("packageList");
        $packageList->setType('action');
        $packageList->setSubType('select');
        $packageList->setValue($this->getCmd(null,'package')->getId());
        $packageList->setOrder(16);
        $packageList->save();
        
        /* *****Commandes package***** */
        $enabledPackage = $this->getCmd(null,'enabledPackage');
        if (!is_object($enabledPackage)) {
            $enabledPackage = new jdownloaderCmd();
        }
        $enabledPackage->setName("Paquet activé");
        $enabledPackage->setEqLogic_id($this->getId());
        $enabledPackage->setLogicalId("enabledPackage");
        $enabledPackage->setType('info');
        $enabledPackage->setSubType('binary');
        $enabledPackage->setTemplate('dashboard', 'line');
        $enabledPackage->setTemplate('mobile', 'line');
        $enabledPackage->setOrder(17);
        $enabledPackage->save();
        
        $enablePackage = $this->getCmd(null,'enablePackage');
        if (!is_object($enablePackage)) {
            $enablePackage = new jdownloaderCmd();
        }
        $enablePackage->setName("Activer paquet");
        $enablePackage->setEqLogic_id($this->getId());
        $enablePackage->setLogicalId("enablePackage");
        $enablePackage->setType('action');
        $enablePackage->setSubType('other');
        $enablePackage->setDisplay('icon', '<i class="fas fa-check"></i>');
        $enablePackage->setDisplay('showIconAndNamedashboard', '1');
        $enablePackage->setDisplay('showIconAndNamemobile', '1');
        $enablePackage->setOrder(18);
        $enablePackage->save();
        
        $disablePackage = $this->getCmd(null,'disablePackage');
        if (!is_object($disablePackage)) {
            $disablePackage = new jdownloaderCmd();
        }
        $disablePackage->setName("Désactiver paquet");
        $disablePackage->setEqLogic_id($this->getId());
        $disablePackage->setLogicalId("disablePackage");
        $disablePackage->setType('action');
        $disablePackage->setSubType('other');
        $disablePackage->setDisplay('icon', '<i class="fas fa-times"></i>');
        $disablePackage->setDisplay('showIconAndNamedashboard', '1');
        $disablePackage->setDisplay('showIconAndNamemobile', '1');
        $disablePackage->setOrder(19);
        $disablePackage->save();

        $bytesTotalPackage = $this->getCmd(null,'bytesTotalPackage');
        if (!is_object($bytesTotalPackage)) {
            $bytesTotalPackage = new jdownloaderCmd();
        }
        $bytesTotalPackage->setName("Taille totale du paquet");
        $bytesTotalPackage->setEqLogic_id($this->getId());
        $bytesTotalPackage->setLogicalId("bytesTotalPackage");
        $bytesTotalPackage->setType('info');
        $bytesTotalPackage->setSubType('numeric');
        $bytesTotalPackage->setUnite("MB");
        $bytesTotalPackage->setTemplate('dashboard', 'line');
        $bytesTotalPackage->setTemplate('mobile', 'line');
        $bytesTotalPackage->setOrder(20);
        $bytesTotalPackage->save();
        
        $bytesLoadedPackage = $this->getCmd(null,'bytesLoadedPackage');
        if (!is_object($bytesLoadedPackage)) {
            $bytesLoadedPackage = new jdownloaderCmd();
        }
        $bytesLoadedPackage->setName("Données téléchargé du paquet");
        $bytesLoadedPackage->setEqLogic_id($this->getId());
        $bytesLoadedPackage->setLogicalId("bytesLoadedPackage");
        $bytesLoadedPackage->setType('info');
        $bytesLoadedPackage->setSubType('numeric');
        $bytesLoadedPackage->setUnite("MB");
        $bytesLoadedPackage->setTemplate('dashboard', 'line');
        $bytesLoadedPackage->setTemplate('mobile', 'line');
        $bytesLoadedPackage->setOrder(21);
        $bytesLoadedPackage->save();
        
        $progressPackage = $this->getCmd(null,'progressPackage');
        if (!is_object($progressPackage)) {
            $progressPackage = new jdownloaderCmd();
        }
        $progressPackage->setName("Progression du paquet");
        $progressPackage->setEqLogic_id($this->getId());
        $progressPackage->setLogicalId("progressPackage");
        $progressPackage->setType('info');
        $progressPackage->setSubType('numeric');
        $progressPackage->setUnite("%");
        $progressPackage->setTemplate('dashboard', 'line');
        $progressPackage->setTemplate('mobile', 'line');
        $progressPackage->setOrder(22);
        $progressPackage->save();
        
        $saveToPackage = $this->getCmd(null,'saveToPackage');
        if (!is_object($saveToPackage)) {
            $saveToPackage = new jdownloaderCmd();
        }
        $saveToPackage->setName("Dossier de téléchargement");
        $saveToPackage->setEqLogic_id($this->getId());
        $saveToPackage->setLogicalId("saveToPackage");
        $saveToPackage->setType('info');
        $saveToPackage->setSubType('string');
        $saveToPackage->setOrder(23);
        $saveToPackage->save();
        
        $hostsPackage = $this->getCmd(null,'hostsPackage');
        if (!is_object($hostsPackage)) {
            $hostsPackage = new jdownloaderCmd();
        }
        $hostsPackage->setName("Hébergeurs du paquet");
        $hostsPackage->setEqLogic_id($this->getId());
        $hostsPackage->setLogicalId("hostsPackage");
        $hostsPackage->setType('info');
        $hostsPackage->setSubType('string');
        $hostsPackage->setOrder(24);
        $hostsPackage->save();
        
        $childCountPackage = $this->getCmd(null,'childCountPackage');
        if (!is_object($childCountPackage)) {
            $childCountPackage = new jdownloaderCmd();
        }
        $childCountPackage->setName("Nombre de liens");
        $childCountPackage->setEqLogic_id($this->getId());
        $childCountPackage->setLogicalId("childCountPackage");
        $childCountPackage->setType('info');
        $childCountPackage->setSubType('numeric');
        $childCountPackage->setTemplate('dashboard', 'line');
        $childCountPackage->setTemplate('mobile', 'line');
        $childCountPackage->setOrder(25);
        $childCountPackage->save();
        
        $onlineCountPackage = $this->getCmd(null,'onlineCountPackage');
        if (!is_object($onlineCountPackage)) {
            $onlineCountPackage = new jdownloaderCmd();
        }
        $onlineCountPackage->setName("Liens en ligne");
        $onlineCountPackage->setEqLogic_id($this->getId());
        $onlineCountPackage->setLogicalId("onlineCountPackage");
        $onlineCountPackage->setType('info');
        $onlineCountPackage->setSubType('numeric');
        $onlineCountPackage->setTemplate('dashboard', 'line');
        $onlineCountPackage->setTemplate('mobile', 'line');
        $onlineCountPackage->setOrder(26);
        $onlineCountPackage->save();
        
        $offlineCountPackage = $this->getCmd(null,'offlineCountPackage');
        if (!is_object($offlineCountPackage)) {
            $offlineCountPackage = new jdownloaderCmd();
        }
        $offlineCountPackage->setName("Liens hors ligne");
        $offlineCountPackage->setEqLogic_id($this->getId());
        $offlineCountPackage->setLogicalId("offlineCountPackage");
        $offlineCountPackage->setType('info');
        $offlineCountPackage->setSubType('numeric');
        $offlineCountPackage->setTemplate('dashboard', 'line');
        $offlineCountPackage->setTemplate('mobile', 'line');
        $offlineCountPackage->setOrder(27);
        $offlineCountPackage->save();
        
        $unknownCountPackage = $this->getCmd(null,'unknownCountPackage');
        if (!is_object($unknownCountPackage)) {
            $unknownCountPackage = new jdownloaderCmd();
        }
        $unknownCountPackage->setName("Liens inconnus");
        $unknownCountPackage->setEqLogic_id($this->getId());
        $unknownCountPackage->setLogicalId("unknownCountPackage");
        $unknownCountPackage->setType('info');
        $unknownCountPackage->setSubType('numeric');
        $unknownCountPackage->setTemplate('dashboard', 'line');
        $unknownCountPackage->setTemplate('mobile', 'line');
        $unknownCountPackage->setOrder(28);
        $unknownCountPackage->save();
        
        $speedPackage = $this->getCmd(null,'speedPackage');
        if (!is_object($speedPackage)) {
            $speedPackage = new jdownloaderCmd();
        }
        $speedPackage->setName("Vitesse paquet");
        $speedPackage->setEqLogic_id($this->getId());
        $speedPackage->setLogicalId("speedPackage");
        $speedPackage->setType('info');
        $speedPackage->setSubType('numeric');
        $speedPackage->setUnite("ko/s");
        $speedPackage->setTemplate('dashboard', 'line');
        $speedPackage->setTemplate('mobile', 'line');
        $speedPackage->setOrder(29);
        $speedPackage->save();
        
        $statusPackage = $this->getCmd(null,'statusPackage');
        if (!is_object($statusPackage)) {
            $statusPackage= new jdownloaderCmd();
        }
        $statusPackage->setName("Status paquet");
        $statusPackage->setEqLogic_id($this->getId());
        $statusPackage->setLogicalId("statusPackage");
        $statusPackage->setType('info');
        $statusPackage->setSubType('string');
        $statusPackage->setOrder(30);
        $statusPackage->save();
        
        $runningPackage = $this->getCmd(null,'runningPackage');
        if (!is_object($runningPackage)) {
            $runningPackage = new jdownloaderCmd();
        }
        $runningPackage->setName("Paquet en téléchargement");
        $runningPackage->setEqLogic_id($this->getId());
        $runningPackage->setLogicalId("runningPackage");
        $runningPackage->setType('info');
        $runningPackage->setSubType('binary');
        $runningPackage->setTemplate('dashboard', 'line');
        $runningPackage->setTemplate('mobile', 'line');
        $runningPackage->setOrder(31);
        $runningPackage->save();
        
        $forceDownloadPackage = $this->getCmd(null,'forceDownloadPackage');
        if (!is_object($forceDownloadPackage)) {
            $forceDownloadPackage = new jdownloaderCmd();
        }
        $forceDownloadPackage->setName("Forcer téléchargement paquet");
        $forceDownloadPackage->setEqLogic_id($this->getId());
        $forceDownloadPackage->setLogicalId("forceDownloadPackage");
        $forceDownloadPackage->setType('action');
        $forceDownloadPackage->setSubType('other');
        $forceDownloadPackage->setDisplay('icon', '<i class="fas fa-play"></i>');
        $forceDownloadPackage->setDisplay('showIconAndNamedashboard', '1');
        $forceDownloadPackage->setDisplay('showIconAndNamemobile', '1');
        $forceDownloadPackage->setDisplay('forceReturnLineAfter', '1');
        $forceDownloadPackage->setOrder(32);
        $forceDownloadPackage->save();
        
        $moveToDownloadListPackage = $this->getCmd(null,'moveToDownloadListPackage');
        if (!is_object($moveToDownloadListPackage)) {
            $moveToDownloadListPackage = new jdownloaderCmd();
        }
        $moveToDownloadListPackage->setName("Ajouter paquet en téléchargement");
        $moveToDownloadListPackage->setEqLogic_id($this->getId());
        $moveToDownloadListPackage->setLogicalId("moveToDownloadListPackage");
        $moveToDownloadListPackage->setType('action');
        $moveToDownloadListPackage->setSubType('other');
        $moveToDownloadListPackage->setConfiguration('actionConfirm', '1');
        $moveToDownloadListPackage->setDisplay('icon', '<i class="fas fa-play"></i>');
        $moveToDownloadListPackage->setDisplay('showIconAndNamedashboard', '1');
        $moveToDownloadListPackage->setDisplay('showIconAndNamemobile', '1');
        $moveToDownloadListPackage->setDisplay('forceReturnLineAfter', '1');
        $moveToDownloadListPackage->setOrder(33);
        $moveToDownloadListPackage->save();
        
        $removePackage = $this->getCmd(null,'removePackage');
        if (!is_object($removePackage)) {
            $removePackage = new jdownloaderCmd();
        }
        $removePackage->setName("Supprimer paquet");
        $removePackage->setEqLogic_id($this->getId());
        $removePackage->setLogicalId("removePackage");
        $removePackage->setType('action');
        $removePackage->setSubType('other');
        $removePackage->setConfiguration('actionConfirm', '1');
        $removePackage->setDisplay('icon', '<i class="fas fa-trash-alt"></i>');
        $removePackage->setDisplay('showIconAndNamedashboard', '1');
        $removePackage->setDisplay('showIconAndNamemobile', '1');
        $removePackage->setDisplay('forceReturnLineAfter', '1');
        $removePackage->setOrder(34);
        $removePackage->save();
        
        $linkPackage = $this->getCmd(null,'linkPackage');
        if (!is_object($linkPackage)) {
            $linkPackage = new jdownloaderCmd();
        }
        $linkPackage->setName("Lien");
        $linkPackage->setEqLogic_id($this->getId());
        $linkPackage->setLogicalId("linkPackage");
        $linkPackage->setType('info');
        $linkPackage->setSubType('string');
        $linkPackage->setIsVisible(0);
        $linkPackage->setOrder(35);
        $linkPackage->save();
        
        $linkListPackage = $this->getCmd(null,'linkListPackage');
        if (!is_object($linkListPackage)) {
            $linkListPackage = new jdownloaderCmd();
        }
        $linkListPackage->setName("Liste liens");
        $linkListPackage->setEqLogic_id($this->getId());
        $linkListPackage->setLogicalId("linkListPackage");
        $linkListPackage->setType('action');
        $linkListPackage->setSubType('select');
        $linkListPackage->setValue($this->getCmd(null,'linkPackage')->getId());
        $linkListPackage->setOrder(36);
        $linkListPackage->save();

        /* *****Commandes links***** */
        $enabledLink = $this->getCmd(null,'enabledLink');
        if (!is_object($enabledLink)) {
            $enabledLink = new jdownloaderCmd();
        }
        $enabledLink->setName("Lien activé");
        $enabledLink->setEqLogic_id($this->getId());
        $enabledLink->setLogicalId("enabledLink");
        $enabledLink->setType('info');
        $enabledLink->setSubType('binary');
        $enabledLink->setTemplate('dashboard', 'line');
        $enabledLink->setTemplate('mobile', 'line');
        $enabledLink->setOrder(37);
        $enabledLink->save();
        
        $enableLink = $this->getCmd(null,'enableLink');
        if (!is_object($enableLink)) {
            $enableLink = new jdownloaderCmd();
        }
        $enableLink->setName("Activer lien");
        $enableLink->setEqLogic_id($this->getId());
        $enableLink->setLogicalId("enableLink");
        $enableLink->setType('action');
        $enableLink->setSubType('other');
        $enableLink->setDisplay('icon', '<i class="fas fa-check"></i>');
        $enableLink->setDisplay('showIconAndNamedashboard', '1');
        $enableLink->setDisplay('showIconAndNamemobile', '1');
        $enableLink->setOrder(38);
        $enableLink->save();
        
        $disableLink = $this->getCmd(null,'disableLink');
        if (!is_object($disableLink)) {
            $disableLink = new jdownloaderCmd();
        }
        $disableLink->setName("Désactiver lien");
        $disableLink->setEqLogic_id($this->getId());
        $disableLink->setLogicalId("disableLink");
        $disableLink->setType('action');
        $disableLink->setSubType('other');
        $disableLink->setDisplay('icon', '<i class="fas fa-times"></i>');
        $disableLink->setDisplay('showIconAndNamedashboard', '1');
        $disableLink->setDisplay('showIconAndNamemobile', '1');
        $disableLink->setOrder(39);
        $disableLink->save();
        
        $addedDateLink = $this->getCmd(null,'addedDateLink');
        if (!is_object($addedDateLink)) {
            $addedDateLink = new jdownloaderCmd();
        }
        $addedDateLink->setName("Date d'ajout");
        $addedDateLink->setEqLogic_id($this->getId());
        $addedDateLink->setLogicalId("addedDateLink");
        $addedDateLink->setType('info');
        $addedDateLink->setSubType('string');
        $addedDateLink->setOrder(40);
        $addedDateLink->save();
        
        $bytesTotalLink = $this->getCmd(null,'bytesTotalLink');
        if (!is_object($bytesTotalLink)) {
            $bytesTotalLink = new jdownloaderCmd();
        }
        $bytesTotalLink->setName("Taille totale du lien");
        $bytesTotalLink->setEqLogic_id($this->getId());
        $bytesTotalLink->setLogicalId("bytesTotalLink");
        $bytesTotalLink->setType('info');
        $bytesTotalLink->setSubType('numeric');
        $bytesTotalLink->setUnite("MB");
        $bytesTotalLink->setTemplate('dashboard', 'line');
        $bytesTotalLink->setTemplate('mobile', 'line');
        $bytesTotalLink->setOrder(41);
        $bytesTotalLink->save();
        
        $bytesLoadedLink = $this->getCmd(null,'bytesLoadedLink');
        if (!is_object($bytesLoadedLink)) {
            $bytesLoadedLink = new jdownloaderCmd();
        }
        $bytesLoadedLink->setName("Données téléchargé du lien");
        $bytesLoadedLink->setEqLogic_id($this->getId());
        $bytesLoadedLink->setLogicalId("bytesLoadedLink");
        $bytesLoadedLink->setType('info');
        $bytesLoadedLink->setSubType('numeric');
        $bytesLoadedLink->setUnite("MB");
        $bytesLoadedLink->setTemplate('dashboard', 'line');
        $bytesLoadedLink->setTemplate('mobile', 'line');
        $bytesLoadedLink->setOrder(42);
        $bytesLoadedLink->save();
        
        $progressLink = $this->getCmd(null,'progressLink');
        if (!is_object($progressLink)) {
            $progressLink = new jdownloaderCmd();
        }
        $progressLink->setName("Progression du lien");
        $progressLink->setEqLogic_id($this->getId());
        $progressLink->setLogicalId("progressLink");
        $progressLink->setType('info');
        $progressLink->setSubType('numeric');
        $progressLink->setUnite("%");
        $progressLink->setTemplate('dashboard', 'line');
        $progressLink->setTemplate('mobile', 'line');
        $progressLink->setOrder(43);
        $progressLink->save();
        
        $hostLink = $this->getCmd(null,'hostLink');
        if (!is_object($hostLink)) {
            $hostLink = new jdownloaderCmd();
        }
        $hostLink->setName("Hébergeur du lien");
        $hostLink->setEqLogic_id($this->getId());
        $hostLink->setLogicalId("hostLink");
        $hostLink->setType('info');
        $hostLink->setSubType('string');
        $hostLink->setOrder(44);
        $hostLink->save();
        
        $urlLink = $this->getCmd(null,'urlLink');
        if (!is_object($urlLink)) {
            $urlLink = new jdownloaderCmd();
        }
        $urlLink->setName("URL du lien");
        $urlLink->setEqLogic_id($this->getId());
        $urlLink->setLogicalId("urlLink");
        $urlLink->setType('info');
        $urlLink->setSubType('string');
        $urlLink->setOrder(45);
        $urlLink->save();
        
        $availabilityLink = $this->getCmd(null,'availabilityLink');
        if (!is_object($availabilityLink)) {
            $availabilityLink = new jdownloaderCmd();
        }
        $availabilityLink->setName("Disponibilité du lien");
        $availabilityLink->setEqLogic_id($this->getId());
        $availabilityLink->setLogicalId("availabilityLink");
        $availabilityLink->setType('info');
        $availabilityLink->setSubType('string');
        $availabilityLink->setOrder(46);
        $availabilityLink->save();
        
        $speedLink = $this->getCmd(null,'speedLink');
        if (!is_object($speedLink)) {
            $speedLink = new jdownloaderCmd();
        }
        $speedLink->setName("Vitesse lien");
        $speedLink->setEqLogic_id($this->getId());
        $speedLink->setLogicalId("speedLink");
        $speedLink->setType('info');
        $speedLink->setSubType('numeric');
        $speedLink->setUnite("ko/s");
        $speedLink->setTemplate('dashboard', 'line');
        $speedLink->setTemplate('mobile', 'line');
        $speedLink->setOrder(47);
        $speedLink->save();
        
        $statusLink = $this->getCmd(null,'statusLink');
        if (!is_object($statusLink)) {
            $statusLink= new jdownloaderCmd();
        }
        $statusLink->setName("Status lien");
        $statusLink->setEqLogic_id($this->getId());
        $statusLink->setLogicalId("statusLink");
        $statusLink->setType('info');
        $statusLink->setSubType('string');
        $statusLink->setOrder(48);
        $statusLink->save();
        
        $runningLink = $this->getCmd(null,'runningLink');
        if (!is_object($runningLink)) {
            $runningLink = new jdownloaderCmd();
        }
        $runningLink->setName("Lien en téléchargement");
        $runningLink->setEqLogic_id($this->getId());
        $runningLink->setLogicalId("runningLink");
        $runningLink->setType('info');
        $runningLink->setSubType('binary');
        $runningLink->setTemplate('dashboard', 'line');
        $runningLink->setTemplate('mobile', 'line');
        $runningLink->setOrder(49);
        $runningLink->save();
        
        $forceDownloadLink = $this->getCmd(null,'forceDownloadLink');
        if (!is_object($forceDownloadLink)) {
            $forceDownloadLink = new jdownloaderCmd();
        }
        $forceDownloadLink->setName("Forcer téléchargement lien");
        $forceDownloadLink->setEqLogic_id($this->getId());
        $forceDownloadLink->setLogicalId("forceDownloadLink");
        $forceDownloadLink->setType('action');
        $forceDownloadLink->setSubType('other');
        $forceDownloadLink->setDisplay('icon', '<i class="fas fa-play"></i>');
        $forceDownloadLink->setDisplay('showIconAndNamedashboard', '1');
        $forceDownloadLink->setDisplay('showIconAndNamemobile', '1');
        $forceDownloadLink->setDisplay('forceReturnLineAfter', '1');
        $forceDownloadLink->setOrder(50);
        $forceDownloadLink->save();
        
        $moveToDownloadListLink = $this->getCmd(null,'moveToDownloadListLink');
        if (!is_object($moveToDownloadListLink)) {
            $moveToDownloadListLink = new jdownloaderCmd();
        }
        $moveToDownloadListLink->setName("Ajouter lien en téléchargement");
        $moveToDownloadListLink->setEqLogic_id($this->getId());
        $moveToDownloadListLink->setLogicalId("moveToDownloadListLink");
        $moveToDownloadListLink->setType('action');
        $moveToDownloadListLink->setSubType('other');
        $moveToDownloadListLink->setConfiguration('actionConfirm', '1');
        $moveToDownloadListLink->setDisplay('icon', '<i class="fas fa-play"></i>');
        $moveToDownloadListLink->setDisplay('showIconAndNamedashboard', '1');
        $moveToDownloadListLink->setDisplay('showIconAndNamemobile', '1');
        $moveToDownloadListLink->setDisplay('forceReturnLineAfter', '1');
        $moveToDownloadListLink->setOrder(51);
        $moveToDownloadListLink->save();
        
        $removeLink = $this->getCmd(null,'removeLink');
        if (!is_object($removeLink)) {
            $removeLink = new jdownloaderCmd();
        }
        $removeLink->setName("Supprimer lien");
        $removeLink->setEqLogic_id($this->getId());
        $removeLink->setLogicalId("removeLink");
        $removeLink->setType('action');
        $removeLink->setSubType('other');
        $removeLink->setConfiguration('actionConfirm', '1');
        $removeLink->setDisplay('icon', '<i class="fas fa-trash-alt"></i>');
        $removeLink->setDisplay('showIconAndNamedashboard', '1');
        $removeLink->setDisplay('showIconAndNamemobile', '1');
        $removeLink->setDisplay('forceReturnLineAfter', '1');
        $removeLink->setOrder(52);
        $removeLink->save();
    }
    
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
		$updateVersion = $j->isUpdateAvailable($this->getLogicalId());
        $updateVersion = json_decode($updateVersion, true);
		$status = $j->getStatusToolbar($this->getLogicalId());
        $status = json_decode($status, true);
        $j->disconnect();
        log::add('jdownloader', 'debug', print_r($systemInfos, true));
        log::add('jdownloader', 'debug', print_r($uptime, true));
        log::add('jdownloader', 'debug', print_r($packagesCollector, true));
        log::add('jdownloader', 'debug', print_r($packagesDownload, true));
        log::add('jdownloader', 'debug', print_r($linksCollector, true));
        log::add('jdownloader', 'debug', print_r($linksDownload, true));
		log::add('jdownloader', 'debug', print_r($versionInfo, true));
		log::add('jdownloader', 'debug', print_r($updateVersion, true));
		log::add('jdownloader', 'debug', print_r($status, true));
        return array(
            "systemInfos" => $systemInfos,
            "uptime" => $uptime,
            "packagesCollector" => $packagesCollector,
            "packagesDownload" => $packagesDownload,
            "linksCollector" => $linksCollector,
            "linksDownload" => $linksDownload,
            "versionInfo" => $versionInfo,
			"updateVersion" => $updateVersion,
			"status" => $status
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
    
    function start() {
        $j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
        $start = $j->start($this->getLogicalId());
        $j->disconnect();
        log::add('jdownloader', 'debug', print_r($start, true));
        return $start;
    }
    
    function stop() {
        $j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
        $stop = $j->stop($this->getLogicalId());
        $j->disconnect();
        log::add('jdownloader', 'debug', print_r($stop, true));
        return $stop;
    }
    
    function pause() {
        $j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
        $pause = $j->pause($this->getLogicalId());
        $j->disconnect();
        log::add('jdownloader', 'debug', print_r($pause, true));
        return $pause;
    }
    
    function restart() {
        $j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
        $restart = $j->restart($this->getLogicalId());
        $j->disconnect();
        log::add('jdownloader', 'debug', print_r($restart, true));
        return $restart;
    }
    
    function setEnablePackage($value) {
        $package = $this->getCmd(null,'package');
        if (is_object($package)) {
            $packageInfosArray = explode("_", $package->execCmd());
            if (count($packageInfosArray) == 2) {
                $j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
                if ($packageInfosArray[1] == 'download') {
                    $enable = $j->setEnableFromDownloads($this->getLogicalId(), array($value, null, array($packageInfosArray[0])));
                }
                else if ($packageInfosArray[1] == 'collector') {
                    $enable = $j->setEnableFromCollector($this->getLogicalId(), array($value, null, array($packageInfosArray[0])));
                }
                $j->disconnect();
                log::add('jdownloader', 'debug', print_r($enable, true));
                return $enable;
            }
        }
        return null;
    }
    
    function forceDownloadPackage() {
        $package = $this->getCmd(null,'package');
        if (is_object($package)) {
            $packageInfosArray = explode("_", $package->execCmd());
            if (count($packageInfosArray) == 2) {
                if ($packageInfosArray[1] == 'download') {
                    $j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
                    $download = $j->forceDownload($this->getLogicalId(), array(null, array($packageInfosArray[0])));
                    $j->disconnect();
                    log::add('jdownloader', 'debug', print_r($download, true));
                    return $download;
                }
            }
        }
        return null;
    }
    
    function moveToDownloadListPackage() {
        $package = $this->getCmd(null,'package');
        if (is_object($package)) {
            $packageInfosArray = explode("_", $package->execCmd());
            if (count($packageInfosArray) == 2) {
                if ($packageInfosArray[1] == 'collector') {
                    $j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
                    $download = $j->moveToDownloadlist($this->getLogicalId(), array(null, array($packageInfosArray[0])));
                    $j->disconnect();
                    log::add('jdownloader', 'debug', print_r($download, true));
                    return $download;
                }
            }
        }
        return null;
    }
    
    function removePackage() {
        $package = $this->getCmd(null,'package');
        if (is_object($package)) {
            $packageInfosArray = explode("_", $package->execCmd());
            if (count($packageInfosArray) == 2) {
                $j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
                if ($packageInfosArray[1] == 'download') {
                    $remove = $j->removeFromDownloads($this->getLogicalId(), array(null, array($packageInfosArray[0])));
                }
                else if ($packageInfosArray[1] == 'collector') {
                    $remove = $j->removeFromCollector($this->getLogicalId(), array(null, array($packageInfosArray[0])));
                }
                $j->disconnect();
                log::add('jdownloader', 'debug', print_r($remove, true));
                return $remove;
            }
        }
        return null;
    }
    
    function setEnableLink($value) {
        $package = $this->getCmd(null,'package');
        if (is_object($package)) {
            $packageInfosArray = explode("_", $package->execCmd());
            if (count($packageInfosArray) == 2) {
                $linkPackage = $this->getCmd(null,'linkPackage');
                if (is_object($linkPackage)) {
                    $j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
                    if ($packageInfosArray[1] == 'download') {
                        $enable = $j->setEnableFromDownloads($this->getLogicalId(), array($value, array($linkPackage->execCmd()), null));
                    }
                    else if ($packageInfosArray[1] == 'collector') {
                        $enable = $j->setEnableFromCollector($this->getLogicalId(), array($value, array($linkPackage->execCmd()), null));
                    }
                    $j->disconnect();
                    log::add('jdownloader', 'debug', print_r($enable, true));
                    return $enable;
                }
            }
        }
        return null;
    }
    
    function forceDownloadLink() {
        $package = $this->getCmd(null,'package');
        if (is_object($package)) {
            $packageInfosArray = explode("_", $package->execCmd());
            if (count($packageInfosArray) == 2) {
                if ($packageInfosArray[1] == 'download') {
                    $linkPackage = $this->getCmd(null,'linkPackage');
                    if (is_object($linkPackage)) {
                        $j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
                        $download = $j->forceDownload($this->getLogicalId(), array(array($linkPackage->execCmd()), null));
                        $j->disconnect();
                        log::add('jdownloader', 'debug', print_r($download, true));
                        return $download;
                    }
                }
            }
        }
        return null;
    }
    
    function moveToDownloadListLink() {
        $package = $this->getCmd(null,'package');
        if (is_object($package)) {
            $packageInfosArray = explode("_", $package->execCmd());
            if (count($packageInfosArray) == 2) {
                if ($packageInfosArray[1] == 'collector') {
                    $linkPackage = $this->getCmd(null,'linkPackage');
                    if (is_object($linkPackage)) {
                        $j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
                        $download = $j->moveToDownloadlist($this->getLogicalId(), array(array($linkPackage->execCmd()), null));
                        $j->disconnect();
                        log::add('jdownloader', 'debug', print_r($download, true));
                        return $download;
                    }
                }
            }
        }
        return null;
    }
    
    function removeLink() {
        $package = $this->getCmd(null,'package');
        if (is_object($package)) {
            $packageInfosArray = explode("_", $package->execCmd());
            if (count($packageInfosArray) == 2) {
                $linkPackage = $this->getCmd(null,'linkPackage');
                if (is_object($linkPackage)) {
                    $j = new MYJDAPI( config::byKey('username', 'jdownloader', ''), config::byKey('password', 'jdownloader', ''));
                    if ($packageInfosArray[1] == 'download') {
                        $remove = $j->removeFromDownloads($this->getLogicalId(), array(array($linkPackage->execCmd()), null));
                    }
                    else if ($packageInfosArray[1] == 'collector') {
                        $remove = $j->removeFromCollector($this->getLogicalId(), array(array($linkPackage->execCmd()), null));
                    }
                    $j->disconnect();
                    log::add('jdownloader', 'debug', print_r($remove, true));
                    return $remove;
                }
            }
        }
        return null;
    }
    
    function updatePackageCmd($packageDatas, $cmdId, $visible = null) {
        $cmd = $this->getCmd(null, $cmdId . 'Package');
        if (is_object($cmd)) {
            if ($visible !== null) {
                $cmd->setIsVisible($visible);
            }
            else if (array_key_exists($cmdId, $packageDatas)) {
                $cmd->setIsVisible(1);
            }
            else {
                $cmd->setIsVisible(0);
            }
            $cmd->save();
            if ($cmd->getSubType() === 'binary') {
                if (array_key_exists($cmdId, $packageDatas)) {
                    if ($cmd->formatValue($packageDatas[$cmdId]) != $cmd->execCmd()) {
                        $cmd->setCollectDate('');
                        $cmd->event($packageDatas[$cmdId]);
                    }
                }
                else if ($cmd->formatValue(0) != $cmd->execCmd()) {
                    $cmd->setCollectDate('');
                    $cmd->event(0);
                }
            }
            else if (array_key_exists($cmdId, $packageDatas)) {
                if ($cmd->formatValue($packageDatas[$cmdId]) != $cmd->execCmd()) {
                    $cmd->setCollectDate('');
                    $cmd->event($packageDatas[$cmdId]);
                }
            }
        }
    }
    
    function updateLinkCmd($linkDatas, $cmdId, $visible = null) {
        $cmd = $this->getCmd(null, $cmdId . 'Link');
        if (is_object($cmd)) {
            if ($visible !== null) {
                $cmd->setIsVisible($visible);
            }
            else if (array_key_exists($cmdId, $linkDatas)) {
                $cmd->setIsVisible(1);
            }
            else {
                $cmd->setIsVisible(0);
            }
            $cmd->save();
            if ($cmd->getSubType() === 'binary') {
                if (array_key_exists($cmdId, $linkDatas)) {
                    if ($cmd->formatValue($linkDatas[$cmdId]) != $cmd->execCmd()) {
                        $cmd->setCollectDate('');
                        $cmd->event($linkDatas[$cmdId]);
                    }
                }
                else if ($cmd->formatValue(0) != $cmd->execCmd()) {
                    $cmd->setCollectDate('');
                    $cmd->event(0);
                }
            }
            else if (array_key_exists($cmdId, $linkDatas)) {
                if ($cmd->formatValue($linkDatas[$cmdId]) != $cmd->execCmd()) {
                    $cmd->setCollectDate('');
                    $cmd->event($linkDatas[$cmdId]);
                }
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
		$updateVersion = $this->getCmd(null, "updateVersion");
        if (is_object($updateVersion)) {
			if ($JdownloaderDatas['updateVersion']['data'] == true) {
				$updateVersion->setIsVisible(1);
            }
			else {
				$updateVersion->setIsVisible(0);
			}
			$updateVersion->save();
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
			$newSpeed = round(($JdownloaderDatas['status']['data']['speed']/1000), 2);
            if ($totalSpeed->formatValue($newSpeed) != $totalSpeed->execCmd()) {
                $totalSpeed->setCollectDate('');
                $totalSpeed->event($newSpeed);
            }
        }
		$state = $this->getCmd(null, "state");
        if (is_object($state)) {
            if ($state->formatValue($JdownloaderDatas['status']['data']['state']) != $state->execCmd()) {
                $state->setCollectDate('');
                $state->event($JdownloaderDatas['status']['data']['state']);
            }
        }
        $packageList = $this->getCmd(null,'packageList');
        if (is_object($packageList)) {
            if (count($JdownloaderDatas['packagesCollector']['data']) > 0 || count($JdownloaderDatas['packagesDownload']['data']) > 0) {
                $packageList->setIsVisible(1);
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
            }
            else {
                $packageList->setIsVisible(0);
            }
            $packageList->save();
        }
    }
    
    function updatePackageCmds($packageDatas, $linksDatas) {
        $this->updatePackageCmd($packageDatas, 'enabled');
        $enabledPackage = $this->getCmd(null,'enabledPackage');
        if (is_object($enabledPackage)) {
            $enabled = $enabledPackage->execCmd();
            $enablePackage = $this->getCmd(null,'enablePackage');
            if (is_object($enablePackage)) {
                if ($enabled || !array_key_exists('enabled', $packageDatas)) {
                    $enablePackage->setIsVisible(0);
                }
                else {
                    $enablePackage->setIsVisible(1);
                }
                $enablePackage->save();
            }
            $disablePackage = $this->getCmd(null,'disablePackage');
            if (is_object($disablePackage)) {
                if ($enabled) {
                    $disablePackage->setIsVisible(1);
                }
                else {
                    $disablePackage->setIsVisible(0);
                }
                $disablePackage->save();
            }
        }
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
        $package = $this->getCmd(null,'package');
        if (is_object($package)) {
            $packageInfosArray = explode("_", $package->execCmd());
            $forceDownloadPackage = $this->getCmd(null,'forceDownloadPackage');
            if (is_object($forceDownloadPackage)) {
                if (count($packageInfosArray) == 2) {
                    $runningPackage = $this->getCmd(null,'runningPackage');
                    if (is_object($runningPackage)) {
                        if (($runningPackage->execCmd() != 1) && ($packageInfosArray[1] == 'download')) {
                            $forceDownloadPackage->setIsVisible(1);
                        }
                        else {
                            $forceDownloadPackage->setIsVisible(0);
                        }
                    }
                }
                else {
                    $forceDownloadPackage->setIsVisible(0);
                }
                $forceDownloadPackage->save();
            }
            $moveToDownloadListPackage = $this->getCmd(null,'moveToDownloadListPackage');
            if (is_object($moveToDownloadListPackage)) {
                if (count($packageInfosArray) == 2) {
                    if ($packageInfosArray[1] == 'download') {
                        $moveToDownloadListPackage->setIsVisible(0);
                    }
                    else if ($packageInfosArray[1] == 'collector') {
                        $moveToDownloadListPackage->setIsVisible(1);
                    }
                }
                else {
                    $moveToDownloadListPackage->setIsVisible(0);
                }
                $moveToDownloadListPackage->save();
            }
            $removePackage = $this->getCmd(null,'removePackage');
            if (is_object($removePackage)) {
                if (count($packageInfosArray) != 2) {
                    $removePackage->setIsVisible(0);
                }
                else {
                    $removePackage->setIsVisible(1);
                }
                $removePackage->save();
            }
        }
    }
    
    function updateLinksCmds($linkDatas) {
        $this->updateLinkCmd($linkDatas, 'enabled');
        $enabledLink = $this->getCmd(null,'enabledLink');
        if (is_object($enabledLink)) {
            $enabled = $enabledLink->execCmd();
            $enableLink = $this->getCmd(null,'enableLink');
            if (is_object($enableLink)) {
                if ($enabled || !array_key_exists('enabled', $linkDatas)) {
                    $enableLink->setIsVisible(0);
                }
                else {
                    $enableLink->setIsVisible(1);
                }
                $enableLink->save();
            }
            $disableLink = $this->getCmd(null,'disableLink');
            if (is_object($disableLink)) {
                if ($enabled) {
                    $disableLink->setIsVisible(1);
                }
                else {
                    $disableLink->setIsVisible(0);
                }
                $disableLink->save();
            }
        }
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
        $package = $this->getCmd(null,'package');
        if (is_object($package)) {
            $packageInfosArray = explode("_", $package->execCmd());
            $forceDownloadLink = $this->getCmd(null,'forceDownloadLink');
            if (is_object($forceDownloadLink)) {
                if (count($packageInfosArray) == 2) {
                    $runningLink = $this->getCmd(null,'runningLink');
                    if (is_object($runningLink)) {
                        if (($runningLink->execCmd() != 1) && ($packageInfosArray[1] == 'download')) {
                            $forceDownloadLink->setIsVisible(1);
                        }
                        else {
                            $forceDownloadLink->setIsVisible(0);
                        }
                    }
                }
                else {
                    $forceDownloadLink->setIsVisible(0);
                }
                $forceDownloadLink->save();
            }
            $moveToDownloadListLink = $this->getCmd(null,'moveToDownloadListLink');
            if (is_object($moveToDownloadListLink)) {
                if (count($packageInfosArray) == 2) {
                    if ($packageInfosArray[1] == 'download') {
                        $moveToDownloadListLink->setIsVisible(0);
                    }
                    else if ($packageInfosArray[1] == 'collector') {
                        $moveToDownloadListLink->setIsVisible(1);
                    }
                }
                else {
                    $moveToDownloadListLink->setIsVisible(0);
                }
                $moveToDownloadListLink->save();
            }
            $removeLink = $this->getCmd(null,'removeLink');
            if (is_object($removeLink)) {
                if (count($packageInfosArray) != 2) {
                    $removeLink->setIsVisible(0);
                }
                else {
                    $removeLink->setIsVisible(1);
                }
                $removeLink->save();
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
            case "start":
                $eqLogic->start();
                $eqLogic->updateDeviceInfos();
                return true;
            case "stop":
                $eqLogic->stop();
                $eqLogic->updateDeviceInfos();
                return true;
            case "pause":
                $eqLogic->pause();
                $eqLogic->updateDeviceInfos();
                return true;
            case "restart":
                $eqLogic->restart();
                return true;
            case "enablePackage":
                $eqLogic->setEnablePackage(true);
                $eqLogic->updateDeviceInfos();
                return true;
            case "disablePackage":
                $eqLogic->setEnablePackage(false);
                $eqLogic->updateDeviceInfos();
                return true;
            case "forceDownloadPackage":
                $eqLogic->forceDownloadPackage();
                $eqLogic->updateDeviceInfos();
                return true;
            case "moveToDownloadListPackage":
                $eqLogic->moveToDownloadListPackage();
                $eqLogic->updateDeviceInfos();
                return true;
            case "removePackage":
                $eqLogic->removePackage();
                $eqLogic->updateDeviceInfos();
                return true;
            case "enableLink":
                $eqLogic->setEnableLink(true);
                $eqLogic->updateDeviceInfos();
                return true;
            case "disableLink":
                $eqLogic->setEnableLink(false);
                $eqLogic->updateDeviceInfos();
                return true;
            case "forceDownloadLink":
                $eqLogic->forceDownloadLink();
                $eqLogic->updateDeviceInfos();
                return true;
            case "moveToDownloadListLink":
                $eqLogic->moveToDownloadListLink();
                $eqLogic->updateDeviceInfos();
                return true;
            case "removeLink":
                $eqLogic->removeLink();
                $eqLogic->updateDeviceInfos();
                return true;
            default:
                return false;
        }
     }

    /*     * **********************Getteur Setteur*************************** */
}


