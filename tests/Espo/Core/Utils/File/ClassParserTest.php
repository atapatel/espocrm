<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2015 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 ************************************************************************/

namespace tests\Espo\Core\Utils\File;

use tests\ReflectionHelper;


class ClassParserTest extends \PHPUnit_Framework_TestCase
{
    protected $object;
    
    protected $objects;

    protected $reflection;


    protected function setUp()
    {  
        $this->objects['fileManager'] = new \Espo\Core\Utils\File\Manager();
        $this->objects['config'] = $this->getMockBuilder('\Espo\Core\Utils\Config')->disableOriginalConstructor()->getMock();
        $this->objects['metadata'] = $this->getMockBuilder('\Espo\Core\Utils\Metadata')->disableOriginalConstructor()->getMock();

        $this->object = new \Espo\Core\Utils\File\ClassParser($this->objects['fileManager'], $this->objects['config'], $this->objects['metadata']);

        $this->reflection = new ReflectionHelper($this->object);
    }

    protected function tearDown()
    {
        $this->object = NULL;
    }


    function testGetClassNameHash()
    {
        $paths = array(
            'tests/testData/EntryPoints/Espo/EntryPoints',
             'tests/testData/EntryPoints/Espo/Modules/Crm/EntryPoints',             
        );

        $result = array(
            'Download' => '\tests\testData\EntryPoints\Espo\EntryPoints\Download',
            'Test' => '\tests\testData\EntryPoints\Espo\EntryPoints\Test',
            'InModule' => '\tests\testData\EntryPoints\Espo\Modules\Crm\EntryPoints\InModule'          
        );        
        $this->assertEquals( $result, $this->reflection->invokeMethod('getClassNameHash', array($paths)) ); 
    }    


    function testGetDataWithCache()
    {
        $this->objects['config']
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue(true));

        $cacheFile = 'tests/testData/EntryPoints/cache/entryPoints.php';
        $paths = array(
            'corePath' => 'tests/testData/EntryPoints/Espo/EntryPoints',
             'modulePath' => 'tests/testData/EntryPoints/Espo/Modules/{*}/EntryPoints',
            'customPath' => 'tests/testData/EntryPoints/Espo/Custom/EntryPoints',      
        );

        $result = array (
          'Download' => '\\tests\\testData\\EntryPoints\\Espo\\EntryPoints\\Download',
        );

        $this->assertEquals( $result, $this->reflection->invokeMethod('getData', array($paths, $cacheFile)) );        
    }    

    function testGetDataWithNoCache()
    {
        $this->objects['config']
            ->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValue(false));

        $this->objects['metadata']
            ->expects($this->once())
            ->method('getModuleList')
            ->will($this->returnValue(
                array(
                    'Crm',
                )
            )); 

        $cacheFile = 'tests/testData/EntryPoints/cache/entryPoints.php';
        $paths = array(
            'corePath' => 'tests/testData/EntryPoints/Espo/EntryPoints',
             'modulePath' => 'tests/testData/EntryPoints/Espo/Modules/{*}/EntryPoints',
            'customPath' => 'tests/testData/EntryPoints/Espo/Custom/EntryPoints',      
        );

        $result = array(
            'Download' => '\tests\testData\EntryPoints\Espo\EntryPoints\Download',
            'Test' => '\tests\testData\EntryPoints\Espo\EntryPoints\Test',
            'InModule' => '\tests\testData\EntryPoints\Espo\Modules\Crm\EntryPoints\InModule'          
        );  

        $this->assertEquals( $result, $this->reflection->invokeMethod('getData', array($paths, $cacheFile)) );        
    }


    function testGetDataWithNoCacheString()
    {
        $this->objects['config']
            ->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValue(false));

        $this->objects['metadata']
            ->expects($this->never())
            ->method('getModuleList')
            ->will($this->returnValue(
                array(
                    'Crm',
                )
            )); 

        $cacheFile = 'tests/testData/EntryPoints/cache/entryPoints.php';
        $path = 'tests/testData/EntryPoints/Espo/EntryPoints';

        $result = array(
            'Download' => '\tests\testData\EntryPoints\Espo\EntryPoints\Download',
            'Test' => '\tests\testData\EntryPoints\Espo\EntryPoints\Test',                    
        );  

        $this->assertEquals( $result, $this->reflection->invokeMethod('getData', array($path, $cacheFile)) );      
    }


    function testGetDataWithCacheFalse()
    {
        $this->objects['config']
            ->expects($this->never())
            ->method('get')
            ->will($this->returnValue(false));

        $this->objects['metadata']
            ->expects($this->never())
            ->method('getModuleList')
            ->will($this->returnValue(
                array(
                    'Crm',
                )
            )); 
        
        $paths = array(
            'corePath' => 'tests/testData/EntryPoints/Espo/EntryPoints',            
            'customPath' => 'tests/testData/EntryPoints/Espo/Custom/EntryPoints',      
        );

        $result = array(
            'Download' => '\tests\testData\EntryPoints\Espo\EntryPoints\Download',
            'Test' => '\tests\testData\EntryPoints\Espo\EntryPoints\Test',                    
        );  

        $this->assertEquals( $result, $this->reflection->invokeMethod('getData', array($paths)) );      
    }
    


}

?>
