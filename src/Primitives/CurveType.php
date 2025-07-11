<?php

namespace Famoser\Elliptic\Primitives;

enum CurveType
{
    // curves of the form y^2 = x^3 + ax + b
    case ShortWeierstrass;

    // curves of the form by^2 = x^3 + ax^2 + x
    case Montgomery;

    // curves of the form ax^2 + y^2 = 1 + d*x^2*y^2
    case TwistedEdwards;
}
