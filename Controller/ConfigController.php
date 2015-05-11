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

        return $this->jsonResponse(json_encode($filter));
    }

    public function desactivateFilterAction($id)
    {
        $filter = SmartyFilterQuery::create()->findOneById($id);

        if ($filter) {
            $filter->setActive(false);
            $filter->save();
        }

        return $this->jsonResponse(json_encode($filter));
    }
}
