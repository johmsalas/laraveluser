<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Connection;

class ActivationRepository
{
    /**
     * Database reference
     * @var Connection
     */
    protected $db;

    /**
     * Table that holds the activation
     * @var string
     */
    protected $table = 'user_activations';

    /**
     * The constructor of ActivationRepository
     * @param Connection $db database connection
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Provides the token
     * @return string
     */
    protected function getToken()
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

    /**
     * Adds a new activation for the provided user
     * @param  User $user
     * @return string token
     */
    public function createActivation($user)
    {
        $activation = $this->getActivation($user);

        if (!$activation) {
            return $this->createToken($user);
        }
        return $this->regenerateToken($user);
    }

    /**
     * Regenerates a token given a user
     * @param  User $user
     * @return string token
     */
    private function regenerateToken($user)
    {
        $token = $this->getToken();
        $this->db->table($this->table)->where('user_id', $user->id)->update([
            'token' => $token,
            'created_at' => new Carbon()
        ]);
        return $token;
    }

    /**
     * Creates a token given a user
     * @param  User $user
     * @return string token
     */
    private function createToken($user)
    {
        $token = $this->getToken();
        $this->db->table($this->table)->insert([
            'user_id' => $user->id,
            'token' => $token,
            'created_at' => new Carbon()
        ]);
        return $token;
    }

    /**
     * Provides the activation reference given a user
     * @param  User $user
     * @return Elloquent activation
     */
    public function getActivation($user)
    {
        return $this->db->table($this->table)->where('user_id', $user->id)->first();
    }

    /**
     * Get the activation given the token
     * @param  string $token
     * @return Elloquent activation
     */
    public function getActivationByToken($token)
    {
        return $this->db->table($this->table)->where('token', $token)->first();
    }

    /**
     * Deletes the activation given the token
     * @param  string $token
     */
    public function deleteActivation($token)
    {
        $this->db->table($this->table)->where('token', $token)->delete();
    }
}
