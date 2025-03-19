# Elliptic: Low-level Elliptic Curve Library

This library provides low-level access to elliptic curve computations. It is based on `phpecc/phpecc`, but focuses purely on elliptic curve computations (hence does not provide cryptographic primitives or structures).

This is a work in progress. Targets:
- Reduce library to purely expose elliptic curve math
- Re-vet and document all algorithms
- Add brainpool & ed25519 curves

This is part of a larger effort:
- Provide low-level library that executes math on elliptic curves (this project)
- Provide elliptic-crypto library which exposes general cryptographic primitives (signatures, encryptions and zero-knowledge proofs)
- Provide more specialized libraries for more exotic cryptographic primitives (verifiable shuffle)


## Why

This library is a fork from `phpecc/phpecc`, which was itself was based on `mdanter/ecc` (from Matyas Danter, which currently 404s). `phpecc/phpecc` is unfortunately no longer maintained, and the original maintainer (afk11) seems to be unreachable. 

There is another fork that took over maintenance from `phpecc/phpecc` called `paragonie/phpecc`. It explained its reasons in a [public mail](https://www.openwall.com/lists/oss-security/2024/04/24/4), and is the recommended replacement by [packagist](https://github.com/phpecc/phpecc/issues/289#issuecomment-2075703542). It extended the library, with the following core contributions:
- Introduce `SecureCurveFactories` which prevent instantiation of insecure curves
- Harden signatures (mitigate malleable ECDSA signatures, work towards constant time math)
- Use OpenSSL signature creation and verification when available for the respective curve
- Introduce optimized variants of curves, with "optimized" implying hardening against side-channels
- Add brainpool curves

The target of this project is different: It provides expert users a way to interact with elliptic curves, for them to build their own algorithms on top of it. Out of scope is therefore guidance what curves to choose, and all higher-layer algorithms (hence no signatures, encryptions or similar). 


## Next steps

Major steps done:
- Rewrite integration testing infrastructure
- Define new structure of curves, math and serializer 
- Implement `UnsafeMath`; a best-effort generic implementation

Major next steps:
- Migrate `Spec` and `WycheProof` integration tests to new curves / math
- Remove all crypto primitives no longer used, together with their unit tests
- Write unit tests for the new structures
- Add constant time math for specific curves
- Add brainpool, ed25519 curves
