<?php

namespace Famoser\Elliptic\ConstTime\Collectors;

use Famoser\Elliptic\Integration\Rooterberg\FixturesRepository;
use Famoser\Elliptic\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Integration\WycheProof\Utils\WycheProofConstants;
use Famoser\Elliptic\Math\MathInterface;

interface ProofCollectorInterface
{
    public function getCurveName(): string;
    public function getMathName(): string;

    public function collect(): void;

    public function store(): array;
}
