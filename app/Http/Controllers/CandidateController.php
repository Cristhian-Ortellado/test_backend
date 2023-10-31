<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCandidateRequest;
use App\Http\Resources\CandidateResource;
use App\Repositories\CandidateRepository;
use App\Repositories\Interfaces\CandidateRepositoryInterface;
use App\Utilities\RoleUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CandidateController extends Controller
{

    private $candidateRepository;

    public function __construct(CandidateRepositoryInterface $candidateRepository)
    {
        $this->candidateRepository = $candidateRepository;
    }

    /*
     * Return all Candidates resources related to the authenticated user
     *  Users with manager role can access to all candidates resources
     * */
    public function index(Request $request)
    {
        //verify if the authenticated user have access to this candidate
        $user = $request->user();
        $candidates = [];

        // we can't use redis here because each agent have his own candidate list
        //if we try to save a list of candidates for each agent then redis if going to have an overflow of memory (this will happen for big projects)
        if ($user->hasRole(RoleUtility::AGENT)) {
            $candidates = $this->candidateRepository->candidatesForAgent($user->id);
        }

        //we can use redis here because all managers share the same information
        if ($user->hasRole(RoleUtility::MANAGER)){

            if (is_null(Redis::get('user_candidates'))) {
                //use repository here
                $candidates = $this->candidateRepository->all();
                Redis::set('user_candidates', json_encode($candidates));

            } else {
                $candidates = json_decode(Redis::get('user_candidates'));

            }
        }

        return CandidateResource::collection($candidates)->additional([
            'meta' => [
                'success' => true,
                'errors' => []
            ]
        ]);
    }


    /*
     * Return a Candidate resource
     *  User with agent role can access just to candidate where they are owners
     *  Users with manager role can access to all candidates resources
     * */
    public function show(Request $request, $id)
    {
        $candidate = $this->candidateRepository->findById($id);

        if (is_null($candidate)) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => ['No lead found']
                ]
            ], 404);
        }

        //verify if the authenticated user have access to this candidate
        $user = $request->user();

        if ($user->hasRole(RoleUtility::AGENT) && $candidate->owner !== $user->id) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => ['You don\'t have access to this resource']
                ]
            ], 403);
        }

        return CandidateResource::make($candidate)
            ->additional([
                'meta' => [
                    'success' => true,
                    'errors' => []
                ]
            ]);
    }


    public function store(StoreCandidateRequest $request)
    {
        $user = $request->user();

        $candidate = $this->candidateRepository->create(
            array_merge(
                $request->only('name', 'source','owner'),
                ['created_by' =>$user->id]
            )
        );

        //if we have new candidates delete the old memory for this redis cache key
        Redis::del('user_candidates');

        return CandidateResource::make($candidate)->additional([
            'meta' => [
                'success' => true,
                'errors' => []
            ]
        ]);
    }


}
