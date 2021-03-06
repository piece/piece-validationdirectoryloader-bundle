<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5.3
 *
 * Copyright (c) 2012 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PieceValidationDirectoryLoaderBundle
 * @copyright  2012 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: @package_version@
 * @since      File available since Release 0.1.0
 */

namespace Piece\Bundle\ValidationDirectoryLoaderBundle\Tests\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

use Piece\Bundle\ValidationDirectoryLoaderBundle\DependencyInjection\PieceValidationDirectoryLoaderExtension;
use Piece\Bundle\ValidationDirectoryLoaderBundle\Tests\DependencyInjection\Fixtures\Entity\Foo;
use Piece\Bundle\ValidationDirectoryLoaderBundle\Tests\DependencyInjection\Fixtures\Entity\Bar;
use Piece\Bundle\ValidationDirectoryLoaderBundle\Tests\DependencyInjection\Fixtures\Entity\Baz;

/**
 * @package    PieceValidationDirectoryLoaderBundle
 * @copyright  2012 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class PieceValidationDirectoryLoaderExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function registersMappingFilesInTheSpecifiedDirectories()
    {
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.debug' => false,
            'kernel.bundles' => array(
                'FrameworkBundle' => 'Symfony\Bundle\FrameworkBundle\FrameworkBundle',
                'PieceValidationDirectoryLoaderBundle' => 'Piece\Bundle\ValidationDirectoryLoaderBundle\PieceValidationDirectoryLoaderBundle',
            ),
            'kernel.cache_dir' => __DIR__,
        )));
        $container->registerExtension(new FrameworkExtension());
        $container->registerExtension(new PieceValidationDirectoryLoaderExtension());
        $container->loadFromExtension('framework', array(
            'secret' => '154F520832A9BC66316C259EEC70E4FA671A12F5',
            'validation' => array('enable_annotations' => false),
        ));
        $container->loadFromExtension('piece_validationdirectoryloader', array(
            'mapping_dirs' => array(
                __DIR__ . '/Fixtures/validation/a',
                __DIR__ . '/Fixtures/validation/b',
        )));
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        foreach (array(new Foo(), new Bar(), new Baz()) as $entity) {
            $violations = $container->get('validator')->validate($entity);
            $this->assertEquals(1, count($violations));
            $this->assertSame($entity, $violations[0]->getRoot());

            $entityClass = new \ReflectionObject($entity);
            $this->assertEquals(strtolower($entityClass->getShortName()), $violations[0]->getPropertyPath());
        }
    }
}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
