<?php

use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Math\MathFactory;

require __DIR__ . '/../vendor/autoload.php';

// get curves from SEC, brainpool and bernstein
$repository = new CurveRepository();
$curve = $repository->findByName('secp256r1');

// hardened (against side-channels) math implemented for most curves
$mathFactory = new MathFactory($repository);
$math = $mathFactory->createHardenedMath($curve);

// can do double, add and mul
$G2 = $math->double($curve->getG());
$G3 = $math->add($curve->getG(), $G2);
$GA = $math->mul($curve->getG(), gmp_init(3));
if ($G3->equals($GA)) { echo "success"; }
