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

namespace SmartyFilter\Controller;

use SmartyFilter\Model\SmartyFilterQuery;
use \Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Event\Cache\CacheEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\ConfigQuery;

/**
 * Class ConfigController
 */
class ConfigController extends BaseAdminController
{

    public function showFilterListAction()
    {
        return $this->render("filterslist");
    }

    public function activateFilterAction($id)
    {
        $filter = SmartyFilterQuery::create()->findOneById($id);

        if ($filter) {
            $filter->setActive(true);
            $filter->save();
        }
        $this->clearCacheAction();

        return $this->jsonResponse(json_encode($filter));
    }

    public function desactivateFilterAction($id)
    {
        $filter = SmartyFilterQuery::create()->findOneById($id);

        if ($filter) {
            $filter->setActive(false);
            $filter->save();
        }
        $this->clearCacheAction();

        return $this->jsonResponse(json_encode($filter));
    }

    public function clearCacheAction()
    {


        // dispatch cache clear
        $event = new CacheEvent($this->container->getParameter("kernel.cache_dir"));
        $this->dispatch(TheliaEvents::CACHE_CLEAR, $event);


        //dispatch cache assets clear
        $event = new CacheEvent(THELIA_WEB_DIR . "assets");
        $this->dispatch(TheliaEvents::CACHE_CLEAR, $event);


        //dispatch cache document and image clear
        $event = new CacheEvent(
            THELIA_WEB_DIR . ConfigQuery::read(
                'image_cache_dir_from_web_root',
                'cache' . DS . 'images'
            )
        );
        $this->dispatch(TheliaEvents::CACHE_CLEAR, $event);

        $event = new CacheEvent(
            THELIA_WEB_DIR . ConfigQuery::read(
                'document_cache_dir_from_web_root',
                'cache' . DS . 'documents'
            )
        );
        $this->dispatch(TheliaEvents::CACHE_CLEAR, $event);
    }
}
