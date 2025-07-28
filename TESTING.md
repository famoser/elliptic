# Testing

Testing Targets:
- Third-party integration tests for all curves [done]
- 100% branch coverage for the crypto code using unit tests [in progress]
- Timing tests for all curves (e.g. see [here](https://github.com/bleichenbacher-daniel/Rooterberg/issues/2)).

## Integration tests

For the integration tests, we aim for a broad set of test vectors from different sources. While this libraries' target is to provide elliptic curve math, not crypto primitives, we nonetheless use integration tests from the crypto domain, as there are plenty available.

There are two kind of integration tests at the moment:
- The `WycheProof` integration tests from [C2SP/wycheproof](https://github.com/C2SP/wycheproof). It aims to explore edge cases in implementations, both on the crypto primitive layer (which we do not care about), but also on the elliptic curve math layer (which we do care about). The project seems to be [abandoned)(https://github.com/C2SP/wycheproof/issues/113#issuecomment-2610184843), and the original maintainer started a new project called [Rooterberg](https://github.com/bleichenbacher-daniel/Rooterberg).
- The `Rooterberg` integration tests from [bleichenbacher-daniel/Rooterberg](https://github.com/bleichenbacher-daniel/Rooterberg). Same target as `WycheProof` by the same original maintainer.

| Curve            | WycheProof        | Rooterberg |
|------------------|-------------------|------------|
| `secp192k1`      | ecdsa(190)        |            |
| `secp192r1`      | ecdsa(192)        |            |
| `secp224k1`      |                   | ecdsa(384) |
| `secp224r1`      |                   | ecdsa(384) |
| `secp256k1`      | ecdh(238)         |            |
| `secp256r1`      | ecdh_ecpoint(216) |            |
| `secp384r1`      | ecdh_ecpoint(182) |            |
| `secp521r1`      | ecdh_ecpoint(237) |            |
| `brainpool192r1` |                   | ecdsa(375) |
| `brainpool192t1` |                   | ecdsa(377) |
| `brainpool224r1` | ecdh(241)         |            |
| `brainpool224t1` |                   | ecdsa(406) |
| `brainpool256r1` | ecdh(288)         |            |
| `brainpool256t1` |                   | ecdsa(534) |
| `brainpool320r1` | ecdh(189)         |            |
| `brainpool320t1` |                   | ecdsa(431) |
| `brainpool384r1` | ecdh(128)         |            |
| `brainpool384t1` |                   | ecdsa(428) |
| `brainpool512r1` | ecdh(184)         |            |
| `brainpool512t1` |                   | ecdsa(741) |

For the `WycheProof`, it seems to be the case that the more abstract testsuites include all the primitive tests, plus some new tests corresponding to the abstraction. As an example, `ecdh_ecpoint` and `ecdh` contain the same tests, while `ecdh` contains additional tests concerning the DER public key deserialization. As we do not care about the abstract functionality, we aim to execute the most primitive available test suite per curve. Concretely, we prefer `ecdh_ecpoint`, if not available then `ecdh`, if not available `ecdsa`.

The `Rooterberg` testset is used for the curves that did not have tests in the Wycheproof. Its file format is not yet stable, hence this is potentially a maintenance burden.  


## Timing tests

To test for side channels, we check whether the following distributions are equal: a) multiplying with a constant value and b) multiplying with always a different random value. The details are documented in the jupiter lab file at `tests/consttime/analyse.ipynb`. 

The results are as follows:
- `SWUnsafeMath` is not const time (as expected)
- `SW_ANeg3_Math` is const time for a reasonable same size (tested with 1000 samples)
- `SW_QT_ANeg3_Math` cannot be shown to be const time, likely because the inversion operation of GMP is not const time (inversion is needed when applying the twist)

## Open Questions

Correctness:
- In the JacobiCoordinator, how to handle a non-invertible Z properly? (and in the bilinear mappings)
- Reducing factors before montgomery ladder; correct to reduce by N*h?
- MG_ED math is wrong; "better" if cofactor is set to 1. Why & what could be the problem?
