# Elliptic: Low-level Elliptic Curve Library

[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE)
[![Test](https://github.com/famoser/elliptic/actions/workflows/test.yml/badge.svg)](https://github.com/famoser/elliptic/actions/workflows/test.yml)
[![Coverage Status](https://coveralls.io/repos/github/famoser/elliptic/badge.svg?branch=safe-math)](https://coveralls.io/github/famoser/elliptic?branch=safe-math)

This library provides low-level access to elliptic curve group computations.

This is a work in progress. Targets:
- [done] Expose elliptic curves and primitive math operations
- Add hardened implementations for some selected curves
- Add brainpool & ed25519 curves

This is part of a larger effort:
- Provide low-level library that executes math on elliptic curves (this project)
- Provide elliptic-crypto library which exposes general cryptographic primitives (signatures, encryptions and zero-knowledge proofs)
- Provide more specialized libraries for more exotic cryptographic primitives (verifiable shuffle)

If you are looking for a project that provides cryptographic primitives, you might want to look into `phpecc/phpecc` (resp. its recommended replacement by [packagist](https://github.com/phpecc/phpecc/issues/289#issuecomment-2075703542) at `paragonie/phpecc`). 

