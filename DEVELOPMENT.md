# Development

Notes for continued development.

Until full release:
- provide mul over N (instead of NH). this gets rid of cofactor complexity for normal users. users that want to multiply f \in NH can use mulH() first, then mul(f/H). note that f % H != 0 is not a use-case.
- RFC7784: provide util to detect non-canonical encoding.
- add ristretto
- consider debugging curve448, but curve is less used in practice, hence less relevant.
- reexecute the hardening measure tests for all implementations. 
- recheck all ignored tests.

Curve448 issues:
- MG_ED_Math is incorrect: double/add are different compared to MGUnsafeMath. Are projective coordinates incorrect?
- the generator of curve448 is not in torsion group of (0,1). why not? is this an issue for the montgomery ladder? how to integrate the cofactor test sensibly?

Next major milestone:
- add weil pairing
- add SEC deprecated curves (kobliz), as sometimes still used
