<?php

namespace App;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use SpatialTrait;

    protected $spatialFields = [
        'body_points',
        'wheel_center_positions'
    ];

    public static $vehicle_example = "{
        \"vehicles\" : [{
          \"type\" : \"Car\",
          \"components\" : {
            \"wheel\" : [{
              \"DO\" : 0.5,
              \"DI\" : 0.43,
              \"t\" : 0.15,
              \"tireType\" : \"Flat\",
              \"tireDims\" : {
                \"DO\" : 0.5,
                \"DI\" : 0.43,
                \"t\" : 0.15
              },
              \"rimType\" : \"Ribs\",
              \"rimDims\" : {
                \"DO\": 0.43,
                \"DI\": 0.4,
                \"t\": 0.15,
                \"intrWidth\":  0.022,
                \"numRibs\": 12,
                \"tRib\": 0.015,
                        \"dRib\": 0.030,
                \"ribsPosition\": 0.12,
                \"axleIntrWidth\": -0.01,
                \"axleDI\": 0.01 ,
                \"axleDO\": 0.08,
                \"tAxle\": 0.02
              },
              \"pressure\" : 0.04,
              \"frictionOptions\" : {
                \"static\" : 1,
                \"sliding\" : 0.8,
                \"rolling\" : 0.01
              },
              \"meshMaterial\" : {
                \"rimMaterialType\" : \"MeshPhysicalMaterial\",
                \"tire\" : {
                  \"colour\": \"0xd7d7d7\",
                  \"roughness\": 0.17,
                  \"metalness\": 0.47,
                  \"reflectivity\": 1,
                  \"clearCoat\": 0.64,
                  \"clearCoatRoughness\": 0.22
                },
                \"tireMaterialType\" : \"MeshPhongMaterial\",
                \"rim\" : {
                  \"shininess\": 50,
                  \"colour\" : \"0x1b1b1b\"
                }
              }
            }],
            \"engine\" : {
              \"shaft_inertia\" : 50,
              \"rev_limit\" : 116.66,
              \"idle_rot\" : 17.5,
              \"maximum_hp\" : 390
            },
            \"clutch\" : {
              \"clutchFrictionCoeff\" : 0
            },
            \"transmission\" : {
              \"gearbox\" : [ -0.15, 0.163, 0.262, 0.38, 0.52, 0.68, 0.87 ]
            }
          },
          \"mass\" : 2000,
          \"geometry\" : {
            \"creationType\" : \"points2D\",
            \"pointArray\" : [
              [1.7, 0],
              [1.68, 0.05],
              [1.67, 0.19],
              [1.7, 0.25],
              [1.69, 0.32],
              [1.67, 0.34],
              [0.55, 0.47],
              [0.1, 0.65],
              [-0.7, 0.67],
              [-1.3,  0.4],
              [-1.79, 0.41],
              [-1.8, 0.4],
              [-1.8, 0.09],
              [-1.85, 0.09],
              [-1.85, 0]
            ],
            \"wheelsCentersPositions\" :  [ [ -1, 0.8 ], [ 1, 0.8 ] ],
            \"radius\" : 0.27,
            \"width\" : 1.9,
            \"bevelThickness\" : 0.05
          },
          \"spawnPosition\" : {
            \"position\" : [ 0, 0, 0 ],
            \"rotation\" : 0
          }
        }]
      }
      ";
}
