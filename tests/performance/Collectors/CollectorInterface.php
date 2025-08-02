<?php

namespace Famoser\Elliptic\Tests\Performance\Collectors;

interface CollectorInterface
{
    public function getCurveName(): string;
    public function getMathName(): string;
    public function getCollectorId(): string;

    public function collect(int $iterations): void;

    public function store(): array;
}
