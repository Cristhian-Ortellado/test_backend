<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCandidateRequest;
use App\Http\Resources\CandidateResource;
use App\Models\Candidate;
use App\Utilities\RoleUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CandidateController extends Controller
{
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
            $candidates = $user->candidates();
        }

        //we can use redis here because all managers share the same information
        if ($user->hasRole(RoleUtility::MANAGER)){

            if (is_null(Redis::get('user_candidates'))) {
                $candidates = $user->candidates();
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
        $candidate = Candidate::find($id);

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
        $candidate = new Candidate();

        //fill data
        $candidate->fill($request->only('name', 'source'));
        $candidate->owner = $request->get('owner');
        $candidate->created_by = $request->user()->id;
        $candidate->save();

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
