<?php

use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Math\MathFactory;
use Famoser\Elliptic\Serializer\PointDecoder\TwEDPointDecoder;
use Famoser\Elliptic\Serializer\SECSerializer;
use Famoser\Elliptic\Validator\CofactorValidator;

require __DIR__ . '/../vendor/autoload.php';

// get curves from SEC, brainpool and bernstein
$repository = new CurveRepository();
$curve = $repository->findByName('edwards25519');
assert($curve !== null);

// hardened (against side-channels) math implemented for most curves
$mathFactory = new MathFactory($repository);
$math = $mathFactory->createHardenedMath($curve);
assert($math !== null);

// can do double, add and mul
$G2 = $math->double($curve->getG());
$G3 = $math->add($curve->getG(), $G2);
$GA = $math->mul($curve->getG(), gmp_init(3));
if ($G3->equals($GA)) {
    echo "success\n";
}

// serialization and deserialization
$serializer = new SECSerializer($math, new TwEDPointDecoder($curve));
$serializedPoint = $serializer->serialize($curve->getG());
$recoveredPoint = $serializer->deserialize($serializedPoint);

// validation
$validator = new CofactorValidator($math);
if ($validator->isValidPoint($recoveredPoint)) {
    echo "success";
}
