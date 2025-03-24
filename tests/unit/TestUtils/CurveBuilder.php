<?php

namespace Famoser\Elliptic\Tests\TestUtils;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;

class CurveBuilder
{
    private CurveType $type;
    private \GMP $a;

    public function __construct(private readonly Curve $template)
    {
        $this->type = $this->template->getType();
        $this->a = $this->template->getA();
    }

    public function withType(CurveType $type): CurveBuilder
    {
        $this->type = $type;

        return $this;
    }

    public function withA(\GMP $a): CurveBuilder
    {
        $this->a = $a;

        return $this;
    }

    public function build(): Curve
    {
        return new Curve($this->type, $this->template->getP(), $this->a, $this->template->getB(), $this->template->getG(), $this->template->getN(), $this->template->getH());
    }
}
