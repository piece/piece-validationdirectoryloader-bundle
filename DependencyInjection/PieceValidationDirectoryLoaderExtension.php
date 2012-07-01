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

namespace Piece\Bundle\ValidationDirectoryLoaderBundle\DependencyInjection;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @package    PieceValidationDirectoryLoaderBundle
 * @copyright  2012 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class PieceValidationDirectoryLoaderExtension extends Extension
{
    /**
     * @var string
     */
    private static $XML_MAPPING_FILES_PARAMETER = 'validator.mapping.loader.xml_files_loader.mapping_files';

    /**
     * @var string
     */
    private static $YAML_MAPPING_FILES_PARAMETER = 'validator.mapping.loader.yaml_files_loader.mapping_files';

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        if (count($config['mapping_dirs']) == 0) return;

        $xmlMappingFiles = $container->getParameter(self::$XML_MAPPING_FILES_PARAMETER);
        $yamlMappingFiles = $container->getParameter(self::$YAML_MAPPING_FILES_PARAMETER);

        $finder = Finder::create()
            ->files()
            ->name('*.xml')
            ->name('*.yml')
            ->in($config['mapping_dirs']);
        foreach ($finder as $file) { /* @var $file \SplFileInfo */
            $container->addResource(new FileResource($file->getPathname()));
            if (substr($file->getFilename(), - strlen('.xml'), strlen('.xml')) === '.xml') {
                $xmlMappingFiles[] = $file->getPathname();
            } elseif (substr($file->getFilename(), - strlen('.yml'), strlen('.yml')) === '.yml') {
                $yamlMappingFiles[] = $file->getPathname();
            }
        }

        $container->setParameter(self::$XML_MAPPING_FILES_PARAMETER, $xmlMappingFiles);
        $container->setParameter(self::$YAML_MAPPING_FILES_PARAMETER, $yamlMappingFiles);
    }

    public function getAlias()
    {
        return 'piece_validationdirectoryloader';
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
