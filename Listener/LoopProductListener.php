<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ProductLoopAttributeFilter\Listener;

use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Loop\LoopExtendsArgDefinitionsEvent;
use Thelia\Core\Event\Loop\LoopExtendsBuildModelCriteriaEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Model\ProductQuery;

class LoopProductListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::getLoopExtendsEvent(
                TheliaEvents::LOOP_EXTENDS_ARG_DEFINITIONS,
                'product'
            ) => ['productArgDefinitions', 128],
            TheliaEvents::getLoopExtendsEvent(
                TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA,
                'product'
            ) => ['productBuildModelCriteria', 128],
        ];
    }

    public function productArgDefinitions(LoopExtendsArgDefinitionsEvent $event): void
    {
        $argument = $event->getArgumentCollection();

        $argument->addArgument(Argument::createBooleanTypeArgument('attribute_extend', false));

        $argument->addArgument(Argument::createIntListTypeArgument('attribute_availability', null));

        $argument->addArgument(Argument::createIntTypeArgument('attribute_min_stock', null));
    }

    public function productBuildModelCriteria(LoopExtendsBuildModelCriteriaEvent $event): void
    {
        if ($event->getLoop()->getAttributeExtend()) {
            if (null !== $attributeAvailability = $event->getLoop()->getAttributeAvailability()) {
                $this->manageAttributeAvailability($event, $attributeAvailability);
            }
        }
    }

    protected function manageAttributeAvailability(LoopExtendsBuildModelCriteriaEvent $event, array $attributeAvailability): void
    {
        /** @var ProductQuery $query */
        $query = $event->getModelCriteria();

        $useProductSaleElementsQuery = $query
            ->useProductSaleElementsQuery('pse_attribute_extend', Criteria::INNER_JOIN);

        if (null !== $minStock = $event->getLoop()->getAttributeMinStock()) {
            $useProductSaleElementsQuery->filterByQuantity($minStock, Criteria::GREATER_EQUAL);
        }

        $useProductSaleElementsQuery->useAttributeCombinationQuery('attribute_extend', Criteria::INNER_JOIN)
            ->filterByAttributeAvId($attributeAvailability, Criteria::IN)
        ->endUse()
        ->endUse();
    }
}
