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

    public static function dataInvalidConfiguration()
    {
        $config_swap_core0_core1 = self::configStub(
            self::generalConfigEnabled(),
            self::indexingConfigWithSwap(),
            self::serverConfigWithCores('core0', 'core1')
        );
        $config_noswap_core0_core1 = self::configStub(
            self::generalConfigEnabled(),
            self::indexingConfigWithoutSwap(),
            self::serverConfigWithCores('core0', 'core1')
        );
        $config_swap_core0_core2 = self::configStub(
            self::generalConfigEnabled(),
            self::indexingConfigWithSwap(),
            self::serverConfigWithCores('core0', 'core2')
        );
        return [
            'Swapping not enabled for all stores with same configuration' => [
                [
                    1 => $config_swap_core0_core1,
                    2 => $config_noswap_core0_core1,
                ],
                self::DO_NOT_RESTRICT_STORE_IDS,
                'Configuration Error: Activate Core Swapping for all Store Views using the same Solr Core.'
            ],
            'Different swap cores for same core' => [
                [
                    1 => $config_swap_core0_core1,
                    2 => $config_swap_core0_core2,
                ],
                self::DO_NOT_RESTRICT_STORE_IDS,
                'Configuration Error: A Core must swap with the same Core for all Store Views using it.'
            ],
            'Invalid store id restriction' => [
                [
                    1 => $config_swap_core0_core1,
                    2 => $config_swap_core0_core1,
                ],
                [1],
                'Call Error: All Stores using the same Swap Configuration must be reindexed at the same Time.'
            ]
        ];
    }
}