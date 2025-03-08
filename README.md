## Elliptic: Low-level Elliptic Curve Library

This library provides low-level access to elliptic curve computations. 

This is a work in progress.

The library is based on `phpecc/phpecc`, but focuses purely on elliptic curve computations (hence does not provide cryptographic primitives or structures). 

## Why

This library is a fork from `phpecc/phpecc`, which was itself was based on `mdanter/ecc` (from Matyas Danter, which currently 404s). `phpecc/phpecc` is unfortunately no longer maintained, and the original maintainer (afk11) seems to be unreachable. 

There is another fork that took over maintenance from `phpecc/phpecc` called `paragonie/phpecc`. It explained its reasons in a [public mail](https://www.openwall.com/lists/oss-security/2024/04/24/4), and is the recommended replacement by [packagist](https://github.com/phpecc/phpecc/issues/289#issuecomment-2075703542). It extended the library, with the following core contributions:
- Introduce `SecureCurveFactories` which prevent instantiation of insecure curves
- Harden signatures (mitigate malleable ECDSA signatures, work towards constant time math)
- Use OpenSSL signature creation and verification when available for the respective curve
- Introduce optimized variants of curves, with "optimized" implying hardening against side-channels
- Add brainpool curves

The target of this project is different:
- Acceptance that GMP itself has no constant time guarantees (and neither the PHP interpreter nor its JIT). Out of scope is therefore hardening towards constant time, as fundamentally through the usage of GMP the guarantees will remain unclear.
- Provide expert users a way to interact with elliptic curves, for them to build their own algorithms on top of it. Out of scope is therefore guidance what curves to choose, and all higher-layer algorithms (hence no signatures, encryptions or similar). 
