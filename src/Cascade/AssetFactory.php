<?php
namespace Cascade;

use Exception;

class AssetFactory extends Base
{
    public $asset;

    /**
     * output all attributes of read asset factory
     *
     * @return void
     */
    public function dump()
    {
        var_dump($this->asset);
    }

    /**
     * edit asset factory
     *
     * @return boolean
     */
    public function edit()
    {
        return $this->editBase(parent::ASSET_FACTORY, $this->asset);
    }

    /**
     * Read in asset factory
     *
     * @param string $siteOrId
     * @param string $path
     * @return bool|void
     * @throws Exception
     */
    public function read($siteOrId, $path)
    {
        if ($path == '') {
            $id = $siteOrId;
            $siteName = $path = '';
        } else {
            $id = '';
            $siteName = $siteOrId;
        }
        try {
            $this->asset = $this->readBase(parent::ASSET_FACTORY, $siteName, $path, $id);
            return $this->asset;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
