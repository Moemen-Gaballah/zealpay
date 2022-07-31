<?php

namespace App\Services;

use App\Models\Baby;

class BabyService
{
    public function get($take = 12){
        $babies = Baby::select('id', 'name')->where('user_id', auth('api')->id());
        if(auth('api')->user()->partner_id)
            $babies->orWhere('user_id', auth('api')->user()->partner_id);

        return $babies->paginate($take);
    }
}
