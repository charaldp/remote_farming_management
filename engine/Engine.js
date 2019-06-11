class Engine {
  constructor ( inertia_tor, rev_limit, idle_rot, maximum_hp ) {
    this._isOn = false;
    this._rot = 0;
    this._idle_rot = idle_rot;
    this._rev_limit = rev_limit;
    this._inertia_tor = inertia_tor;
    this._currentPower = 0;
    this._currentTorque = 0;
    this._maximumHP = maximum_hp;
  }
  updateEngineState( throttle, timestep ) {
    this._currentPower = (throttle * Math.pow(this._rot / this._idle_rot, 1.2) - Math.pow(this._rot / this._idle_rot, 1.3)) * this._maximumHP / Math.pow(this._idle_rot / this._rev_limit, 2) / 10;
    this._currentTorque = this._rot == 0 ? (throttle > 1 ? 2 : 0) : 745.7 * this._currentPower / (this._rot);
    console.log(this._currentPower, this._currentTorque);
    this._rot += this._currentTorque / this._inertia_tor * timestep;// N * m / ( kg * m ^ 2 ) = N / (kg * m);
    if (this._rot > this._rev_limit) this._rot = this._rev_limit;
    if (this._rot < 0) this._rot = 0.1;
  }
  getCurrentPower() {
    return 0;
  }
}
