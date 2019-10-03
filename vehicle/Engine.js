class Engine {
  constructor ( shaft_inertia, rev_limit, idle_rot, maximum_hp, clutch ) {
    this._isOn = false;
    this._rot = 0;
    this._idle_rot = idle_rot;
    this._rev_limit = rev_limit;
    this._shaft_inertia = shaft_inertia;
    this._currentPower = 0;
    this._currentTorque = 0;
    this._maximumHP = maximum_hp;
    this._load_inertia = 0;
    this._clutch = clutch;
  }

  updateEngineState( throttle, timestep, handleLoadState ) {
    if (this._rot <= 0) this._rot = 0.001;
    this._currentPower = ( throttle * Math.pow(this._rot / this._idle_rot, 1.2 ) - Math.pow(this._rot / this._idle_rot, 1.4)) * this._maximumHP * this._idle_rot / this._rev_limit;
    // if ( this._currentPower > this._maximumHP ) this._currentPower = this._maximumHP;
    this._currentTorque = this._rot < 2 ? (throttle > 1.2 ? 50 : (this._rot < 0 ? 0 : -1000)) : 118.675 * this._currentPower / this._rot;
    this._rot += timestep * this._currentTorque / (this._shaft_inertia + ( handleLoadState ? this._load_inertia : 0 )  );// N * m / ( kg * m ^ 2 ) = N / (kg * m);
    // console.log(this._currentPower, this._currentTorque, this._shaft_inertia, this._load_inertia, this._rot, this._clutch, timestep);
    if (this._rot > this._rev_limit) this._rot -= 2 * Math.random() * (this._rot - this._rev_limit);
    if (this._rot <= 0) this._rot = 0.001;
  }


}
