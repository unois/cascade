<?php
namespace Cascade;

use Exception;

class File extends Base
{
    public $asset;

    /**
     * createFile
     *
     * @param  boolean $update      Update file if it exists or create a new file
     * @param  string $localFile    Full path and name to local file
     * @param  string $siteName     Site Name in Cascade Server
     * @param  string $path         Path in Cascade Server
     *
     * @return boolean
     */
    public function create($update, $localFile, $siteName, $path)
    {
        $exists     = $this->readBase(self::FILE, $siteName, $path);

        $pathInfo   = pathinfo($path);
        $name       = $pathInfo['basename'];
        $parentFolderPath = $pathInfo['dirname'];
        
        if ($update && $exists) {
            $file = [
                'name' => $name,
                'id' => $this->lastResponse->asset->file->id,
                'parentFolderId' => $this->lastResponse->asset->file->parentFolderId,
                'siteId' => $this->lastResponse->asset->file->siteId,
                'data' => file_get_contents($localFile),
            ];
            
            $params = ['authentication' => $this->auth, 'asset' => ['file' => $file]];
            $this->lastResponse = $this->client->edit($params)->editReturn;
        } else {
            $file = [
                'name' => $name,
                'data' => file_get_contents($localFile),
                'parentFolderPath' => $parentFolderPath,
                'siteName' => $siteName
            ];
            
            $params = ['authentication' => $this->auth, 'asset' => ['file' => $file]];
            $this->lastResponse = $this->client->create($params)->createReturn;
        }
        
        $retVal = $this->isLastResponseSuccess();
        if ($retVal) {
            //Get attributes in last response variable
            $this->read(self::FILE, $path, $siteName);
        }
        return $retVal;
    }

    /**
     * output all attributes of file
     *
     * @return void
     */
    public function dump()
    {
        var_dump($this->asset);
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
            $this->asset = $this->readBase(parent::FILE, $siteName, $path, $id);
            return $this->asset;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
