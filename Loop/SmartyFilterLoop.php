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

namespace SmartyFilter\Loop;

use SmartyFilter\Model\Map\SmartyFilterTableMap;
use SmartyFilter\Model\SmartyFilterQuery;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Type\TypeCollection;
use Thelia\Type;
use Thelia\Type\BooleanOrBothType;
use Propel\Runtime\ActiveQuery\Criteria;

/**
 * Class SmartyFilterLoop
 * @package SmartyFilter\Loop
 */
class SmartyFilterLoop extends BaseI18nLoop implements PropelSearchLoopInterface
{

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $filter) {
            $loopResultRow = new LoopResultRow($filter);


            $loopResultRow->set("ID", $filter->getId())
                ->set("IS_TRANSLATED", $filter->getVirtualColumn('IS_TRANSLATED'))
                ->set("LOCALE", $filter->locale)
                ->set("TITLE", $filter->getVirtualColumn('i18n_TITLE'))
                ->set("CODE", $filter->getCode())
                ->set("DESCRIPTION", $filter->getVirtualColumn('i18n_DESCRIPTION'))
                ->set("ACTIVATE", $filter->getActive())
                ->set("TYPE", $filter->getFiltertype());

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

    /**
     * Definition of loop arguments
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            new Argument(
                'filtertype',
                new TypeCollection(
                    new Type\AlphaNumStringListType()
                )
            ),
            new Argument(
                'order',
                new TypeCollection(
                    new Type\EnumListType(array(
                        'alpha',
                        'alpha-reverse',
                        'random',
                        'given_id'
                    ))
                ),
                'alpha'
            )
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $search = SmartyFilterQuery::create();

        /* manage translations */
        $this->configureI18nProcessing($search, array('TITLE', 'DESCRIPTION'));

        $id = $this->getId();

        if (!is_null($id)) {
            $search->filterById($id, Criteria::IN);
        }

        $type = $this->getFiltertype();

        if (!is_null($type)) {
            $search->filterByFiltertype($type, Criteria::IN);
        }


        $orders = $this->getOrder();

        foreach ($orders as $order) {
            switch ($order) {
                case "alpha":
                    $search->addAscendingOrderByColumn('i18n_TITLE');
                    break;
                case "alpha-reverse":
                    $search->addDescendingOrderByColumn('i18n_TITLE');
                    break;
                case "given_id":
                    if (null === $id) {
                        throw new \InvalidArgumentException('Given_id order cannot be set without `id` argument');
                    }
                    foreach ($id as $singleId) {
                        $givenIdMatched = 'given_id_matched_' . $singleId;
                        $search->withColumn(SmartyFilterTableMap::ID . "='$singleId'", $givenIdMatched);
                        $search->orderBy($givenIdMatched, Criteria::DESC);
                    }
                    break;
                case "random":
                    $search->clearOrderByColumns();
                    $search->addAscendingOrderByColumn('RAND()');
                    break(2);
            }
        }

        return $search;
    }
}
