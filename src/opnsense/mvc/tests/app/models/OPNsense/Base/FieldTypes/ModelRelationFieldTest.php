<?php
/**
 *    Copyright (C) 2018 Deciso B.V.
 *
 *    All rights reserved.
 *
 *    Redistribution and use in source and binary forms, with or without
 *    modification, are permitted provided that the following conditions are met:
 *
 *    1. Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *
 *    2. Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 *    THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
 *    INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 *    AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *    AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 *    OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 *    SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 *    INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 *    CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 *    ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 *    POSSIBILITY OF SUCH DAMAGE.
 *
 */

namespace tests\OPNsense\Base\FieldTypes;

// @CodingStandardsIgnoreStart
require_once 'Field_Framework_TestCase.php';
require_once __DIR__.'/../BaseModel/TestModel.php';
// @CodingStandardsIgnoreEnd

use \OPNsense\Base\FieldTypes\ModelRelationField;
use \Phalcon\DI\FactoryDefault;
use \OPNsense\Core\Config;

class ModelRelationFieldTest extends Field_Framework_TestCase
{
    protected function setUp()
    {
        FactoryDefault::getDefault()->get('config')->globals->config_path = __DIR__ .'/ModelRelationFieldTest/';
        Config::getInstance()->forceReload();
        $model = new \tests\OPNsense\Base\BaseModel\TestModel();
        foreach ($model->simpleList->items->iterateItems() as $nodeid => $node) {
            echo $nodeid . " " .$node->number ."\n";
        }
//        $item = $model->simpleList->items->Add();
//        $item->number = "1";
//        $model->serializeToConfig();
//        Config::getInstance()->save();
    }

    /**
     * test construct
     */
    public function testCanBeCreated()
    {
        $this->assertInstanceOf('\OPNsense\Base\FieldTypes\ModelRelationField', new ModelRelationField());
    }

    public function testSetSingleOk()
    {
        $field = new ModelRelationField();
        $field->setModel(array(
            "item" => array(
                "source" => "tests.OPNsense.Base.BaseModel.TestModel",
                "items" => "simpleList.items",
                "display" => "number"
            )
        ));
        $field->eventPostLoading();
        $field->setValue("5ea2a35c-b02b-485a-912b-d077e639bf9f");
        $this->assertEmpty($this->validate($field));
    }

    public function testSetSingleNok()
    {
        $field = new ModelRelationField();
        $field->setModel(array(
            "item" => array(
                "source" => "tests.OPNsense.Base.BaseModel.TestModel",
                "items" => "simpleList.items",
                "display" => "number"
            )
        ));
        $field->eventPostLoading();
        $field->setValue("XX5ea2a35c-b02b-485a-912b-d077e639bf9f");
        $this->assertEquals($this->validate($field), ['InclusionIn']);
    }

    public function testSetMultiOk()
    {
        $field = new ModelRelationField();
        $field->setMultiple("Y");
        $field->setModel(array(
            "item" => array(
                "source" => "tests.OPNsense.Base.BaseModel.TestModel",
                "items" => "simpleList.items",
                "display" => "number"
            )
        ));
        $field->eventPostLoading();
        $field->setValue("4d0e2835-7a19-4a19-8c23-e12383827594,5ea2a35c-b02b-485a-912b-d077e639bf9f");
        $this->assertEmpty($this->validate($field));
    }

    public function testSetMultiNok()
    {
        $field = new ModelRelationField();
        $field->setMultiple("Y");
        $field->setModel(array(
            "item" => array(
                "source" => "tests.OPNsense.Base.BaseModel.TestModel",
                "items" => "simpleList.items",
                "display" => "number"
            )
        ));
        $field->eventPostLoading();
        $field->setValue("x4d0e2835-7a19-4a19-8c23-e12383827594,5ea2a35c-b02b-485a-912b-d077e639bf9f");
        $this->assertEquals($this->validate($field), ['CsvListValidator']);
    }


    /**
     * type is not a container
     */
    public function testIsContainer()
    {
        $field = new ModelRelationField();
        $this->assertFalse($field->isContainer());
    }
}