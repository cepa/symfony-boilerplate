<?php

namespace Core\Service;

use Core\Entity\Admin;
use Core\Helper\ListContainer;
use Core\Helper\ListParams;
use Core\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * @var AdminRepository
     */
    protected $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->repository = $this->entityManager->getRepository(Admin::class);
    }

    public function create($email, $password): Admin
    {
        $admin = new Admin();
        return $admin
            ->setEmail($email)
            ->setPasswordHash($this->passwordEncoder->encodePassword($admin, $password))
            ->setCreatedAt(new \DateTime())
            ;
    }

    public function save(Admin $admin)
    {
        $this->entityManager->persist($admin);
        $this->entityManager->flush();
    }

    public function delete(Admin $admin)
    {
        $admin->setIsDeleted(true);
        $this->save($admin);
    }

    public function findById(int $id)
    {
        return $this->repository->find($id);
    }

    public function findByEmail(string $email)
    {
        return $this->repository->findOneBy(['email' => $email]);
    }

    public function fetchById(int $id): Admin
    {
        $admin = $this->findById($id);
        if (!($admin instanceof Admin)) {
            throw new \RuntimeException("Can't fetch Admin by id=" . $id);
        }
        return $admin;
    }

    public function fetchByEmail(string $email): Admin
    {
        $admin = $this->findByEmail($email);
        if (!($admin instanceof Admin)) {
            throw new \RuntimeException("Can't fetch Admin by email=" . $email);
        }
        return $admin;
    }

    public function encodePassword(Admin $admin, string $password)
    {
        return $this->passwordEncoder->encodePassword($admin, $password);
    }

    public function validatePassword(Admin $admin, string $password)
    {
        return $this->passwordEncoder->isPasswordValid($admin, $password);
    }

    public function listAdmins(ListParams $params = null): ListContainer
    {
        return new ListContainer($this->repository
            ->buildAdminListQuery($params)
            ->getQuery()
            ->getResult());
    }
}
