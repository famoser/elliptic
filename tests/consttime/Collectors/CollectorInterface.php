<?php

namespace Famoser\Elliptic\Tests\ConstTime\Collectors;

use Famoser\Elliptic\Tests\Integration\Rooterberg\FixturesRepository;
use Famoser\Elliptic\Tests\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Tests\Integration\WycheProof\Utils\WycheProofConstants;
use Famoser\Elliptic\Math\MathInterface;

interface CollectorInterface
{
    public function getCurveName(): string;
    public function getMathName(): string;

    public function collect(): void;

    public function store(): array;
}
