<?php

namespace IntegerNet\Solr\Resource;

use IntegerNet\Solr\Config\Stub\Config;
use IntegerNet\Solr\Config\Stub\GeneralConfigBuilder;
use IntegerNet\Solr\Config\Stub\IndexingConfigBuilder;
use IntegerNet\Solr\Config\Stub\ServerConfigBuilder;
use PHPUnit\Framework\TestCase;

class SwapCoreConfigurationTest extends TestCase
{
    const DO_NOT_RESTRICT_STORE_IDS = null;

    private static function configStub($generalConfig, $indexingConfig, $serverConfig)
    {
        return Config::defaultConfig()
            ->withGeneralConfig($generalConfig)
            ->withIndexingConfig($indexingConfig)
            ->withServerConfig($serverConfig);
    }

    private static function generalConfigEnabled()
    {
        return GeneralConfigBuilder::defaultConfig();
    }

    private static function generalConfigDisabled()
    {
        return GeneralConfigBuilder::defaultConfig()->withActive(false);
    }

    private static function indexingConfigWithSwap()
    {
        return IndexingConfigBuilder::swapCoreConfig();
    }

    private static function indexingConfigWithoutSwap()
    {
        return IndexingConfigBuilder::defaultConfig();
    }

    private static function serverConfigWithCores($core, $swapCore)
    {
        return ServerConfigBuilder::defaultConfig()->withCore($core)->withSwapCore($swapCore);
    }

    private static function configWithSwap($core, $swapCore): Config
    {
        return self::configStub(
            self::generalConfigEnabled(),
            self::indexingConfigWithSwap(),
            self::serverConfigWithCores($core, $swapCore)
        );
    }

    private static function configWithoutSwap($core, $swapCore): Config
    {
        return self::configStub(
            self::generalConfigEnabled(),
            self::indexingConfigWithoutSwap(),
            self::serverConfigWithCores($core, $swapCore)
        );
    }

    public function testStub()
    {
        $stub = self::configStub(
            self::generalConfigEnabled(),
            self::indexingConfigWithSwap(),
            self::serverConfigWithCores('core0', 'core1')
        );
        $this->assertTrue($stub->getGeneralConfig()->isActive(), 'is active');
        $this->assertTrue($stub->getIndexingConfig()->isSwapCores(), 'is swap');
        $this->assertEquals('core0', $stub->getServerConfig()->getCore());
        $this->assertEquals('core1', $stub->getServerConfig()->getSwapCore());
    }

    /**
     * @dataProvider dataInvalidConfiguration
     * @param Config[] $invalidConfiguration
     * @param int[]|null $restrictToStoreIds
     * @param string $expectedException
     */
    public function testThrowExceptionForInvalidConfiguration(
        array $invalidConfiguration,
        array $restrictToStoreIds = null,
        string $expectedException
    ) {
        $resource = new ResourceFacade($invalidConfiguration);
        $this->expectException(\IntegerNet\Solr\Exception::class);
        $this->expectExceptionMessage($expectedException);
        $resource->checkSwapCoresConfiguration($restrictToStoreIds);
    }

    /**
     * @dataProvider dataValidConfiguration
     * @param Config[] $validConfiguration
     * @param int[]|null $restrictToStoreIds
     */
    public function testNoExceptionForValidConfiguration(
        array $validConfiguration,
        array $restrictToStoreIds = null
    ) {
        $resource = new ResourceFacade($validConfiguration);
        $resource->checkSwapCoresConfiguration($restrictToStoreIds);
        $this->assertTrue(true, 'Dummy assertion: Check should return without exception');
    }


    public static function dataInvalidConfiguration()
    {
        return [
            'Swapping not enabled for all stores with same configuration' => [
                [
                    1 => self::configWithSwap('core0', 'core1'),
                    2 => self::configWithoutSwap('core0', 'core1'),
                ],
                self::DO_NOT_RESTRICT_STORE_IDS,
                'Configuration Error: Activate Core Swapping for all Store Views using the same Solr Core.'
            ],
            'Different swap cores for same core' => [
                [
                    1 => self::configWithSwap('core0', 'core1'),
                    2 => self::configWithSwap('core0', 'core2'),
                ],
                self::DO_NOT_RESTRICT_STORE_IDS,
                'Configuration Error: A Core must swap with the same Core for all Store Views using it.'
            ],
            'Invalid store id restriction' => [
                [
                    1 => self::configWithSwap('core0', 'core1'),
                    2 => self::configWithSwap('core0', 'core1'),
                ],
                [1],
                'Call Error: All Stores using the same Swap Configuration must be reindexed at the same Time.'
            ]
        ];
    }

    public static function dataValidConfiguration()
    {
        return [
            'Swapping enabled for all stores with same configuration' => [
                [
                    1 => self::configWithSwap('core0', 'core1'),
                    2 => self::configWithSwap('core0', 'core1'),
                ],
                self::DO_NOT_RESTRICT_STORE_IDS,
            ],
            'Invalid configuration but store not enabled' => [
                [
                    1 => self::configWithSwap('core0', 'core1'),
                    2 => self::configWithoutSwap('core0', 'core1')->withGeneralConfig(self::generalConfigDisabled()),
                ],
                self::DO_NOT_RESTRICT_STORE_IDS,
            ],
            'Overriden default configuration' => [
                [
                    0 => self::configWithoutSwap('core0', 'core1'),
                    1 => self::configWithSwap('core0', 'core2'),
                    2 => self::configWithSwap('core0', 'core2'),
                ],
                self::DO_NOT_RESTRICT_STORE_IDS,
            ],
        ];
    }
}
