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

use TheliaSmarty\Template\SmartyParser as TheliaSmartyParser;

/**
 * Class SmartyParser
 * @package SmartyFilter\Parser
 */
class SmartyParser extends TheliaSmartyParser
{

    /**
     *
     * @inheritdoc
     */
    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {
        if ($this->getTemplateDefinition()->getPath() === "backOffice/default") {
            $no_output_filter = true;
        }
        return parent::fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter);
    }
}
