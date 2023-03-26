<?php

namespace App\Repositories;

use App\Models\TakenBottle;

class TakenBottleRepository
{
    protected $takenBottle;

    public function __construct(TakenBottle $takenBottle)
    {
        $this->takenBottle = $takenBottle;
    }

    public function create($takenBottleData)
    {
        try {
            return $this->takenBottle->create($takenBottleData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function find($id)
    {
        try {
            return $this->takenBottle->find($id);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function get()
    {
        try {
            return $this->takenBottle->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function update($id, $takenBottleData)
    {
        try {
            return $this->takenBottle->where('id', $id)->update($takenBottleData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function delete($id)
    {
        try {
            $takenBottle = $this->takenBottle->find($id);
            $takenBottle->delete();
            return $takenBottle;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function count()
    {
        try {
            return $this->takenBottle->count();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}
