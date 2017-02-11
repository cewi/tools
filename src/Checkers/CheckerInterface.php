<?php

namespace Cewi\Checkers;

/**
 *
 * @author cewi <c.wichmann@gmx.de>
 *
 * @license https://opensource.org/licenses/MIT
 */
interface CheckerInterface
{
    public function isDeliverable($address);

    public function getAddress();
}
