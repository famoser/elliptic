<?php

namespace Famoser\Elliptic\Tests\Math;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Math\ED_Math;
use Famoser\Elliptic\Primitives\Point;
use PHPUnit\Framework\TestCase;

class ED_MathTest extends TestCase
{
    public function testSampleValues()
    {
        $curve = BernsteinCurveFactory::curve448Edwards();
        $math = new ED_Math($curve);

        $actualAdd = $math->add($curve->getG(), $curve->getG());
        $actualDouble = $math->double($curve->getG());
        $expected = new Point(
            gmp_init('532197462714747127433329576807071134107497244344845390447372466034459372412525732889812214919932149868057668795216741879196520154547279', 10),
            gmp_init('115320072469748031025935616778501987543283109050765169310991720570811047172456575163970160521981879283524072359546498166042398779177527', 10),
        );

        $this->assertObjectEquals($expected, $actualDouble);
        $this->assertObjectEquals($expected, $actualAdd);
    }
}
