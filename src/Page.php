<?php
namespace Cascade;

use Exception;

class Page extends Base
{
    public $asset;

    /**
     * edit page
     *
     * @return boolean
     */
    public function edit()
    {
        return $this->editBase(parent::PAGE, $this->asset);
    }

    /**
     * Helper to get attribute from dynamic metadata field
     * @param string $node
     * @return mixed|false
     */
    public function getDynamicMetatadataField($name)
    {
        foreach ($this->asset->metadata->dynamicFields->dynamicField as $key => $obj) {
            if ($obj->name == $name) {
                return $obj;
            }
        }
        return false;
    }

    /**
     * Helper to get attribute from structured data node
     * @param string $node
     * @return mixed|false
     */
    public function getStructuredDataNode($node)
    {
        foreach ($this->asset->structuredData->structuredDataNodes->structuredDataNode as $key => $obj) {
            if ($obj->identifier == $node) {
                return $obj;
            }
        }
        return false;
    }

    /**
     * Return all subscribers of current page
     * @return object
     * @throws Exception
     */
    public function listSubscribers()
    {
        return $this->listSubscribersBase(parent::PAGE, '', '', $this->asset->id);
    }

    /**
     * Publish selected asset
     * @return bool
     */
    public function publish()
    {
        return $this->publishBase($this::PAGE, '', '', $this->asset->id);
    }

    /**
     * Read in page
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
            $this->asset = $this->readBase(parent::PAGE, $path, $siteName, $id);
            return $this->asset;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * Set dynamic metadata field
     *
     * @param mixed $name
     * @param mixed $value
     * @return bool
     */
    public function setDynamicMetatadataFieldValue($name, $value)
    {
        foreach ($this->asset->metadata->dynamicFields->dynamicField as $key => $obj) {
            if ($obj->name == $name) {
                $this->asset->metadata->dynamicFields->dynamicField[ $key ]->fieldValues->fieldValue->value = $value;
                return true;
            }
        }
        return false;
    }
}
