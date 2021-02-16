<?php
namespace Cascade;

use Exception;

class MetadataSet extends Base
{
    public $asset;

    /**
     * Read in metadata set
     *
     * @param string $siteOrId
     * @param string $path
     * @return object
     * @throws Exception
     */
    public function read($siteOrId, $path = '')
    {
        if ($path == '') {
            $id = $siteOrId;
            $siteName = $path = '';
        } else {
            $id = '';
            $siteName = $siteOrId;
        }
        try {
            $this->asset = $this->readBase(parent::METADATA_SET, $siteName, $path, $id);
            return $this->asset;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
