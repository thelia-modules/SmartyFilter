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

namespace SmartyFilter\Parser;

use SmartyFilter\Model\SmartyFilterQuery;
use TheliaSmarty\Template\SmartyParser as TheliaSmartyParser;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Template\ParserContext;
use Thelia\Core\Template\TemplateHelperInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class SmartyParser
 * @package SmartyFilter\Parser
 */
class SmartyParser extends TheliaSmartyParser
{

    protected $container;

    public function __construct(
        Request $request,
        EventDispatcherInterface $dispatcher,
        ParserContext $parserContext,
        TemplateHelperInterface $templateHelper,
        $env = "prod",
        $debug = false,
        Container $container
    ) {
        $this->container = $container;
        parent::__construct($request, $dispatcher, $parserContext, $templateHelper, $env, $debug);
    }

    /**
     *
     * @inheritdoc
     */
    public function fetch(
        $template = null,
        $cache_id = null,
        $compile_id = null,
        $parent = null,
        $display = false,
        $merge_tpl_vars = true,
        $no_output_filter = false
    ) {
        if ($this->getTemplateDefinition()->getPath() === "backOffice/default") {
            $this->disableSmartyFilter();
        }
        $output = parent::fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars,
            $no_output_filter);

        if ($this->getTemplateDefinition()->getPath() === "backOffice/default") {
            $this->enableSmartyFilter();
        }

        return $output;
    }

    public function disableSmartyFilter()
    {
        $filters = SmartyFilterQuery::create()->find();
        foreach ($filters as $filter) {
            $this->unregisterFilter('output', array(get_class($this->container->get($filter->getCode())), "filter"));
        }
    }

    public function enableSmartyFilter()
    {
        $filters = SmartyFilterQuery::create()->find();
        foreach ($filters as $filter) {
            $this->registerFilter('output', array($this->container->get($filter->getCode()), "filter"));
        }
    }
}
