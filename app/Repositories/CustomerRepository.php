<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    protected $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function create($customerData)
    {
        try {
            return $this->customer->create($customerData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function find($id)
    {
        try {
            return $this->customer->find($id);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function get()
    {
        try {
            return $this->customer->get();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function update($id, $customerData)
    {
        try {
            return $this->customer->where('id', $id)->update($customerData);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function delete($id)
    {
        try {
            $customer = $this->customer->find($id);
            $customer->delete();
            return $customer;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function count()
    {
        try {
            return $this->customer->count();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

  
    public function exists($name, $contact)
    {
        try {
            return $this->customer->where('name', $name)->where('contact', $contact)->exists();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function existsonUpdate($id, $name, $contact)
    {
        try {
            return $this->customer->where('id', '!=', $id)->where('name', $name)->where('contact', $contact)->exists();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
