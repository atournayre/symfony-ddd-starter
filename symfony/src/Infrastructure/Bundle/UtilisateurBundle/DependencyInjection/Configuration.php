<?php

namespace App\Infrastructure\Bundle\UtilisateurBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('utilisateur');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}