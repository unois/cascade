<?php
namespace Cascade;

use Exception;

class Page extends Base
{
    public $asset;

    /**
     * copy page from sitename and path to destination folder
     *
     * @param  string $siteName
     * @param  string $path
     * @param  string $destinationContainerSiteName
     * @param  string $destinationContainerPath
     * @param  string $newName
     * @return boolean
     */
    public function copy($siteName, $path, $destinationContainerSiteName, $destinationContainerPath, $newName)
    {
        return $this->copyBase(parent::PAGE, parent::FOLDER, $siteName, $path, $destinationContainerSiteName, $destinationContainerPath, $newName);
    }

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
        if ($this->api_type == 'soap') {
            foreach ($this->asset->metadata->dynamicFields->dynamicField as $key => $obj) {
                if ($obj->name == $name) {
                    return $obj;
                }
            }
        } elseif ($this->api_type == 'rest') {
            foreach ($this->asset->metadata->dynamicFields as $key => $obj) {
                if ($obj->name == $name) {
                    return $obj;
                }
            }
        }
        return false;
    }

    /**
     * Helper to get value from dynamic metadata field
     * @param string $node
     * @return string
     */
    public function getDynamicMetatadataFieldValue($name)
    {
        if ($this->api_type == 'soap') {
            return isset($this->getDynamicMetatadataField($name)->fieldValues->fieldValue->value) ? $this->getDynamicMetatadataField($name)->fieldValues->fieldValue->value : '';
        } elseif ($this->api_type == 'rest') {
            return isset($this->getDynamicMetatadataField($name)->fieldValues[0]->value) ? $this->getDynamicMetatadataField($name)->fieldValues[0]->value : '';
        }
    }
    
    /**
     * Helper to get attribute from structured data node
     * @param string $node
     * @return mixed|false
     */
    public function getStructuredDataNode($node)
    {
        if ($this->api_type == 'soap') {
            foreach ($this->asset->structuredData->structuredDataNodes->structuredDataNode as $key => $obj) {
                if ($obj->identifier == $node) {
                    return $obj;
                }
            }
        } elseif ($this->api_type == 'rest') {
            foreach ($this->asset->structuredData->structuredDataNodes as $key => $obj) {
                if ($obj->identifier == $node) {
                    return $obj;
                }
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
            $this->asset = $this->readBase(parent::PAGE, $siteName, $path, $id);
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
        if ($this->api_type == 'soap') {
            foreach ($this->asset->metadata->dynamicFields->dynamicField as $key => $obj) {
                if ($obj->name == $name) {
                    $this->asset->metadata->dynamicFields->dynamicField[ $key ]->fieldValues->fieldValue->value = $value;
                    return true;
                }
            }
        } elseif ($this->api_type == 'rest') {
            foreach ($this->asset->metadata->dynamicFields as $key => $obj) {
                if ($obj->name == $name) {
                    $this->asset->metadata->dynamicFields[ $key ]->fieldValues[0]->value = $value;
                    return true;
                }
            }
        }
        return false;
    }
}
