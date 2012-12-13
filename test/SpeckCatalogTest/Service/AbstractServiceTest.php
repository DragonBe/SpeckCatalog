<?php

namespace SpeckCatalogTest\Mapper;

use PHPUnit\Extensions\Database\TestCase;

class AbstractServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testFindCallsFindOnMapper()
    {
        $mockedMapper = $this->getMock('\SpeckCatalog\Mapper\Product');
        $mockedMapper->expects($this->any())
            ->method('find');

        $service = $this->getService();
        $service->setEntityMapper($mockedMapper);

        $data = array('return_model' => true);
        $service->find($data);
    }

    public function testPopulateReturnsInstanceOfModelParam()
    {
        //$service = $this->getService();
        //$model = new \SpeckCatalog\Model\Product();
        //$return = $service->populate($model);
        //$this->assertTrue($return instanceOf \SpeckCatalog\Model\Product);
    }

    public function testGetEntity()
    {
    }

    public function getMockMapper($methodName)
    {
        $mock = $this->getMock('\SpeckCatalog\Mapper\Product');
        $mock->expects($this->any())
            ->method($methodName);
        return $mock;
    }

    public function getService()
    {
        return new \SpeckCatalog\Service\Product();
    }
}
