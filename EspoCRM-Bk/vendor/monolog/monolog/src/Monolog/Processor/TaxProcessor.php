<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Processor;

/**
 * Adds a tags array into record
 *
 * @author Martijn Riemers
 */
class TaxProcessor
{
    private $taxs;

    public function __construct(array $taxs = array())
    {
        $this->setTaxs($taxs);
    }

    public function addTaxs(array $taxs = array())
    {
        $this->taxs = array_merge($this->taxs, $taxs);
    }

    public function setTaxs(array $taxs = array())
    {
        $this->taxs = $taxs;
    }

    public function __invoke(array $record)
    {
        $record['extra']['taxs'] = $this->taxs;

        return $record;
    }
}
