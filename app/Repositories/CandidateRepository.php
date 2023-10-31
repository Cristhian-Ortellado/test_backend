<?php

namespace App\Repositories;

use App\Models\Candidate;

class CandidateRepository
{

    public function findById($id)
    {
        return Candidate::find($id);
    }

    public function create(array $fields)
    {
        return Candidate::create($fields);
    }

    public function all()
    {
        return Candidate::all();
    }

    public function candidatesForAgent($ownerId)
    {
        return Candidate::where('owner',$ownerId)->get();
    }

}
