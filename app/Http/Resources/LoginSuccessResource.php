<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginSuccessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
       return [
           'meta' => [
               'success' => true,
               'errors' => []
           ],
           'data' => [
               'token' => $this->createToken("Bearer", ['*'], now()->minutes(config('app.TOKEN_EXPIRATION_TIME_MIN')))->plainTextToken,
               'minutes_to_expire' => config('app.TOKEN_EXPIRATION_TIME_MIN')
           ]
       ];
    }
}
