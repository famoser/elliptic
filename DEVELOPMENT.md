# Development

Notes for continued development.

Validation features:
- check for canonical encoding? -> provide util for this, but do not enforce

Until full release:
- add cofactor test for curve448
- generalize isInfinity test to handle that curve448 is not in 0-torsion
- look at root case for failing test, likely bidirectional map [cofactor](https://crypto.stackexchange.com/a/101800)
- provide mul over N (instead of N*H), consider providing other mul, too
- fix edwards computation for curve448
- check GMP is responsible for 0-muls faster than random-muls

Next features:
- add ristretto
- add weil pairing
