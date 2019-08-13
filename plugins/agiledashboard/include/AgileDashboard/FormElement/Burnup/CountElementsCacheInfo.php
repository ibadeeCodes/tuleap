<?php
/**
 * Copyright (c) Enalean, 2019 - Present. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Tuleap\AgileDashboard\FormElement\Burnup;

final class CountElementsCacheInfo
{
    /**
     * @var int
     */
    private $closed_elements;

    /**
     * @var int
     */
    private $total_elements;

    public function __construct(int $closed_elements, int $total_elements)
    {
        $this->closed_elements = $closed_elements;
        $this->total_elements  = $total_elements;
    }

    public function getClosedElements(): int
    {
        return $this->closed_elements;
    }

    public function getTotalElements(): int
    {
        return $this->total_elements;
    }
}
