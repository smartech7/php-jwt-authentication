<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tymon\JWTAuth;

use Illuminate\Support\Str;
use Tymon\JWTAuth\Claims\Claim;
use Tymon\JWTAuth\Claims\Collection;
use Tymon\JWTAuth\Support\RefreshFlow;
use Tymon\JWTAuth\Support\CustomClaims;
use Tymon\JWTAuth\Validators\PayloadValidator;
use Tymon\JWTAuth\Claims\Factory as ClaimFactory;

class Factory
{
    use RefreshFlow, CustomClaims;

    /**
     * @var \Tymon\JWTAuth\Claims\Factory
     */
    protected $claimFactory;

    /**
     * @var \Tymon\JWTAuth\Validators\PayloadValidator
     */
    protected $validator;

    /**
     * @var int
     */
    protected $ttl = 60;

    /**
     * @var array
     */
    protected $defaultClaims = ['iss', 'iat', 'exp', 'nbf'];

    /**
     * @var \Tymon\JWTAuth\Claims\Collection
     */
    protected $claims;

    /**
     * @param  \Tymon\JWTAuth\Claims\Factory  $claimFactory
     * @param  \Tymon\JWTAuth\Validators\PayloadValidator  $validator
     *
     * @return void
     */
    public function __construct(ClaimFactory $claimFactory, PayloadValidator $validator)
    {
        $this->claimFactory = $claimFactory;
        $this->validator = $validator;

        $this->claims = new Collection;
    }

    /**
     * Create the Payload instance.
     *
     * @return \Tymon\JWTAuth\Payload
     */
    public function make()
    {
        $claims = $this->buildClaims()->resolveClaims();

        return $this->withClaims($claims);
    }

    /**
     * Add an array of claims to the Payload.
     *
     * @param  array  $claims
     *
     * @return $this
     */
    public function addClaims(array $claims)
    {
        foreach ($claims as $name => $value) {
            $this->addClaim($name, $value);
        }

        return $this;
    }

    /**
     * Add a claim to the Payload.
     *
     * @param  string  $name
     * @param  mixed  $value
     *
     * @return $this
     */
    public function addClaim($name, $value)
    {
        $this->claims->put($name, $value);

        return $this;
    }

    /**
     * Build the default claims.
     *
     * @return $this
     */
    protected function buildClaims()
    {
        // remove the exp claim if it exists and the ttl is null
        if ($this->ttl === null && $key = array_search('exp', $this->defaultClaims)) {
            unset($this->defaultClaims[$key]);
        }

        // add the default claims
        foreach ($this->defaultClaims as $claim) {
            $this->addClaim($claim, $this->claimFactory->make($claim));
        }

        // add custom claims on top, allowing them to overwrite defaults
        $this->addClaims($this->getCustomClaims());

        // add the jti last since it is based on all other claims,
        // but only if it hasn't already been added.
        if (! $this->claims->has('jti')) {
            $this->addClaim('jti', $this->jti());
        }

        return $this;
    }

    /**
     * Build out the Claim DTO's.
     *
     * @return \Tymon\JWTAuth\Claims\Collection
     */
    protected function resolveClaims()
    {
        return $this->claims->map(function ($value, $name) {
            return $value instanceof Claim ? $value : $this->claimFactory->get($name, $value);
        });
    }

    /**
     * Get a Payload instance with a claims collection.
     *
     * @param  \Tymon\JWTAuth\Claims\Collection  $claims
     *
     * @return \Tymon\JWTAuth\Payload
     */
    public function withClaims(Collection $claims)
    {
        return new Payload($claims, $this->validator, $this->refreshFlow);
    }

    /**
     * Get a unique id (jti) for the token.
     *
     * @return string
     */
    protected function jti()
    {
        return md5(sprintf('%s.%s', $this->claims->toJson(), Str::quickRandom()));
    }

    /**
     * Set the default claims to be added to the Payload.
     *
     * @param  array  $claims
     *
     * @return $this
     */
    public function setDefaultClaims(array $claims)
    {
        $this->defaultClaims = $claims;

        return $this;
    }

    /**
     * Get the default claims.
     *
     * @return array
     */
    public function getDefaultClaims()
    {
        return $this->defaultClaims;
    }

    /**
     * Get the PayloadValidator instance.
     *
     * @return \Tymon\JWTAuth\Validators\PayloadValidator
     */
    public function validator()
    {
        return $this->validator;
    }

    /**
     * Magically add a claim.
     *
     * @param  string  $method
     * @param  array  $parameters
     *
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $this->addClaim($method, $parameters[0]);

        return $this;
    }
}
