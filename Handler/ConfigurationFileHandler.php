<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/
/*************************************************************************************/

namespace SmartyFilter\Handler;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Thelia\Model\Module;
use SmartyFilter\Model\SmartyFilterQuery;
use SmartyFilter\Model\SmartyFilter;

/**
 * Class ConfigurationFileHandler
 * @package SmartyFilter\Handler
 */
class ConfigurationFileHandler
{
    /**
     * Find, parse and load smarty filter configuration file for module
     *
     * @param Module $module A module object
     *
     * @throws InvalidConfigurationException
     */
    public function loadConfigurationFile(Module $module)
    {
        $finder = (new Finder)
            // TODO: Flip when yaml parsing will be up
//            ->name('#smarty[-_\.]?filter\.(?:xml|yml|yaml)#i')
            ->name('#smarty[-_\.]?filter\.xml#i')
            ->in($module->getAbsoluteConfigPath());
        $count = $finder->count();

        if ($count > 1) {
            throw new InvalidConfigurationException('Too many configuration file.');
        } else {
            foreach ($finder as $file) {
                if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'xml') {
                    $moduleConfig = $this->parseXml($file);
                } else {
                    $moduleConfig = $this->parseYml($file);
                }

                $this->applyConfig($moduleConfig);
            }
        }
    }

    /**
     * Get config from xml file
     *
     * @param SplFileInfo $file XML file
     *
     * @return array Smarty Filter module configuration
     */
    protected function parseXml(SplFileInfo $file)
    {
        $dom = XmlUtils::loadFile($file, realpath(dirname(__DIR__) . DS . 'Schema' . DS . 'smarty_filter.xsd'));
        /** @var \Symfony\Component\DependencyInjection\SimpleXMLElement $xml */
        $xml = simplexml_import_dom($dom, '\\Symfony\\Component\\DependencyInjection\\SimpleXMLElement');

        $parsedConfig = [];
        /** @var \Symfony\Component\DependencyInjection\SimpleXMLElement $smartyFilterDefinition */
        foreach ($xml->smartyfilter as $smartyFilterDefinition) {
            $descriptive = [];
            /** @var \Symfony\Component\DependencyInjection\SimpleXMLElement $descriptiveDefinition */
            foreach ($smartyFilterDefinition->descriptive as $descriptiveDefinition) {
                $descriptive[] = [
                    'locale' => $descriptiveDefinition->getAttributeAsPhp('locale'),
                    'title' => (string)$descriptiveDefinition->title,
                    'description' => (string)$descriptiveDefinition->description,
                    'type' => (string)$descriptiveDefinition->type
                ];
            }

            $parsedConfig['smarty_filter'][] = [
                'code' => $smartyFilterDefinition->getAttributeAsPhp('code'),
                'descriptive' => $descriptive
            ];
        }


        return $parsedConfig;
    }

    /**
     * Get config from yml file
     *
     * @param SplFileInfo $file Yaml file
     *
     * @return array Smarty Filter module configuration
     */
    protected function parseYml(SplFileInfo $file)
    {
        //@todo

        return [];
    }

    /**
     * Save new smarty filter to database
     *
     * @param array $moduleConfiguration Smarty Filter module configuration
     */
    protected function applyConfig(array $moduleConfiguration)
    {
        foreach ($moduleConfiguration['smarty_filter'] as $smartyFilterData) {
            if (SmartyFilterQuery::create()->findOneByCode($smartyFilterData['code']) === null) {
                $smartyFilter = (new SmartyFilter())
                    ->setCode($smartyFilterData['code']);

                foreach ($smartyFilterData['descriptive'] as $descriptiveData) {
                    $smartyFilter
                        ->setLocale($descriptiveData['locale'])
                        ->setTitle($descriptiveData['title'])
                        ->setDescription($descriptiveData['description'])
                        ->setFiltertype($descriptiveData['type']);
                }

                $smartyFilter->save();
            }
        }
    }
}
