<?php 
    $property_array = array();

    if($properties)
    {
        foreach($properties->result() as $property)
        {
            $item = array();
            $item["address"] = $property->address;
            $item["suburb"] = $property->suburb;
            $item["area"] = $property->area_name;
            $item["state"] = $property->state_name;
            $item["bedrooms"] = $property->bedrooms;
            $item["bathrooms"] = $property->bathrooms;
            $item["garage"] = $property->garage;
            $item["nras"] = $property->nras;
            $item["smsf"] = $property->smsf;
            $item["total_price"] = $property->total_price;
            $item["land_area"] = $property->land;
            $item["house_area"] = $property->house_area;
            $item["rate"] = $property->rate;
            
            // Add the item to the property array
            $property_array[] = $item;
        }
    }

    echo json_encode($property_array);