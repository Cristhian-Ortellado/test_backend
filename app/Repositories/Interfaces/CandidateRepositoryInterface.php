<?php

namespace App\Repositories\Interfaces;

interface CandidateRepositoryInterface
{
    public function findById($id);

    public function create(array $fields);

    public function all();

    public function candidatesForAgent($ownerId);
}
