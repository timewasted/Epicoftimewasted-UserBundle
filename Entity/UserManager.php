<?php

namespace Epicoftimewasted\UserBundle\Entity;

//use Epicoftimewasted\CryptoBundle\Security\CryptoManager;
use Doctrine\ORM\EntityManager;
use Epicoftimewasted\UserBundle\Model\AbstractUserManager;
use Epicoftimewasted\UserBundle\Model\EpicoftimewastedUserInterface;
use Epicoftimewasted\UserBundle\Util\CanonicalizerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraint;

class UserManager extends AbstractUserManager
{
	/**
	 * @var EntityManager $em
	 */
	protected $em;

	/**
	 */
	protected $repository;

	/**
	 * @var string $class
	 */
	protected $class;

	/**
	 * Constructor.
	 *
	 * @param EncoderFactoryInterface $encoderFactory
	 * @param string $algorithm
	 * @param integer $workFactor
	 * @param CanonicalizerInterface $usernameCanonicalizer
	 * @param CanonicalizerInterface $emailCanonicalizer
	 * @param CryptoManager $cryptoManager
	 * @param EntityManager $em
	 * @param string $class
	 */
	public function __construct(EncoderFactoryInterface $encoderFactory, $algorithm, $workFactor, CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer, /*CryptoManager*/ $cryptoManager, EntityManager $em, $class)
	{
		parent::__construct($encoderFactory, $algorithm, $workFactor, $usernameCanonicalizer, $emailCanonicalizer, $cryptoManager);

		$this->em = $em;
		$this->repository = $this->em->getRepository($class);
		$this->class = $class;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateUser(EpicoftimewastedUserInterface $user, $shouldFlush = true)
	{
		$this->updateCanonicalFields($user);
		$this->updatePassword($user);

		$this->em->persist($user);
		if( $shouldFlush )
			$this->em->flush();
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleteUser(EpicoftimewastedUserInterface $user)
	{
		$this->em->remove($user);
		$this->em->flush();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findUserBy(array $criteria)
	{
		return $this->repository->findOneBy($criteria);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validateUnique(EpicoftimewastedUserInterface $user, Constraint $constraint)
	{
		$classMetadata = $this->em->getClassMetadata($this->class);

		foreach( $constraint->properties as $property ) {
			if( !$classMetadata->hasField($property) )
				throw new \InvalidArgumentException(sprintf('The "%s" class metadata does not have any "%s" field or association mapping.', $this->class, $field));

			$criteria = array($property => $classMetadata->getFieldValue($user, $property));
			if( $this->findUserBy($criteria) !== null )
				return false;
		}

		return true;
	}
}
