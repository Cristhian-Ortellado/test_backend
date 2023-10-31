<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\User;
use App\Utilities\RoleUtility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     *
     * @return void
     */
    public function should_be_authenticated_to_access()
    {
        $body = ['name' => 'Candidato', 'source' => 'Fotocasa', 'owner' => 1];
        $response = $this->post(route('leads.store'), $body, self::headers());
        $response->assertStatus(401);
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_have_manager_rol_to_access()
    {
        //get an agent user
        $user = User::factory()->create(['role'=>RoleUtility::AGENT]);
        $this->actingAs($user);

        $body = ['name'=>'Candidato 1', 'source'=>'Fotocasa','owner'=>$user->id];

        $response = $this->post(route('leads.store'), $body, self::headers());

        $response->assertStatus(403);
    }

    /**
     * @test
     *
     * @return void
     */
    public function name_source_and_owner_fields_are_required()
    {
        //get an agent user
        $user = User::factory()->create(['role'=>RoleUtility::MANAGER]);
        $this->actingAs($user);

        //name field required
        $body = [ 'source'=>'Fotocasa','owner'=>$user->id];

        $response = $this->post(route('leads.store'), $body, self::headers());

        $response->assertStatus(422);

        //source field required
        $body = [ 'name'=>'juan','owner'=>$user->id];
        $response = $this->post(route('leads.store'), $body, self::headers());
        $response->assertStatus(422);

        //source field required
        $body = [ 'name'=>'juan','source'=>$user->id];
        $response = $this->post(route('leads.store'), $body, self::headers());
        $response->assertStatus(422);

    }

    /**
     * @test
     *
     * @return void
     */
    public function owner_field_should_exists()
    {
        //get an agent user
        $user = User::factory()->create(['role'=>RoleUtility::MANAGER]);
        $this->actingAs($user);

        //name field required
        $body = [ 'source'=>'Fotocasa','owner'=> 55];

        $response = $this->post(route('leads.store'), $body, self::headers());
        $response->assertStatus(422);
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_be_able_to_create_candidate_as_manager()
    {
        //get an agent user
        $user = User::factory()->create(['role'=>RoleUtility::MANAGER]);
        $this->actingAs($user);

        $body = ['name' => 'Candidato', 'source' => 'Fotocasa', 'owner' => $user->id];

        $response = $this->post(route('leads.store'), $body, self::headers());

        $response->assertStatus(201)
        ->assertJson([
            'data'=> [
                'name' =>$body['name'],
                'source'=>$body['source'],
                'owner'=>$body['owner'],
                'created_by'=> $body['owner']
            ],
            'meta'=>[
                'success'=>true,
                'errors'=>[]
            ]
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_not_be_able_to_create_candidate_as_agent()
    {
        //get an agent user
        $user = User::factory()->create(['role'=>RoleUtility::AGENT]);
        $this->actingAs($user);

        $body = ['name' => 'Candidato', 'source' => 'Fotocasa', 'owner' => $user->id];

        $response = $this->post(route('leads.store'), $body, self::headers());

        $response->assertStatus(403);
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_not_be_able_to_get_candidate_not_related_to_authenticathed_user_as_agent()
    {
        //get an agent user
        $user = User::factory()->create(['role'=>RoleUtility::AGENT]);
        $this->actingAs($user);

        $candidate = Candidate::factory()->create();

        $response = $this->get(route('leads.show',$candidate), self::headers());

        $response->assertStatus(403)
            ->assertJson([
                'meta'=>[
                    'success'=>false,
                    'errors'=>['You don\'t have access to this resource']
                ]
            ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_be_able_to_get_candidate_not_related_to_authenticathed_user_as_manager()
    {
        //get an agent user
        $user = User::factory()->create(['role'=>RoleUtility::MANAGER]);
        $this->actingAs($user);

        $candidate = Candidate::factory()->create();

        $response = $this->get(route('leads.show',$candidate), self::headers());

        $response->assertStatus(200);
    }

    /**
     * @test
     *
     * @return void
     */
    public function fetch_all_related_candidates_for_agent_role()
    {
        //get an agent user
        $user = User::factory()->create(['role'=>RoleUtility::AGENT]);
        $this->actingAs($user);

        //create 4 candidate related to this user
        $candidate = Candidate::factory()->count(4)->create(['owner'=>$user->id]);

        //create two more
        Candidate::factory()->count(2)->create();

        $response = $this->get(route('leads.index'), self::headers());

        $response->assertStatus(200)
        ->assertJsonCount(4,'data');
    }

    /**
     * @test
     *
     * @return void
     */
    public function fetch_all_related_candidates_for_manager_role()
    {
        //get an agent user
        $user = User::factory()->create(['role'=>RoleUtility::MANAGER]);
        $this->actingAs($user);

        //create 4 candidate related to this user
        Candidate::factory()->count(4)->create(['owner'=>$user->id]);

        //create two more
        Candidate::factory()->count(2)->create();

        $response = $this->get(route('leads.index'), self::headers());

        //as manager should get all candidates
        $response->assertStatus(200)
            ->assertJsonCount(6,'data');
    }

}
