class VesselUtils {

  constructor ( dimDiv ) {
    this.dimDiv = dimDiv;
  }

  static scaleParams( args, dimDiv ) {
      for (var i = 0; i < args.length; i++ ) {
        if ( args[i] instanceof Array ) {
            for (let j = 0; j < args[i].length; j++ )
              [args[i][j]] = VesselUtils.scaleParams( [args[i][j]], dimDiv );
        } else if ( args[i] instanceof Object ) {
          for ( var property in args[i] )
            if ( args[i].hasOwnProperty(property) && !( args[i][property] instanceof Boolean ) && !( property.search('num') != -1 || property.search('Angle') != -1 || property.search('Type') != -1 ) )
              [args[i][property]] = VesselUtils.scaleParams( [args[i][property]], dimDiv );//= dimDiv;
        } else {
          args[i] /= dimDiv;
        }
      }
      return args;
  }

  static printInfo( args ) {
    var string = '';
    for (var property in args) {
      string += property + ' : '
      if ( args[property] instanceof Array ) {
          string += ' [ ';
          for (let i = 0; i < args[property].length; i++ ) {
            string += args[property][i] + (i < args[property].length - 1 ? ',' : '') + ' ';
          }
          string += ']\n';
      } else if ( args[property] instanceof Object ) {
        string += ''
        for (var property2 in args[property])
          if (args[property].hasOwnProperty(property2)) {
            string += '\n\t' + property2 + ' : ' + args[property][property2];
          }
        string += '\n';
      } else {
        string += args[property] + '\n';
      }
    }
    return string;
  }

  static mergeGroupChildren( geo, aGroup, matArray ) {
    for (var i = 0; i < aGroup.children.length;i++) {
      if ( aGroup.children[i].isMesh ) {
        var tempGeo = new THREE.Geometry();
        tempGeo.mergeMesh( aGroup.children[i].clone() );
        for (var j = 0; j < matArray.length; j++) {
          var str = matArray[j][6].split('');
          for (var k = 0; k < 3; k++) {
            switch (str[k]) {
              case 'X':
                tempGeo.rotateX(matArray[j][0]);
                break;
              case 'Y':
                tempGeo.rotateY(matArray[j][1]);
                break;
              case 'Z':
                tempGeo.rotateZ(matArray[j][2]);
            }
          }
          tempGeo.translate(matArray[j][3], matArray[j][4], matArray[j][5]);
        }
        geo.merge(tempGeo.clone());
      } else {

        // Save the children's transform
        matArray.unshift([aGroup.children[i].rotation.x, aGroup.children[i].rotation.y, aGroup.children[i].rotation.z,
                          aGroup.children[i].position.x, aGroup.children[i].position.y, aGroup.children[i].position.z ,aGroup.children[i].rotation.order]);
        // Recursive Call	on Group's children
        VesselUtils.mergeGroupChildren( geo, aGroup.children[i], matArray );

      }
    }
  }

  static computeSingleGeometry( group ) {
    var geo = new THREE.Geometry();
    if (group.children.length > 1 && group.children[0].isGroup) {
      for (var i = 0; i < group.children.length; i++) {
        // Use the group's transforn then the current subgroup's transfrom
        VesselUtils.mergeGroupChildren( geo, group.children[i], [[group.children[i].rotation.x, group.children[i].rotation.y, group.children[i].rotation.z,
          group.children[i].position.x, group.children[i].position.y, group.children[i].position.z, group.children[i].rotation.order],
          [group.rotation.x, group.rotation.y, group.rotation.z,
            group.position.x, group.position.y, group.position.z, group.rotation.order]] );
      }
    } else {
      // Use the group's transforn
      VesselUtils.mergeGroupChildren( geo, group, [[group.rotation.x, group.rotation.y, group.rotation.z,
        group.position.x, group.position.y, group.position.z, group.rotation.order]] );
    }
    return geo;
  }
}
