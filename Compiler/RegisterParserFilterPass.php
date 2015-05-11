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

namespace SmartyFilter\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use SmartyFilter\Model\SmartyFilterQuery;
use SmartyFilter\Model\SmartyFilter;

/**
 * Class RegisterParserFilterPass
 * @package SmartyFilter\Compiler
 */
class RegisterParserFilterPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.r
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {

        $this->addFilters($container, 'pre');
        $this->addFilters($container, 'post');
        $this->addFilters($container, 'output');

    }

    public function addFilters(ContainerBuilder $container, $tag)
    {
        if (!$container->hasDefinition("thelia.parser")) {
            return;
        }

        /** @var \Symfony\Component\DependencyInjection\Definition $smarty */
        $smarty = $container->getDefinition("thelia.parser");

        foreach ($container->findTaggedServiceIds("thelia.parser.register_" . $tag . "_filter") as $id => $filter) {
            /** @var SmartyFilter $smartyFilter */
            $smartyFilter = SmartyFilterQuery::create()->findOneByCode($id);
            if ($smartyFilter && $smartyFilter->getActive()) {
                $smarty->addMethodCall("registerFilter", array($tag, array(new Reference($id), "filter")));
            }
        }
    }
}
