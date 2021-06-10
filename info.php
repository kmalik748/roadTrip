<?php
$jsonobj = '{"uniqueID": 20, "radius": "25", "lat": "30.148218", "long": "-97.356507", "categories": ["WALKING TOURS", "testing Category"], "firebaseKey": "RandomVeryLongKey", "developmntPhase": "yes"}';

var_dump(json_decode($jsonobj));
