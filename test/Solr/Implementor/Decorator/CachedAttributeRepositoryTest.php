<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet
 * @copyright  Copyright (c) 2016 integer_net GmbH (http://www.integer-net.de/)
 * @author     Fabian Schmengler <fs@integer-net.de>
 */

namespace IntegerNet\Solr\Implementor\Decorator;


use IntegerNet\Solr\Implementor\AttributeRepository;
use IntegerNet\Solr\Implementor\Stub\AttributeStub;

class CachedAttributeRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CachedAttributeRepository
     */
    private $cachedAttributeRepository;
    /**
     * @var AttributeRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeRepositoryMock;

    protected function setUp()
    {
        $this->attributeRepositoryMock = $this->getMockForAbstractClass(AttributeRepository::class);
        $this->cachedAttributeRepository = new CachedAttributeRepository($this->attributeRepositoryMock);
    }

    public function testSearchableAttributes()
    {
        $storeIds = [[1], [2]];
        $attributes = [
            [AttributeStub::filterable('foo-store-1', [])],
            [AttributeStub::filterable('foo-store-2', [])],
        ];
        $this->attributeRepositoryMock->expects($this->exactly(2))
            ->method('getSearchableAttributes')
            ->withConsecutive(...$storeIds)
            ->willReturnOnConsecutiveCalls(...$attributes);
        $this->assertSame($attributes[0],
            $this->cachedAttributeRepository->getSearchableAttributes($storeIds[0][0])
        );
        $this->assertSame($attributes[0],
            $this->cachedAttributeRepository->getSearchableAttributes($storeIds[0][0])
        );
        $this->assertSame($attributes[1],
            $this->cachedAttributeRepository->getSearchableAttributes($storeIds[1][0])
        );
    }

    public function testSortableAttributes()
    {
        $storeIds = [[1], [2]];
        $attributes = [
            [AttributeStub::filterable('foo-store-1', [])],
            [AttributeStub::filterable('foo-store-2', [])],
        ];
        $this->attributeRepositoryMock->expects($this->exactly(2))
            ->method('getSortableAttributes')
            ->withConsecutive(...$storeIds)
            ->willReturnOnConsecutiveCalls(...$attributes);
        $this->assertSame($attributes[0],
            $this->cachedAttributeRepository->getSortableAttributes($storeIds[0][0])
        );
        $this->assertSame($attributes[0],
            $this->cachedAttributeRepository->getSortableAttributes($storeIds[0][0])
        );
        $this->assertSame($attributes[1],
            $this->cachedAttributeRepository->getSortableAttributes($storeIds[1][0])
        );
    }

    public function testFilterableInSearchAttributes()
    {
        $args = [[1, true], [1, false], [2, true]];
        $attributes = [
            [AttributeStub::filterable('bar-store-1', []), AttributeStub::filterable('foo-store-1', [])],
            [AttributeStub::filterable('foo-store-1', []), AttributeStub::filterable('bar-store-1', [])],
            [AttributeStub::filterable('foo-store-2', [])],
        ];
        $this->attributeRepositoryMock->expects($this->exactly(\count($args)))
            ->method('getFilterableInSearchAttributes')
            ->withConsecutive(...$args)
            ->willReturnOnConsecutiveCalls(...$attributes);
        $this->assertSame($attributes[0],
            $this->cachedAttributeRepository->getFilterableInSearchAttributes(...$args[0])
        );
        $this->assertSame($attributes[0],
            $this->cachedAttributeRepository->getFilterableInSearchAttributes(...$args[0])
        );
        $this->assertSame($attributes[1],
            $this->cachedAttributeRepository->getFilterableInSearchAttributes(...$args[1])
        );
        $this->assertSame($attributes[2],
            $this->cachedAttributeRepository->getFilterableInSearchAttributes(...$args[2])
        );
    }

    public function testFilterableInCatalogAttributes()
    {
        $args = [[1, true], [1, false], [2, true]];
        $attributes = [
            [AttributeStub::filterable('bar-store-1', []), AttributeStub::filterable('foo-store-1', [])],
            [AttributeStub::filterable('foo-store-1', []), AttributeStub::filterable('bar-store-1', [])],
            [AttributeStub::filterable('foo-store-2', [])],
        ];
        $this->attributeRepositoryMock->expects($this->exactly(\count($args)))
            ->method('getFilterableInCatalogAttributes')
            ->withConsecutive(...$args)
            ->willReturnOnConsecutiveCalls(...$attributes);
        $this->assertSame($attributes[0],
            $this->cachedAttributeRepository->getFilterableInCatalogAttributes(...$args[0])
        );
        $this->assertSame($attributes[0],
            $this->cachedAttributeRepository->getFilterableInCatalogAttributes(...$args[0])
        );
        $this->assertSame($attributes[1],
            $this->cachedAttributeRepository->getFilterableInCatalogAttributes(...$args[1])
        );
        $this->assertSame($attributes[2],
            $this->cachedAttributeRepository->getFilterableInCatalogAttributes(...$args[2])
        );
    }

    public function testFilterableInCatalogOrSearchAttributes()
    {
        $args = [[1, true], [1, false], [2, true]];
        $attributes = [
            [AttributeStub::filterable('bar-store-1', []), AttributeStub::filterable('foo-store-1', [])],
            [AttributeStub::filterable('foo-store-1', []), AttributeStub::filterable('bar-store-1', [])],
            [AttributeStub::filterable('foo-store-2', [])],
        ];
        $this->attributeRepositoryMock->expects($this->exactly(\count($args)))
            ->method('getFilterableInCatalogOrSearchAttributes')
            ->withConsecutive(...$args)
            ->willReturnOnConsecutiveCalls(...$attributes);
        $this->assertSame($attributes[0],
            $this->cachedAttributeRepository->getFilterableInCatalogOrSearchAttributes(...$args[0])
        );
        $this->assertSame($attributes[0],
            $this->cachedAttributeRepository->getFilterableInCatalogOrSearchAttributes(...$args[0])
        );
        $this->assertSame($attributes[1],
            $this->cachedAttributeRepository->getFilterableInCatalogOrSearchAttributes(...$args[1])
        );
        $this->assertSame($attributes[2],
            $this->cachedAttributeRepository->getFilterableInCatalogOrSearchAttributes(...$args[2])
        );
    }

    public function testAttributeCodesToIndex()
    {
        $attributeCodes = ['attribute-1', 'attribute-2', 'attribute-3'];
        $this->attributeRepositoryMock->expects($this->once())
            ->method('getAttributeCodesToIndex')
            ->willReturn($attributeCodes);
        $this->assertSame($attributeCodes,
            $this->cachedAttributeRepository->getAttributeCodesToIndex()
        );
        $this->assertSame($attributeCodes,
            $this->cachedAttributeRepository->getAttributeCodesToIndex()
        );
    }

    public function testAttributeByCode()
    {
        $args = [['color', 1], ['color', 2], ['size', 1]];
        $attribute = [
            AttributeStub::filterable('color', ['label' => 'color1']),
            AttributeStub::filterable('color', ['label' => 'color2']),
            AttributeStub::filterable('size', ['label' => 'size1']),
        ];
        $this->attributeRepositoryMock->expects($this->exactly(\count($args)))
            ->method('getAttributeByCode')
            ->withConsecutive(...$args)
            ->willReturnOnConsecutiveCalls(...$attribute);
        $this->assertSame($attribute[0],
            $this->cachedAttributeRepository->getAttributeByCode(...$args[0])
        );
        $this->assertSame($attribute[0],
            $this->cachedAttributeRepository->getAttributeByCode(...$args[0])
        );
        $this->assertSame($attribute[1],
            $this->cachedAttributeRepository->getAttributeByCode(...$args[1])
        );
        $this->assertSame($attribute[2],
            $this->cachedAttributeRepository->getAttributeByCode(...$args[2])
        );    }

}
