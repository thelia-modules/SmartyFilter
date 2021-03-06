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

namespace SmartyFilter\Filter;

/**
 * Class MinifyHTMLFilter.
 */
class MinifyHTMLFilter
{
    public function filter($tpl_output, $smarty)
    {
        $tpl_output = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<![a-zA-Z0-9"\':\.\-]|\\\|\')\/\/.*))/',
            '',
            $tpl_output);
        $tpl_output = preg_replace('#(?ix)(?>[^\S ]\s*|\s{2,})(?=(?:(?:[^<]++|<(?!/?(?:textarea|pre|script)\b))*+)(?:<(?>textarea|pre|script)\b|\z))#',
            ' ', $tpl_output);

        return $tpl_output;
    }
}
