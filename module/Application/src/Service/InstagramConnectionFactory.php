<?php

namespace Application\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

use Instagram\Auth;

use Application\Controller\InstagramConnectionController;
use Application\Database\PicturesTable;
use Application\Database\CommentsTable;
use Application\Database\AuthorsTable;

final class InstagramConnectionFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName === 'Application\Controller\InstagramConnectionController')
        {
            $credentials = $container->get('Config')['db_credentials'];
            $user = $credentials['user'];
            $password = $credentials['password'];
            $database = $credentials['database'];
            
            $picturesTable = new PicturesTable($database, $user, $password);
            $commentsTable = new CommentsTable($database, $user, $password);
            $authorsTable  = new AuthorsTable($database, $user, $password);
        
            $instagram = $container->get('Config')['instagram_client'];
            $auth = new Auth($instagram);
        
            return new InstagramConnectionController($auth, $picturesTable,
                                                     $commentsTable, $authorsTable);
        }
        return (null === $options) ? new $requestedName : new $requestedName($options);
    }
}
