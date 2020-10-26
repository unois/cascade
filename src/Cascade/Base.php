<?php
namespace Cascade;

use Exception;
use Dotenv\Dotenv;
use stdClass;

class Base
{
    protected $client;
    protected $auth;
    protected $api_type;

    public const ASSET_FACTORY_CONTAINER          = "assetfactorycontainer";
    public const ASSET_FACTORY                    = "assetfactory";
    public const CONTENT_TYPE                     = "contenttype";
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
        if ($configuration === null) {
            $this->loadDotEnv(__DIR__.'/../../');
            $base_uri = getenv('CASCADE_BASE_URI');
            $username = getenv('CASCADE_USERNAME');
            $password = getenv('CASCADE_PASSWORD');
            $this->api_type = null !== getenv('API_TYPE') ? getenv('API_TYPE') : 'soap';
        } else {
            $base_uri = $configuration['base_uri'];
            $username = $configuration['username'];
            $password = $configuration['password'];
            $this->api_type = isset($configuration['api_type']) ? $configuration['api_type'] : 'soap';
        }
        if ($this->api_type == 'soap') {
            //For SOAP
            $soapURL = $base_uri."ws/services/AssetOperationService?wsdl";
            $this->client = new \SoapClient(
                $soapURL,
                ['trace' => 1, 'location' => str_replace('?wsdl', '', $soapURL)]
            );
            $this->auth = ['username' => $username, 'password' => $password ];
        } elseif ($this->api_type == 'rest') {
            //For Rest
            $this->client = new \GuzzleHttp\Client([
                'base_uri' => $base_uri.'api/v1/',
                'auth' => [$configuration['username'], $configuration['password']],
            ]);
        } else {
            die("Please define either 'soap' or 'rest' as the api type *{$this->api_type}*\n");
        }
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
        if ($this->api_type == 'soap') {
            $params = ['authentication' => $this->auth,
                'asset' => [
                    $type =>  $data,
                ]
            ];

            $this->lastResponse = $this->client->edit($params)->editReturn;

            return $this->isLastResponseSuccess();
        } elseif ($this->api_type == 'rest') {
            $t = new \stdClass();
            $t->asset = new \stdClass();
            $t->asset->{$type} = $data;
            $response = $this->client->request('POST', 'edit', ['body' => json_encode($t)]);
            $this->lastResponse = json_decode($response->getBody()->getContents());

            if ($this->lastResponse->success) {
                return $this->lastResponse->asset->{$type};
            } else {
                throw new \Exception(($this->lastResponse->message));
            }
        }
    }

    /**
     * load dotenv.
     *
     * @param $path
     *
     * @throws Exception
     */
    private function loadDotEnv($path)
    {
        $requireParam = [
            'CASCADE_BASE_URI', 'CASCADE_USERNAME', 'CASCADE_PASSWORD',
        ];

        // support for dotenv 1.x and 2.x.
        if (class_exists('\Dotenv\Dotenv')) {
            if (method_exists('\Dotenv\Dotenv', 'createImmutable')) {    // v4
                $dotenv = \Dotenv\Dotenv::createImmutable($path);

                $dotenv->safeLoad();
                $dotenv->required($requireParam);
            } elseif (method_exists('\Dotenv\Dotenv', 'create')) {    // v3
                $dotenv = \Dotenv\Dotenv::create($path);
 
                $dotenv->safeLoad();
                $dotenv->required($requireParam);
            } else {    // v2
                $dotenv = new \Dotenv\Dotenv($path);

                $dotenv->load();
                $dotenv->required($requireParam);
            }
        } elseif (class_exists('\Dotenv')) {    // DotEnv v1
            \Dotenv::load($path);
            \Dotenv::required($requireParam);
        } else {
            throw new Exception('can not load PHP dotenv class.!');
        }
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
    protected function listSubscribersBase($type, $siteName = '', $path = '', $id = '')
    {
        if ($this->api_type == 'soap') {
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
        } elseif ($this->api_type == 'rest') {
            $method = 'listSubscribers/'.$type.'/'.$this->generateRestfulUri($siteName, $path, $id);

            $response = $this->client->request('GET', $method);
            $this->lastResponse = json_decode($response->getBody()->getContents());
            if ($this->lastResponse->success) {
                return $this->lastResponse->subscribers;
            } else {
                throw new \Exception(($this->lastResponse->message));
            }
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
    protected function publishBase($type, $siteName = '', $path = '', $id = '', $unpublish = false)
    {
        if ($this->api_type == 'soap') {
            $params = ['authentication' => $this->auth,
                'publishInformation' => [
                    'identifier' => ['type' => $type, 'id' => $id, 'path' => ['path' => $path, 'siteName' => $siteName]],
                    'unpublish' => $unpublish
                ]
            ];
            $this->lastResponse = $this->client->publish($params)->publishReturn;
            return $this->isLastResponseSuccess();
        } elseif ($this->api_type == 'rest') {
            $method = 'publish/'.$type.'/'.$this->generateRestfulUri($siteName, $path, $id);
            $response = $this->client->request('GET', $method);
            $this->lastResponse = json_decode($response->getBody()->getContents());

            if ($this->lastResponse->success) {
                return true;
            } else {
                throw new \Exception(($this->lastResponse->message));
            }
        }
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
     * @return object
     */
    protected function readBase($type, $siteName = '', $path = '', $id = '')
    {
        if ($this->api_type == 'soap') {
            $params = ['authentication' => $this->auth, 'identifier' => ['type' => $type, 'id' => $id, 'path' => ['path' => $path, 'siteName' => $siteName]]];

            $this->lastResponse = $this->client->read($params)->readReturn;
    
            if ($this->isLastResponseSuccess($this->lastResponse)) {
                $type = $this->translateReadResponseType($type);
                return $this->lastResponse->asset->{$type};
            } else {
                throw new \Exception(($this->lastResponse->message));
            }
        } elseif ($this->api_type == 'rest') {
            $method = 'read/'.$type.'/'.$this->generateRestfulUri($siteName, $path, $id);
            $response = $this->client->request('GET', $method);
            $this->lastResponse = json_decode($response->getBody()->getContents());

            if ($this->lastResponse->success) {
                return $this->lastResponse->asset->{$type};
            } else {
                throw new \Exception(($this->lastResponse->message));
            }
        }
    }

    /**
     * Helper function for restful uri's
     * @param mixed $siteName
     * @param mixed $path
     * @param mixed $id
     * @return string
     */
    private function generateRestfulUri($siteName, $path, $id)
    {
        $uri='';
        if ($id <> '') {
            $uri.=$id;
        } else {
            $uri.=$siteName.'/'.$path;
        }
        return $uri;
    }

    private function translateReadResponseType($type)
    {
        switch ($type) {
            case self::ASSET_FACTORY_CONTAINER: $type = 'assetFactoryContainer'; break;
            case self::ASSET_FACTORY: $type = 'assetFactory'; break;
            case self::CONTENT_TYPE: $type = 'contentType'; break;
        }
        return $type;
    }
}
