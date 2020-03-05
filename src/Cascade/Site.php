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
     * get all sites in cascade server
     *
     * @return array of all sites
     */
    public function getSites()
    {
        $params = ['authentication' => $this->auth];
        return $this->client->listSites($params)->listSitesReturn->sites->assetIdentifier;
    }

    /**
     * Read in site
     *
     * @param string $siteOrId
     * @param string $path
     * @return bool|void
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
}
