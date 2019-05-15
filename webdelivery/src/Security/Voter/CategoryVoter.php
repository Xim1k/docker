<?php

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['edit', 'add', 'delete'])
            && $subject instanceof Category;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'edit':
                return $this->canEditDeleteAdd($user);
                break;
            case 'add':
                return $this->canEditDeleteAdd($user);
                break;
            case 'delete':
                return $this->canEditDeleteAdd($user);
                break;
        }

        return false;
    }

    protected function canEditDeleteAdd(User $user)
    {
        foreach($user->getRoles() as $item)
        {
            if ($item == "ROLE_ADMIN")
            {
                return true;
            }
        }
        return false;
    }
}
