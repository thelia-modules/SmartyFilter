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

namespace SmartyFilter\Hook;

use Symfony\Component\Routing\Router;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Translation\Translator;
use SmartyFilter\SmartyFilter;

/**
 * Class AdminHook
 * @package SmartyFilter\Hook
 */
class AdminHook extends BaseHook
{

    protected $router;
    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    protected function transQuick($id, $locale, $parameters = [])
    {
        if ($this->translator === null) {
            $this->translator = Translator::getInstance();
        }
        return $this->trans($id, $parameters, SmartyFilter::DOMAIN_NAME, $locale);
    }

    public function onMainTopMenuTools(HookRenderBlockEvent $event)
    {
        $url = $this->router->generate("smarty_filter.list");
        $lang = $this->getSession()->getLang();
        $title = $this->transQuick("Smarty Filter", $lang->getLocale());
        $event->add(
            [
                "id" => "smartyfilter",
                "class" => "",
                "title" => $title,
                "url" => $url
            ]
        );
    }
}
