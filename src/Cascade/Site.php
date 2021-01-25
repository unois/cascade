<?php

namespace Cascade;

use Exception;

class Site extends Base
{
    public $asset;

    /**
     * output all attributes of read site
     *
     * @return void
     */
    public function dump()
    {
        var_dump($this->asset);
    }

    /**
     * edit site
     *
     * @return boolean
     */
    public function edit()
    {
        return $this->editBase(parent::SITE, $this->asset);
    }
    
    /**
     * get all sites in cascade server
     *
     * @return array of all sites
     */
    public function getSites()
    {
        if ($this->api_type == 'soap') {
            $params = ['authentication' => $this->auth];
            return $this->client->listSites($params)->listSitesReturn->sites->assetIdentifier;
        } elseif ($this->api_type == 'rest') {
            return json_decode($this->client->request('GET', 'listSites')->getBody()->getContents())->sites;
        }
    }

    /**
     * Read in site
     *
     * @param string $siteOrId
     * @param string $path
     * @return object
     * @throws Exception
     */
    public function read($name, $id = '')
    {
        try {
            $this->asset = $this->readBase(parent::SITE, '', $name, $id);
            return $this->asset;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
    
    /**
     * Rename site
     *
     * @param string $newName
     * @param string $siteOrId
     * @param string $path
     * @return bool|void
     * @throws Exception
     */
    public function rename($newName)
    {
        try {
            $this->asset = $this->renameBase(parent::SITE, $newName, '', '', $this->asset->id);
            return $this->asset;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
