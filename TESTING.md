# Testing

Testing Targets:
- Correctness: Third-party integration tests for all curves
- 100% branch coverage for the crypto code using unit tests
- Timing tests for all curves (e.g. see [here](https://github.com/bleichenbacher-daniel/Rooterberg/issues/2)).

## Correctness

Status:
- 100% branch coverage
- Math soundness tests (e.g. G*order = 0)
- Third-party integration tests

Correctness exceptions:
- `MG_ED_MathTest` outputs different results than the baseline (i.e. is wrong)
- `TwED_ANeg1_Math` and `MG_TwED_ANeg1_Math` do not cycle as expected (i.e. G*order != 0)

### Integration tests

There are two kind of integration tests at the moment:
- The `WycheProof` integration tests from [C2SP/wycheproof](https://github.com/C2SP/wycheproof). It aims to explore edge cases in implementations, both on the crypto primitive layer (which we do not care about), but also on the elliptic curve math layer (which we do care about). The project seems to be [abandoned)(https://github.com/C2SP/wycheproof/issues/113#issuecomment-2610184843), and the original maintainer started a new project called [Rooterberg](https://github.com/bleichenbacher-daniel/Rooterberg).
- The `Rooterberg` integration tests from [bleichenbacher-daniel/Rooterberg](https://github.com/bleichenbacher-daniel/Rooterberg). Same target as `WycheProof` by the same original maintainer.

We choose which testset to execute as follows, mainly to ease maintenance:
- We prefer `WycheProof` over `Rooterberg`, as the latter is still experimental, and its file format might change. 
- We prefer the testset as closest to the elliptic curve as possible (i.e. we prefer `ecdh_ecpoint` over `ecdh` over `ecdsa`)

It holds that:
- Over every curve, at least one testset is executed
- Over every math, at least one testset is executed (unless noted otherwise)

Exceptions:
- No direct test of `edwards25519` (but tested implicitly by `MG_TwED_ANeg1_Math`)
- No tests for `curve448Edwards` and `edwards448`
- No tests for `EDMath`, `EDUnsafeMath` and `TwEDUnsafeMath`, `TwED_ANeg1_Math` 
- Test of `MG_ED_Math` fails

| Curve            | Math                 | WycheProof        | Rooterberg |
|------------------|----------------------|-------------------|------------|
| `secp192k1`      | `SWUnsafeMath`       | ecdsa(190)        |            |
| `secp192r1`      | `SW_ANeg3_Math`      | ecdsa(192)        |            |
| `secp224k1`      | `SWUnsafeMath`       |                   | ecdsa(384) |
| `secp224r1`      | `SW_ANeg3_Math`      |                   | ecdsa(384) |
| `secp256k1`      | `SWUnsafeMath`       | ecdh(238)         |            |
| `secp256r1`      | `SW_ANeg3_Math`      | ecdh_ecpoint(216) |            |
| `secp384r1`      | `SW_ANeg3_Math`      | ecdh_ecpoint(182) |            |
| `secp521r1`      | `SW_ANeg3_Math`      | ecdh_ecpoint(237) |            |
| `brainpool192r1` | `SW_QT_ANeg3_Math`   |                   | ecdsa(375) |
| `brainpool192t1` | `SW_ANeg3_Math`      |                   | ecdsa(377) |
| `brainpool224r1` | `SW_QT_ANeg3_Math`   | ecdh(241)         |            |
| `brainpool224t1` | `SW_ANeg3_Math`      |                   | ecdsa(406) |
| `brainpool256r1` | `SW_QT_ANeg3_Math`   | ecdh(288)         |            |
| `brainpool256t1` | `SW_ANeg3_Math`      |                   | ecdsa(534) |
| `brainpool320r1` | `SW_QT_ANeg3_Math`   | ecdh(189)         |            |
| `brainpool320t1` | `SW_ANeg3_Math`      |                   | ecdsa(431) |
| `brainpool384r1` | `SW_QT_ANeg3_Math`   | ecdh(128)         |            |
| `brainpool384t1` | `SW_ANeg3_Math`      |                   | ecdsa(428) |
| `brainpool512r1` | `SW_QT_ANeg3_Math`   | ecdh(184)         |            |
| `brainpool512t1` | `SW_ANeg3_Math`      |                   | ecdsa(741) |
| `curve25519`     | `MG_TwED_ANeg1_Math` | x25519(294)       |            |
| `curve25519`     | `MGUnsafeMath`       | x25519(294)       |            |
| `curve25519`     | `MGXCalculator`      | x25519(518)       |            |
| `curve448`       | `MGUnsafeMath`       | x25519(264)       |            |
| `curve448`       | `MGXCalculator`      | x25519(510)       |            |


## Timing tests

To test for side channels, we check whether the following distributions are equal: a) multiplying with a constant value and b) multiplying with always a different random value. The details are documented in the jupiter lab file at `tests/consttime/analyse.ipynb`. 

The results are as follows:
- `SWUnsafeMath` is not const time (as expected)
- `SW_ANeg3_Math` is const time for a reasonable same size (tested with 1000 samples)
- `SW_QT_ANeg3_Math` cannot be shown to be const time, likely because the inversion operation of GMP is not const time (inversion is needed when applying the twist)

## Open Questions

Correctness:
- In the JacobiCoordinator, how to handle a non-invertible Z properly? (and in the bilinear mappings)
- In the Coordinators, how to detect/handle infinity?
- Reducing factors before montgomery ladder; correct to reduce by N*h?
- MG_ED math is wrong; "better" if cofactor is set to 1. Why & what could be the problem?

