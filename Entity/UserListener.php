<?php

namespace Epicoftimewasted\UserBundle\Entity;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Epicoftimewasted\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserListener implements EventSubscriber
{
	/**
	 * @var UserManagerInterface $userManager
	 */
	private $userManager;

	/**
	 * @var ContainerInterface $container
	 */
	private $container;

	/**
	 * Constructor.
	 *
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public function getSubscribedEvents()
	{
		return array(
			Events::prePersist,
			Events::preUpdate,
		);
	}

	public function prePersist(LifecycleEventArgs $args)
	{
		$this->handleEvent($args);
	}

	public function preUpdate(PreUpdateEventArgs $args)
	{
		$this->handleEvent($args);
	}

	private function handleEvent(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		if( $entity instanceof UserInterface ) {
			if( $this->userManager === null )
				$this->userManager = $this->container->get('epicoftimewasted_user.user_manager');

			$this->userManager->updateCanonicalFields($entity);
			$this->userManager->updatePassword($entity);
			if( $args instanceof PreUpdateEventArgs ) {
				/**
				 * Make Doctrine re-check to see if the canonical fields and/or
				 * the password field has changed.
				 */
				$em = $args->getEntityManager();
				$uow = $em->getUnitOfWork();
				$meta = $em->getClassMetadata(get_class($entity));
				$uow->recomputeSingleEntityChangeSet($meta, $entity);
			}
		}
	}
}
