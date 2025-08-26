# Testing

The testing is done across multiple dimensions:
- Correctness: Check whether the implementation is correct, notably in relation to math sanity checks and third-party test vectors.
- Hardened: Check whether the implementation might leak the private key to an adversary over side-channels, notably by assessing constant-time execution.
- Runtime: Check how fast the essential operations are executed on the curve.

The implementation is in PHP, using the GMP extension for the big-number-math. This inherently limits what is possible in terms of hardening and performance. If these are of prime concern, switch to a mature implementation closer to the metal (e.g. `openssl`).



## Correctness

Overview:
- 100% branch coverage (some exceptions apply around string functions and match statements)
- Math soundness tests (e.g. G*order = 0)
- Math baseline tests (i.e. comparing hardened math output to baseline math)
- Third-party integration tests 

Result:
- Over every curve and on every math, all these tests are executed successfully
- Exception: Math soundness tests of `TwED_ANeg1_Math` and `MG_TwED_ANeg1_Math` fail (but integration tests succeed)
- Exception: Third-party integration tests of `MG_ED_Math` fail (hence `curve448Edwards` remains untested, too)


### Third-Party Integration tests

There are two kinds of integration tests at the moment:
- The `WycheProof` integration tests from [C2SP/wycheproof](https://github.com/C2SP/wycheproof). It aims to explore edge cases in implementations, both on the crypto primitive layer (which we do not care about), but also on the elliptic curve math layer (which we do care about). The project seems to be [abandoned)(https://github.com/C2SP/wycheproof/issues/113#issuecomment-2610184843), and the original maintainer started a new project called [Rooterberg](https://github.com/bleichenbacher-daniel/Rooterberg).
- The `Rooterberg` integration tests from [bleichenbacher-daniel/Rooterberg](https://github.com/bleichenbacher-daniel/Rooterberg). Same target as `WycheProof` by the same original maintainer.

We choose which testset to execute as follows, mainly to ease maintenance:
- We prefer `WycheProof` over `Rooterberg`, as the latter is still experimental, and its file format might change. 
- We prefer the testset as closest to the elliptic curve as possible (i.e. we prefer `ecdh_ecpoint` over `ecdh` over `ecdsa`)

It holds that:
- Over every curve and on every math, at least one full testset is executed
- Exception: Test of `MG_ED_Math` fails (hence also `curve448Edwards` remains untested)

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
| `ed25519`        | `TwEDUnsafeMath`     | eddsa(145)        |            |
| `ed25519`        | `TwED_ANeg1_Math`    | eddsa(145)        |            |
| `ed448`          | `EDUnsafeMath`       | 448(86)           |            |
| `ed448`          | `EDMath`             | 448(86)           |            |



## Hardened

Overview:
- The implementation is written in PHP and uses GMP for the large-number math. **Neither give any side-channel-free guarantees.**
- The implementation carefully follows RFCs and standards, and hence the algorithms themselves do not trivially introduce side-channels.
- Constant time behaviour, notably corresponding to crafted inputs, is assessed using tests.
- Besides constant time, no other side-channel is explicitly tested for (e.g. caching, power).

Results:
- The implementations are not constant time for crafted inputs (e.g., small points/factors, 2^n factors).
- The hardened implementations perform better (i.e., adversary needs more accurate measurements to detect const-time violations).

### Constant-time analysis

Method:
- Over each math, chosing one of the smaller curves available, we run the third-party test-cases.
- We isolate the math operations (e.g. we do not measure hashing, point decoding, etc).
- We do this multiple times, discarding the 50% slowest runs (we either keep all test-cases of a run, or discard it in its entirety).
- We then cluster the test-cases according to their measured time, using the third-party test-cases to find reasons for non-const behaviour.
- We assess visually the clusters, and document the resolution inside the cluster (i.e. what span a single test-case occupies)

Result summary:
- `UnsafeMath` implementations show clear and multiple clusters for small factors or 0-points
- Hardened implementations show also different clusters for small factors, these are however closer to the main cluster
- Inside the main cluster are also smaller clusters (i.e. the main cluster is also not const-time)

Note: 
`paragonie/phpecc` seems to leak less edge-cases (compared to `SW_QT_ANeg3_Math`).
However, small points remain detectable, hence `paragonie/phpecc` is also not const-time.


#### Hardened Maths

`SW_ANeg3_Math`
- 19.5 - 20 main cluster with resolution of 0.1
- 19.4 for point = 0
- 19 for factor = smallish
- 18 for factor = 0

`SW_QT_ANeg3_Math`:
- 19.6 - 20.1 main cluster with resolution of 0.1
- 19.4 for point with lots of zeros (e.g. 2^128)
- 18.5 for factor = 32 bits
- 17.8 for factor = 0

`MGXCalculator`:
- 6.3 - 6.6 main cluster with resolution of 0.03
- 5.5 for low order u

`MG_TwED_ANeg1_Math`:
- 17.5 - 17.8 main cluster with resolution of 0.2
- 17.3 for public key with low order / many zeros

`TwED_ANeg3_Math`:
- 17.4 - 17.7 main cluster with resolution of 0.1
- No other clusters, but likely because testset is too small / has no special cases

`EDMath`:
- 28.2 - 28.5 main cluster with resolution of 0.2
- No other clusters, but likely because testset is too small / has no special cases


#### Unsafe maths

`SWUnsafeMath`:
- 7.5 - 8 main cluster with resolution 0.1
- 6 for factor = smallish
- 4.5 for factor = 1

`MGUnsafeMath`:
- resolution is very low
- 12.5 main cluster
- 5.2, 7, 9 clear clusters for low order u

`TwEDUnsafeMath`:
- 15 - 15.4 main cluster with resolution of 0.1
- No other clusters, but likely because testset is too small / has no special cases

`EDUnsafeMath`:
- 30.5 - 31 main cluster with resolution of 0.1
- No other clusters, but likely because testset is too small / has no special cases


## Runtime

Method:
- The measurement is done on a ThinkPad X1 Gen11 (no turbo boost, CPU at 1900 Mhz).
- Performed is `mul` over the basepoint using a random factor.
- Outliers are removed (i.e. 25% of the slowest, and 25% of the fastest runs).

Results:
- The Montgomery curves are 2x as fast than the Short Weiherstrass at the same security level.
- Using the `MGXCalculator` (only supports `mul` but no `add` or `double`) it is 4x as fast.
- For Short Weiherstrass, the hardened math is around 2x slower, worse for big curves.
- For Montgomery and Twisted Edwards, the hardened maths is around 20% slower.
- For Edwards, the hardened math is around 20% faster.

Note: 
Compared to `paragonie/phpecc`, the performance is around 100x [sic!] faster. 
The implementation strategy chosen by `phpecc` is more complex, and requires much more managing on the PHP side, and more GMP invocations.
There is a slight positive effect towards hardening (see section before), but `phpecc` can still not be shown to be const-time.


### Short Weiherstrasse with A=-3

|                   | `SWUnsafeMath` | `SW_ANeg3_Math` |
|-------------------|----------------|-----------------|
| `brainpoolP160t1` | 0.060          | 0.155           |
| `brainpoolP192t1` | 0.074          | 0.187           |
| `brainpoolP224t1` | 0.089          | 0.222           |
| `brainpoolP256t1` | 0.103          | 0.251           |
| `brainpoolP320t1` | 0.137          | 0.320           |
| `brainpoolP384t1` | 0.172          | 0.389           |
| `brainpoolP512t1` | 0.250          | 0.541           |
| `secp192r1`       | 0.074          | 0.190           |
| `secp224r1`       | 0.087          | 0.224           |
| `secp256r1`       | 0.104          | 0.252           |
| `secp384r1`       | 0.172          | 0.390           |
| `secp521r1`       | 0.261          | 0.563           |

### Quadratic Twist to Short Weiherstrass with A=-3

|                   | `SWUnsafeMath` | `SW_QT_ANeg3_Math` |
|-------------------|----------------|--------------------|
| `brainpoolP160r1` | 0.060          | 0.156              |
| `brainpoolP192r1` | 0.075          | 0.187              |
| `brainpoolP224r1` | 0.088          | 0.222              |
| `brainpoolP256r1` | 0.103          | 0.251              |
| `brainpoolP320r1` | 0.136          | 0.322              |
| `brainpoolP384r1` | 0.172          | 0.387              |
| `brainpoolP512r1` | 0.249          | 0.537              |

### Short Weiherstrass (general fallback)

|             | `SWUnsafeMath` |
|-------------|----------------|
| `secp192k1` | 0.074          |
| `secp224k1` | 0.088          |
| `secp256k1` | 0.103          |


## Montgomery with birational mapping to Twisted Edwards (A=-1)

|              | `MGUnsafeMath` | `MGXCalculator` | `MG_TwED_ANeg1_Math` |
|--------------|----------------|-----------------|----------------------|
| `curve25519` | 0.118          | 0.063           | 0.167                |


## Montgomery with birational mapping to Edwards (A=1)

|            | `MGUnsafeMath` | `MGXCalculator` | `MG_ED_Math` |
|------------|----------------|-----------------|--------------|
| `curve448` | 0.237          | 0.117           | 0.269        |


## Twisted Edwards (A=-1)

|                | `TwEDUnsafeMath` | `TwED_ANeg1_Math` |
|----------------|------------------|-------------------|
| `edwards25519` | 0.147            | 0.168             |


## Edwards

|                   | `EDUnsafeMath` | `EDMath` |
|-------------------|----------------|----------|
| `curve448Edwards` | 0.293          | 0.269    |
| `edwards448`      | 0.294          | 0.269    |



## Open Questions

Correctness:
- In the JacobiCoordinator, how to handle a non-invertible Z properly? (and in the bilinear mappings)
- In the Coordinators, how to detect/handle infinity?
- Reducing factors before montgomery ladder; correct to reduce by N*h?
- MG_ED math is wrong; "better" if cofactor is set to 1. Why & what could be the problem?
- When recovering a point, use jacobi symbol to check for square root (beforehand), or check x² = alpha (afterwards)?

