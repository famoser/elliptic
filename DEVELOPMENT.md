# Development

Notes for continued development.

Debug TwED_ANeg1_Extended_Adder:
- belenios / darek 25519 library (Signal) -> uses same addition / doubling formulas
- darek 25519 library reduces factor by order (not order * cofactor); disadvantage that must check that point is torsion-free. alternatively, could always reduce by order, then dont need to check, but log2(cofactor) longer multiplication ladder.
- implement logic around torsion-free

Next features:
- add ristretto
- add weil pairing
