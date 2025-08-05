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

Compared to the popular `paragonie/phpecc`, this library focuses on the math on the elliptic curves directly, and does not implement any cryptographic primitives. 
For this task, this library is around 100x [sic!] faster, while not performing significantly worse in terms of side-channels.

## Math overview

There are two types of math:
- Unsafe implementations (`$repository->createUnsafeMath($curve)`): Simply implement the addition and double formulas for the given curve type (`$repository->createUnsafeMath($curve)`).
- Hardened implementations (`$repository->createHardenedMath($curve)`): Follow specifications and RFCs that notably aim to reduce the effectiveness of side-channels (side-channels may allow an adversary to recover the input to the algorithms, e.g. private key material, by observing the environment, e.g. time and power usage of the CPU).

All curves, except the `secp*k1` and the `brainpool*r1` variants, have hardened implementations available. Unless you have a good reason, you should use these hardened implementations.

| Hardened Math                | Supported Curves                                       | Correctness         | Hardened                    | Speed |
|------------------------------|--------------------------------------------------------|---------------------|-----------------------------|-------|
| `SW_ANeg3_Math`              | `secp*r1`, `brainpool*t1`                              | :white_check_mark:  | :warning::warning:          | 4     |
| `SW_QT_ANeg3_Math`           | `brainpool*r1`                                         | :white_check_mark:  | :warning::warning:          | 4     |
| `MGXCalculator` (`mul` only) | `curve25519`, `curve448`                               | :white_check_mark:  | :warning::warning::warning: | 1     |
| `MG_TwED_ANeg1_Math`         | `curve25519`                                           | :warning:           | :warning:                   | 2.5   |
| `MG_ED_Math`                 | `curve448`                                             | :x:                 | :grey_question:             | 2     |
| `TwED_ANeg1_Math`            | `edwards25519`                                         | :warning:           | :grey_question:             | 2.5   |
| `EDMath`                     | `edwards448`, `curve448Edwards`                        | :white_check_mark:  | :grey_question:             | 2     |

Correctness:
- `MG_ED_Math` passes math sanity, but performs incorrectly in relation to baselines (e.g. third party testcases).
- `MG_TwED_ANeg1_Math` and `TwED_ANeg1_Math` perform correctly based on third-party testcases, but math sanity (e.g. G*order = 0) fails.

Hardened:
- No implementation can be shown constant-time, and other side-channels are not quantitively assessed.
- Implementations finish faster with adversarial input (0 very small points and factors) vs random input.
- Unsafe maths show 50% variance in execution time, hardened math between 3% (`MG_TwED_ANeg1_Math`) and 15% (`MGXCalculator`)

Speed:
- Denoted is execution time of mul in some unit; hence higher is worse.
- `MGXCalculator` performs best, but is no full math implementation (no `double`, no `add`).
- The unsafe variants of SW are 2x faster, the unsafe variant of MG is 1.5x faster.


## Project context

This library is part of a larger effort:
- Provide low-level library that executes math on elliptic curves (this project)
- Provide elliptic-crypto library which exposes general cryptographic primitives (signatures, encryptions and zero-knowledge proofs)
- Provide more specialized libraries for more exotic cryptographic primitives (verifiable shuffle)

