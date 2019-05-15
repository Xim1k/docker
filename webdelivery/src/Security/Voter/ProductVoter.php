<?php

namespace App\Security\Voter;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['edit', 'add', 'delete', 'view'])
            && $subject instanceof Product;
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
            case 'add':
                return $this->canAdd($user);
                break;
            case 'edit':
                return $this->canEditDeleteView($user, $subject);
                break;
            case 'view':
                return $this->canEditDeleteView($user, $subject);
                break;
            case 'delete':
                return $this->canEditDeleteView($user, $subject);
                break;
        }

        return false;
    }

    protected function canEditDeleteView(User $user, Product $product)
    {
        foreach($user->getRoles() as $item)
        {
            if ($item == "ROLE_SELLER_MAIN")
            {
                foreach ($product->getSeller()->getUsers() as $user_single)
                {
                    if ($user_single == $user)
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    protected function canAdd(User $user)
    {
        foreach($user->getRoles() as $item)
        {
            if ($item == "ROLE_SELLER_MAIN")
            {
                return true;
            }
        }
        return false;
    }
}
