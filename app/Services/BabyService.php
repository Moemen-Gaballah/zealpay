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

    public function find($id){
        return Baby::findOrFail($id);
    }

    public function store($data){
        $data['user_id'] = auth('api')->id();
        return Baby::create($data);
    }

    public function update($data, Baby $baby){
        $baby->update($data);

        return $baby;
    }

    public function delete(Baby $baby){
        return $baby->delete();
    }
}
