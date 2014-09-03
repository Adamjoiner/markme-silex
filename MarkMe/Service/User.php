<?php

namespace MarkMe\Service;

use Doctrine\ORM\EntityRepository;
use MarkMe\Entity\User as UserEntity;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use MarkMe\Service\UserInterface as UserServiceInterface;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * User
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class User extends EntityRepository implements UserProviderInterface, UserServiceInterface {

    /**
     * @var EncoderFactory
     */
    private $encoderFactory;

    /**
     *
     * @var Validator 
     */
    private $validator;

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     *
     */
    public function loadUserByUsername($username) {
        $user = $this->findOneBy(array('username' => $username));
        if ($user == null) {
            throw new UsernameNotFoundException();
        }
        return $user;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user) {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class) {
        return $class == '\MarkMe\Entity\User';
    }

    function setEncoderFactory(EncoderFactory $encoderFactory) {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @return EncoderFactory
     */
    function getEncoderFactory() {
        return $this->encoderFactory;
    }

    function setValidator(Validator $validator) {
        $this->validator = $validator;
    }

    function register(UserEntity $user, $flush = true) {
        $this->validate($user);
        !$user->getSalt() AND $user->setSalt(md5($user->getUsername() . $user->getEmail() . date("U")));

        $user->setPassword($this->getEncoderFactory()->getEncoder($user)->encodePassword($user->getPassword(), $user->getSalt()));

        $this->getEntityManager()->persist($user);
        if ($flush == true)
            $this->getEntityManager()->flush($user);
        return $user;
    }

    protected function validate($object) {
        $errors = $this->validator->validate($bookmark);
        if (count($errors) > 0) {
            throw new ValidatorException($errors[0]->getMessage());
        }
    }

}
