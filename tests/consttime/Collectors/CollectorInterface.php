<?php

namespace Famoser\Elliptic\Tests\ConstTime\Collectors;

interface CollectorInterface
{
    public function getCurveName(): string;
    public function getMathName(): string;

    public function collect(): void;

    public function store(): array;
}
