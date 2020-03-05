<?php
namespace Cascade;

use Exception;

class AssetFactoryContainer extends Base
{
    public $asset;

    /**
     * output all attributes of read asset factory container
     *
     * @return void
     */
    public function dump()
    {
        var_dump($this->asset);
    }

    /**
     * Read in asset factory container
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
            $this->asset = $this->readBase(parent::ASSET_FACTORY_CONTAINER, $siteName, $path, $id);
            return $this->asset;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}