<?php

namespace Core\Service;

use Core\Entity\User;
use Core\Event\UserAccountEvent;
use Core\Helper\RandomIdGenerator;
use Core\Helper\RandomStringGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * @var EntityRepository
     */
    protected $repository;

    public function __construct(
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->repository = $this->entityManager->getRepository(User::class);
    }

    public function register(array $data): User
    {
        $required = ['name', 'email', 'password'];
        foreach ($required as $key) {
            if (!isset($data[$key])) {
                throw new \RuntimeException("Missing value for key: " . $key);
            }
        }

        $this->logger->debug(sprintf('%s: Registering user account, email=%s',
            __METHOD__, $data['email']));

        $user = $this->create($data['email'], $data['password']);
        $user
            ->setIsActive(false)
            ->setToken(RandomStringGenerator::generate(32))
            ->populate($data)
            ;

        $this->save($user);

        $this->eventDispatcher->dispatch(UserAccountEvent::REGISTERED, new UserAccountEvent($user));

        return $user;
    }

    public function activate(User $user)
    {
        $user->setIsActive(true);
        $this->save($user);

        $this->eventDispatcher->dispatch(UserAccountEvent::ACTIVATED, new UserAccountEvent($user));
    }

    public function update(User $user, array $changes)
    {
        $allowedKeys = ['name', 'location'];
        foreach ($changes as $key => $value) {
            if (in_array($key, $allowedKeys)) {
                $user->populate([$key => $value]);
            }
        }

        $this->save($user);
        $this->eventDispatcher->dispatch(UserAccountEvent::MODIFIED, new UserAccountEvent($user));
    }

    public function changePassword(User $user, string $newPassword)
    {
        $user->setPasswordHash($this->passwordEncoder->encodePassword($user, $newPassword));
        $user->setToken(RandomStringGenerator::generate(32));
        $this->save($user);

        $this->eventDispatcher->dispatch(UserAccountEvent::PASSWORD_CHANGED, new UserAccountEvent($user));
    }

    public function requestPassword(User $user)
    {
        $user->setToken(RandomStringGenerator::generate(32));
        $this->save($user);

        $this->eventDispatcher->dispatch(UserAccountEvent::PASSWORD_REQUESTED, new UserAccountEvent($user));
    }

    public function create($email, $password): User
    {
        $user = new User();
        return $user
            ->setEmail($email)
            ->setPasswordHash($this->encodePassword($user, $password))
            ->setUniqueId(RandomIdGenerator::generate())
            ->setCreatedAt(new \DateTime());
    }

    public function save(User $user)
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function delete(User $user)
    {
        $user->setIsDeleted(true);
        $this->save($user);
    }

    public function findById(int $id)
    {
        return $this->repository->find($id);
    }

    public function findByUniqueId(string $uniqueId)
    {
        return $this->repository->findOneBy(['uniqueId' => $uniqueId]);
    }

    public function findByEmail(string $email)
    {
        return $this->repository->findOneBy(['email' => mb_strtolower($email)]);
    }

    public function fetchById(int $id): User
    {
        $user = $this->findById($id);
        if (!($user instanceof User)) {
            throw new \RuntimeException("Can't fetch User by id=" . $id);
        }
        return $user;
    }

    public function fetchByUniqueId(string $uniqueId): User
    {
        $user = $this->findByUniqueId($uniqueId);
        if (!($user instanceof User)) {
            throw new \RuntimeException("Can't fetch User by unique_id=" . $uniqueId);
        }
        return $user;
    }

    public function fetchByEmail(string $email): User
    {
        $user = $this->findByEmail($email);
        if (!($user instanceof User)) {
            throw new \RuntimeException("Can't fetch User by email=" . $email);
        }
        return $user;
    }

    public function encodePassword(User $user, string $password)
    {
        return $this->passwordEncoder->encodePassword($user, $password);
    }

    public function validatePassword(User $user, string $password)
    {
        return $this->passwordEncoder->isPasswordValid($user, $password);
    }
}
