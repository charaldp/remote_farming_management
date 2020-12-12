class Phys {
  constructor ( gravity, frictionCoeff, timescale, objects ) {
    this._gravity = gravity;
    this._frictionCoeff = frictionCoeff;
    this._timescale = timescale !== 'undefined' ? timescale : 1;
  }

  static clutchSigmoidFrictionCoeff( x, sigma_sq /* 15 */, synchronizationCoeff ) {
    return ( 1 + 10 * Phys.distributionFunction( 1, 0.05, synchronizationCoeff ) ) * Phys.activationFunction( x, 0.5, sigma_sq );
  }

  static activationFunction( x, center, sigma_sq /* 15 */ ) {
    return 1 / ( 1 + Math.exp( - sigma_sq * (x - center ) ) )
  }

  static distributionFunction( center, sigma_sq, x ) {
    return Math.exp( - Math.pow( Math.abs(x - center), 2 ) / 2 / sigma_sq ) / Math.sqrt( 2 * Math.PI * sigma_sq );
  }
}
export default {Phys};
