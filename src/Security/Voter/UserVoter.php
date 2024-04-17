<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const POST_UPDATE = 'POST_UPDATE';

    public function __construct(
        private Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::POST_UPDATE])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        // ROLE_SUPER_ADMIN can do anything! The power!
        if ($this->security->isGranted(User::ROLE_SUPER_ADMIN)) {
            return true;
        }
        // ROLE_COMPANY_ADMIN can only change their user
        if ($this->security->isGranted(User::ROLE_COMPANY_ADMIN) &&
            $user->getCompany()->getId() === $subject->getCompany()->getId()) {
            return true;
        }

        switch ($attribute) {
            case self::POST_UPDATE:
                if ($this->security->isGranted(User::ROLE_USER) &&
                    !in_array(User::ROLE_SUPER_ADMIN,$user->getRoles()) &&
                    !in_array(User::ROLE_COMPANY_ADMIN,$user->getRoles()) ){
                    return true;
                }
                break;
        }

        return false;
    }
}
