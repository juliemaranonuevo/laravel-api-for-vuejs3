<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = [
            'id' => $this->id,
            'avatar' => $this->avatar,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'gender' => $this->gender,
            'createdAt' => isset($this->created_at)?$this->created_at->toIso8601String():null,
            'updatedAt' => isset($this->updated_at)?$this->updated_at->toIso8601String():null
        ];

        return array_filter($user);
    }
}
