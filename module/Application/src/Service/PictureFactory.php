<?php

namespace Application\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

use Application\Controller\PictureController;
use Application\Database\PicturesTable;
use Application\Database\CommentsTable;

final class PictureFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName === 'Application\Controller\PictureController')
        {
            $credentials = $container->get('Config')['db_credentials'];
            $user = $credentials['user'];
            $password = $credentials['password'];
            $database = $credentials['database'];
            
            $picturesTable = new PicturesTable($database, $user, $password);
            $commentsTable = new CommentsTable($database, $user, $password);
        
            return new PictureController($picturesTable, $commentsTable);
        }
        return (null === $options) ? new $requestedName : new $requestedName($options);
    }
}
