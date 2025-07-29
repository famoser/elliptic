# Elliptic: Low-level Elliptic Curve Library

[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE)
[![Test](https://github.com/famoser/elliptic/actions/workflows/integration.yml/badge.svg)](https://github.com/famoser/elliptic/actions/workflows/integration.yml)
[![Coverage Status](https://coveralls.io/repos/github/famoser/elliptic/badge.svg?branch=main)](https://coveralls.io/github/famoser/elliptic)

This library provides low-level access to elliptic curve group computations.

```php
// get curves from SEC, brainpool and bernstein
$repository = new CurveRepository();
$curve = $repository->findByName('secp256r1');

// hardened (against side-channels) math for most curves
$mathFactory = new MathFactory($repository);
$math = $mathFactory->createHardenedMath($curve);

// can do double, add and mul
$G2 = $math->double($curve->getG());
$G3_ = $math->add($curve->getG(), $G2);
$G3 = $math->mul($curve->getG(), gmp_init(3));
if ($G3->equals($G3_)) { echo "success"; }
```
- Provide low-level library that executes math on elliptic curves (this project)
- Provide elliptic-crypto library which exposes general cryptographic primitives (signatures, encryptions and zero-knowledge proofs)
- Provide more specialized libraries for more exotic cryptographic primitives (verifiable shuffle)

If you are looking for a project that provides cryptographic primitives, you might want to look into `phpecc/phpecc` (resp. its recommended replacement by [packagist](https://github.com/phpecc/phpecc/issues/289#issuecomment-2075703542) at `paragonie/phpecc`). 

