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

namespace SmartyFilter;

use SmartyFilter\Model\SmartyFilterQuery;
use Thelia\Module\BaseModule;
use SmartyFilter\Compiler\RegisterParserFilterPass;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Thelia\Install\Database;
use SmartyFilter\Handler\ConfigurationFileHandler;
use Thelia\Model\ModuleQuery;
use Thelia\Model\Module;

class SmartyFilter extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'smartyfilter';

    const TAG_PRE_FILTER = "thelia.parser.register_pre_filter";
    const TAG_POST_FILTER = "thelia.parser.register_post_filter";
    const TAG_OUTPUT_FILTER = "thelia.parser.register_output_filter";


    public function preActivation(ConnectionInterface $con = null)
    {
        $insert = false;
        $activate = true;

        try {
            SmartyFilterQuery::create()->findOne();
        } catch (PropelException $exception) {
            $insert = true;
        }

        if ($insert) {
            try {
                $database = new Database($con);

                // Insert Models
                $database->insertSql(null, [__DIR__ . DS . 'Config' . DS . 'thelia.sql']);
            } catch (\PDOException $exception) {
                $activate = false;
            }
        }

        return $activate;
    }

    public function postActivation(ConnectionInterface $con = null)
    {
        $configurationFileHandler = new ConfigurationFileHandler;

        $modules = ModuleQuery::create()->findByActivate(BaseModule::IS_ACTIVATED);
        /** @var Module $module */
        foreach ($modules as $module) {
            $configurationFileHandler->loadConfigurationFile($module);
        }
    }

    public static function getCompilers()
    {
        return [
            new RegisterParserFilterPass()
        ];
    }
}
