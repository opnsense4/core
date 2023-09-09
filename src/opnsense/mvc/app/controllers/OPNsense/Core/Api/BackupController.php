<?php

/*
 * Copyright (C) 2023 Deciso B.V.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 * AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace OPNsense\Core\Api;

use OPNsense\Base\ApiControllerBase;
use OPNsense\Core\Backend;
use OPNsense\Core\Config;

/**
 * Class BackupController
 * @package OPNsense\Core\Api
 */
class BackupController extends ApiControllerBase
{
    /**
     * return available providers and their backup locations
     * @return array
     */
    private function providers()
    {
        $result = [];
        $result['this'] = ['description' => gettext('This Firewall'), 'dirname' => '/conf/backup'];
        if (class_exists('\Deciso\OPNcentral\Central')) {
            $central = new \Deciso\OPNcentral\Central();
            $central->setUserScope($this->getUserName());
            $ctrHosts = [];
            foreach ($central->hosts->host->getNodes() as $itemid => $item) {
                $ctrHosts[$itemid] = ['description' => $item['description']];
            }
            foreach (glob('/conf/remote.backups/*') as $filename) {
                $dirname = basename($filename);
                if (isset($ctrHosts[$dirname])) {
                    $result[$dirname] = $ctrHosts[$dirname];
                    $result[$dirname]['dirname'] = $filename;
                }
            }
        }
        return $result;
    }

    /**
     * list available providers
     * @return array
     */
    public function providersAction()
    {
        return ['items' => $this->providers()];
    }

    /**
     * list available backups for selected host
     */
    public function backupsAction($host)
    {
        $result = ['items' => []];
        $providers = $this->providers();
        if (!empty($providers[$host])) {
            foreach (glob($providers[$host] . "/config-*.xml") as $filename) {
                $xmlNode = @simplexml_load_file($filename, "SimpleXMLElement", LIBXML_NOERROR | LIBXML_ERR_NONE);
                if (isset($xmlNode->revision)) {
                    $cfg_item = [
                        'time' => (string)$xmlNode->revision->time,
                        'description' => (string)$xmlNode->revision->description,
                        'username' => (string)$xmlNode->revision->username,
                        'filesize' => filesize($filename)
                    ] ;
                    $result['items'][] = $cfg_item;
                }
            }
        }
        return $result;
    }
}
