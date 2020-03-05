<?php
namespace Cascade;

use Exception;
use Dotenv\Dotenv;

class Base
{
    protected $client;
    protected $auth;

    public const ASSET_FACTORY_CONTAINER          = "assetfactorycontainer";
    public const ASSET_FACTORY                    = "assetfactory";
    public const DESTINATION                      = "destination";
    public const FILE                             = "file";
    public const FOLDER                           = "folder";
    public const PAGE                             = "page";
    public const PUBLISH_SET                      = "publishset";
    public const PUBLISH_SET_CONTAINER            = "publishsetcontainer";
    public const SITE                             = "site";
    public const USER                             = "user";

    public function __construct($configuration = null)
    {
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        if ($configuration === null) {
            $base_uri = getenv('CASCADE_BASE_URI');
            $username = getenv('CASCADE_USERNAME');
            $password = getenv('CASCADE_PASSWORD');
        } else {
            $base_uri = $configuration['base_uri'];
            $username = $configuration['username'];
            $password = $configuration['password'];
        }
        $soapURL = $base_uri."ws/services/AssetOperationService?wsdl";
        $this->client = new \SoapClient(
            $soapURL,
            ['trace' => 1, 'location' => str_replace('?wsdl', '', $soapURL)]
        );
        $this->auth = ['username' => $username, 'password' => $password ];
    }

    /**
     * edit
     *
     * @param  string       $type         Type defined in wsdl
     * @param  array|obj    $data
     *
     * @return boolean
     */
    protected function editBase($type, $data)
    {
        $params = ['authentication' => $this->auth,
            'asset' => [
                $type =>  $data,
            ]
        ];

        $this->lastResponse = $this->client->edit($params)->editReturn;

        return $this->isLastResponseSuccess();
    }

    private function checkAsset()
    {
        if (!is_object($this->asset)) {
            throw new Exception('You must read the applicable asset first');
        }
    }
    public function getId()
    {
        $this->checkAsset();
        return $this->asset->id;
    }
    public function getName()
    {
        $this->checkAsset();
        return $this->asset->name;
    }
    public function getParentFolderId()
    {
        $this->checkAsset();
        return $this->asset->parentFolderId;
    }
    public function getParentFolderPath()
    {
        $this->checkAsset();
        return $this->asset->parentFolderPath;
    }
    public function getPath()
    {
        $this->checkAsset();
        return $this->asset->path;
    }
    public function getLastModifiedDate()
    {
        $this->checkAsset();
        return $this->asset->lastModifiedDate;
    }
    public function getLastModifiedBy()
    {
        $this->checkAsset();
        return $this->asset->lastModifiedBy;
    }
    public function getCreatedDate()
    {
        $this->checkAsset();
        return $this->asset->createdDate;
    }
    public function getCreatedBy()
    {
        $this->checkAsset();
        return $this->asset->createdBy;
    }
    public function getSiteId()
    {
        $this->checkAsset();
        return $this->asset->siteId;
    }
    public function getSiteName()
    {
        $this->checkAsset();
        return $this->asset->siteName;
    }
    
    /**
     * determine if the lastResponse is a success
     *
     * @return boolean
     */
    private function isLastResponseSuccess()
    {
        return ($this->lastResponse->success == 'true');
    }

    /**
     * read any type of object into lastResponse variable
     *
     * @param  string $type         Type defined in wsdl
     * @param  string $path         Path in Cascade Server
     * @param  string $siteName     Site Name in Cascade Server
     * @param  string $id           ID of asset in Cascade Server
     *
     * @return object
     * @throws exception
     */
    protected function listSubscribersBase($type, $path = '', $siteName = '', $id = '')
    {
        $params = ['authentication' => $this->auth, 'identifier' => ['type' => $type, 'id' => $id, 'path' => ['path' => $path, 'siteName' => $siteName]]];

        $this->lastResponse = $this->client->listSubscribers($params)->listSubscribersReturn;

        if ($this->isLastResponseSuccess()) {
            if (isset($this->lastResponse->subscribers->assetIdentifier)) {
                return $this->lastResponse->subscribers->assetIdentifier;
            } else {
                return [];
            }
        } else {
            throw new \Exception(($this->lastResponse->message));
        }
    }

    /**
     * publish or unpublish an asset within Cascade Server
     *
     * @param  string $type         Type defined in wsdl
     * @param  string $path         Path in Cascade Server
     * @param  string $siteName     Site Name in Cascade Server
     * @param  string $id           ID of asset in Cascade Server
     * @param  boolean $unpublish   Unpublish or publish the asset
     *
     * @return boolean
     */
    protected function publishBase($type, $path = '', $siteName = '', $id = '', $unpublish = false)
    {
        $params = ['authentication' => $this->auth,
            'publishInformation' => [
                'identifier' => ['type' => $type, 'id' => $id, 'path' => ['path' => $path, 'siteName' => $siteName]],
                'unpublish' => $unpublish
            ]
        ];

        $this->lastResponse = $this->client->publish($params)->publishReturn;

        return $this->isLastResponseSuccess();
    }

    /**
     * read any type of object into lastResponse variable
     *
     * @param  string $type         Type defined in wsdl
     * @param  string $path         Path in Cascade Server
     * @param  string $siteName     Site Name in Cascade Server
     * @param  string $id           ID of asset in Cascade Server
     *
     * @link https://www.hannonhill.com/cascadecms/latest/developing-in-cascade/soap-web-services-api/soap-web-services-operations.html#ReadOperation
     *
     * @return boolean
     */
    protected function readBase($type, $path = '', $siteName = '', $id = '')
    {
        $params = ['authentication' => $this->auth, 'identifier' => ['type' => $type, 'id' => $id, 'path' => ['path' => $path, 'siteName' => $siteName]]];

        $this->lastResponse = $this->client->read($params)->readReturn;

        if ($this->isLastResponseSuccess($this->lastResponse)) {
            return $this->lastResponse->asset->{$type};
        } else {
            throw new \Exception(($this->lastResponse->message));
        }
    }
}
