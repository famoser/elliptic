# Elliptic: Low-level Elliptic Curve Library

[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE)
[![Test](https://github.com/famoser/elliptic/actions/workflows/integration.yml/badge.svg)](https://github.com/famoser/elliptic/actions/workflows/integration.yml)
[![Coverage Status](https://coveralls.io/repos/github/famoser/elliptic/badge.svg?branch=main)](https://coveralls.io/github/famoser/elliptic)

This library provides low-level access to elliptic curve group computations.
Extensively tested (100% branch coverage, 9k third-party integration tests) and hardened against side-channels.

```php
// get curves from SEC, brainpool and bernstein
$repository = new CurveRepository();
$curve = $repository->findByName('secp256r1');

// hardened (against side-channels) math for most curves
$mathFactory = new MathFactory($repository);
$math = $mathFactory->createHardenedMath($curve);

// can do double, add and mul
$G2 = $math->double($curve->getG());
$G3 = $math->add($curve->getG(), $G2);
$GA = $math->mul($curve->getG(), gmp_init(3));
if ($G3->equals($GA)) { echo "success"; }
```

Functionality overview:
- Curves: Use `SEC2`, `brainpool` and the bernstein curves (`curve25519` and `curve448`).
- Math: Operate on the curves using `add`, `double` and `mul`.
- Decoders: Decode according to SEC or bernstein. Recover points given only an x or y coordinate.


## Math overview

All curves, except the `secp*k1` and the `brainpool*r1` variants, have hardened implementations available. The hardening aims to reduce the effectiveness of side-channels. Side-channels may allow an adversary to recover the private key indirectly, by observing the timing, the cache behaviour or the power consumption of the algorithm execution. Unless you have a good reason, you should use these hardened implementations.

| Hardened Math                | Supported Curves                                       | Correctness         | Speed | Hardened |
|------------------------------|--------------------------------------------------------|---------------------|-------|----------|
| `SW_ANeg3_Math`              | `secp*r1`, `brainpool*t1`                              | :white_check_mark:  |       |          |
| `SW_QT_ANeg3_Math`           | `brainpool*r1`                                         | :white_check_mark:  |       |          |
| `MGXCalculator` (`mul` only) | `curve25519`, `curve448`                               | :white_check_mark:  |       |          |
| `MG_TwED_ANeg1_Math`         | `curve25519`                                           | :warning:           |       |          |
| `MG_ED_Math`                 | `curve448`                                             | :x:                 |       |          |
| `TwED_ANeg1_Math`            | `edwards25519`                                         | :warning:           |       |          |
| `EDMath`                     | `edwards448`, `curve448Edwards`                        | :white_check_mark:  |       |          |

Correctness:
- `MG_TwED_ANeg1_Math` and `TwED_ANeg1_Math` perform correctly based on third-party testcases, but math sanity checks fail (e.g. G*order != 0)
- `MG_ED_Math` pass math sanity checks, but performs incorrectly in relation to baselines (e.g. third party testcases)



## Project context

This library is part of a larger effort:
- Provide low-level library that executes math on elliptic curves (this project)
- Provide elliptic-crypto library which exposes general cryptographic primitives (signatures, encryptions and zero-knowledge proofs)
- Provide more specialized libraries for more exotic cryptographic primitives (verifiable shuffle)

If you are looking for a project that provides cryptographic primitives, you might want to look into `phpecc/phpecc` (resp. its recommended replacement by [packagist](https://github.com/phpecc/phpecc/issues/289#issuecomment-2075703542) at `paragonie/phpecc`). 

