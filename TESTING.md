# Testing

Testing Targets:
- 100% branch coverage for the crypto code using unit tests
- Third-party integration tests for all curves

**Both of these targets are not yet fulfilled.**

## Integration tests

For the integration tests, we aim for a broad set of test vectors from different sources. While this libraries' target is to provide elliptic curve math, not crypto primitives, we nonetheless use integration tests from the crypto domain, as there are plenty available.

There are two kind of integration tests at the moment:
- The `Spec` integration tests. This set takes in test values from a variety of sources, but it is unclear whether all values per source have been taken over, or whether some important sources have gone missing. A particularity is that values for the nist and the sepc curves are tracked separately, even though the nist curves are just aliases to the sepc curves. 
- The `WycheProof` integration tests from [C2SP/wycheproof](https://github.com/C2SP/wycheproof). It aims to explore edge cases in implementations, both on the crypto primitive layer (which we do not care about), but also on the elliptic curve math layer (which we do care about). The project seems to be [abandoned)(https://github.com/C2SP/wycheproof/issues/113#issuecomment-2610184843), and the original maintainer started a new project called [Rooterberg](https://github.com/bleichenbacher-daniel/Rooterberg). 

| Curve       | Spec tests                                                                                                                                | WycheProof         |
|-------------|-------------------------------------------------------------------------------------------------------------------------------------------|--------------------|
| `secp192k1` | ecdsa(1), keypairs(6), diffie(1)                                                                                                          | ecdsa(190) |
| `secp192r1` | *denoted as `nistp192`*: diffie(1), pubkey(20+), ecdsa-verify(90+), ecdsa(30+), keypairs(60+), hmac(10)                                   | ecdsa(192)  |
| `secp224k1` |                                                                                                                                           |                    |
| `secp224r1` | *denoted as `nistp256`*: diffie(1), pubkey(20+), ecdsa-verify(90+), ecdsa(30+), keypairs(60+), hmac(10)                                   | ecdh_ecpoint(237)  |
| `secp256k1` | keypairs(5), diffie(1), hmac(4)                                                                                                           | ecdh(208)  |
| `secp256r1` | keypairs(2), diffie(1), hmac(10), *denoted as `nistp256`*: diffie(1), pubkey(20+), ecdsa-verify(90+), ecdsa(30+), keypairs(60+), hmac(10) | ecdh_ecpoint(216)  |
| `secp384r1` | keypairs(2), diffie(1), hmac(10), *denoted as `nistp384`*: diffie(1), pubkey(20+), ecdsa-verify(90+), ecdsa(30+), keypairs(60+), hmac(10) | ecdh_ecpoint(182)  |
| `secp521r1` | *denoted as `nistp521`*: diffie(1), pubkey(20+), ecdsa-verify(90+), ecdsa(30+), keypairs(60+), hmac(10)                                   | ecdh_ecpoint(237)  |

For the `WycheProof`, it seems to be the case that the more abstract testsuites include all the primitive tests, plus some new tests corresponding to the abstraction. As an example, `ecdh_ecpoint` and `ecdh` contain the same tests, while `ecdh` contains additional tests concerning the DER public key deserialization. As we do not care about the abstract functionality, we aim to execute the single most primitive test suite per curve. Concretely, we prefer `ecdh_ecpoint`, if not available then `ecdh`, if not available `ecdsa`.

