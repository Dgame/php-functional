<?php

namespace Dgame\Functional\ADT;

/**
 * Interface MatchInterface
 * @package Dgame\Functional\ADT
 */
interface MatchInterface
{
    /**
     * @param array $cases
     */
    public function matchFirst(array $cases): void;
}
