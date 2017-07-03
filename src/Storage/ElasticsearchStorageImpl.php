<?php
/**
 * Created by IntelliJ IDEA.
 * User: petr
 * Date: 29.5.17
 * Time: 16:21
 */

namespace Sofico\Webdriver\Storage;


use Elasticsearch\ClientBuilder;
use Exception;
use Sofico\Webdriver\BasicConfig;

class ElasticsearchStorageImpl implements Storage
{
    const INDEX_NAME = 'automated_tests';
    const TYPE_NAME = 'result';

    protected $client;

    public function __construct(BasicConfig $config)
    {
        try {
            $username = $config->getProperty($config::ELK_USERNAME);
            $password = $config->getProperty($config::ELK_PASSWORD);
            $hosts = [
                "http://$username:$password@localhost:9200",
            ];
            $this->client = ClientBuilder::create()->setHosts($hosts)->build();;
        } catch (Exception $e) {
            $this->client = ClientBuilder::create()->build();;
        }
    }

    public function store(Result $result)
    {
        if (!$this->indexExists()) $this->createIndexWithMapping();
        $params = [
            'index' => self::INDEX_NAME,
            'type' => self::TYPE_NAME,
            'body' => [
                'project' => $result->getProjectName(),
                'environment' => $result->getEnvironment(),
                'testname' => $result->getTestname(),
                'severity' => $result->getSeverity(),
                'browser' => $result->getBrowser(),
                'started' => $result->getStarted(),
                'ended' => $result->getEnded(),
                'status' => $result->getStatus(),
                'error' => $result->getError(),
                'attachments' => [
                    'log' => $result->getLogPath(),
                    'screen' => $result->getScreenPath(),
                ],
            ]
        ];
        return $this->client->index($params);
    }

    private function indexExists()
    {
        $params = [
            'index' => self::INDEX_NAME
        ];
        return $this->client->indices()->exists($params);
    }

    private function createIndexWithMapping()
    {
        $params = [
            'index' => self::INDEX_NAME
        ];
        $this->client->indices()->create($params);

        $params = [
            'index' => 'automated_tests',
            'type' => 'result',
            'body' => [
                'result' => [
                    '_source' => [
                        'enabled' => true
                    ],
                    'properties' => [
                        'project' => [
                            'type' => 'string',
                            'index' => 'not_analyzed'
                        ],
                        'environment' => [
                            'type' => 'string',
                            'index' => 'not_analyzed'
                        ],
                        'testname' => [
                            'type' => 'string',
                            'index' => 'not_analyzed'
                        ],
                        'severity' => [
                            'type' => 'string',
                            'index' => 'not_analyzed'
                        ],
                        'browser' => [
                            'type' => 'string',
                            'index' => 'not_analyzed'
                        ],
                        'started' => [
                            'type' => 'date'
                        ],
                        'ended' => [
                            'type' => 'date'
                        ],
                        'status' => [
                            'type' => 'string',
                            'index' => 'not_analyzed'
                        ],
                        'error' => [
                            'type' => 'string',
                            'index' => 'not_analyzed'
                        ],
                        'attachments.log' => [
                            'type' => 'string',
                            'index' => 'not_analyzed'
                        ],
                        'attachments.screen' => [
                            'type' => 'string',
                            'index' => 'not_analyzed'
                        ]
                    ]
                ]
            ]
        ];
        $this->client->indices()->putMapping($params);
    }
}
