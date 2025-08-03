<?php

namespace Famoser\Elliptic\Tests\Performance\Collectors;

use Famoser\Elliptic\Primitives\Curve;

abstract class MulGCollector implements CollectorInterface
{
    private array $measurements = [];

    public function __construct(private readonly string $curveName, private readonly Curve $curve, private readonly string $mathName)
    {
    }

    public function getCurveName(): string
    {
        return $this->curveName;
    }

    public function getMathName(): string
    {
        return $this->mathName;
    }

    public function getCollectorId(): string
    {
        return "mulG";
    }


    public function collect(int $iterations): void
    {
        $randomFactors = $this->getRandomFactors($iterations);
        foreach ($randomFactors as $factor) {
            $start = microtime(true);
            $this->runMulG($factor);
            $end = microtime(true);

            $this->measurements[] = $end - $start;
        }
    }

    /**
     * @return \GMP[]
     */
    protected function getRandomFactors(int $iterations): array
    {
        $groupOrder = gmp_mul($this->curve->getN(), $this->curve->getH());

        $result = [];
        for ($i = 0; $i < $iterations; $i++) {
            $result[] = gmp_random_range(0, $groupOrder);
        }

        return $result;
    }

    public function store(): array
    {
        return [
            'math' => $this->mathName,
            'curve' => $this->curveName,
            'measurements' => $this->measurements
        ];
    }

    abstract protected function runMulG(\GMP $factor): void;
}
