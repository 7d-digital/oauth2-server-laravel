<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server\Storage;

use Illuminate\Database\ConnectionResolverInterface as Resolver;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Storage\ScopeInterface;
use LucaDegasperi\OAuth2Server\Entities\Scope;

/**
 * This is the fluent scope class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class FluentScope implements ScopeRepositoryInterface
{

    /**
     * @var string
     */
    private $defaultScopes;
    public function __construct($defaultScopes = [])
    {
        $this->defaultScopes = $defaultScopes;
    }
    /**
     * Return information about a scope.
     *
     * @param string $identifier The scope identifier
     *
     * @return \League\OAuth2\Server\Entities\ScopeEntityInterface
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        return Scope::where('identifier', $identifier)->first();
    }
    /**
     * Given a client, grant type and optional user identifier validate the set of scopes requested are valid and optionally
     * append additional scopes or remove requested scopes.
     *
     * @param ScopeEntityInterface[] $scopes
     * @param string $grantType
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $clientEntity
     * @param null|string $userIdentifier
     *
     * @return \League\OAuth2\Server\Entities\ScopeEntityInterface[]
     */
    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
    {
        if (!$clientEntity->has('scopes')) {
            return $scopes;
        }
        $clientScopes = $clientEntity->scopes;
        // TODO: this can be simplified imho.
        $scopes = array_filter($scopes, function ($scope) use ($clientScopes) {
            $identifier = $scope->getItentifier();
            return $clientScopes->contains(function ($key, $value) use ($identifier) {
                $value->getIdentifer() == $identifier;
            });
        });
        // TODO: add possibility to append scopes from clients or grants
        return $scopes;
    }
}
