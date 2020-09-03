<?php
namespace Cascade;

use Exception;

class ContentType extends Base
{
    public $asset;

    /**
     * edit content type
     *
     * @return boolean
     */
    public function edit()
    {
        return $this->editBase(parent::CONTENT_TYPE, $this->asset);
    }

    /**
     * Return all subscribers of current content type
     * @return object
     * @throws Exception
     */
    public function listSubscribers()
    {
        return $this->listSubscribersBase(parent::CONTENT_TYPE, '', '', $this->asset->id);
    }

    /**
     * Read in content type
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
            $this->asset = $this->readBase(parent::CONTENT_TYPE, $siteName, $path, $id);
            return $this->asset;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
