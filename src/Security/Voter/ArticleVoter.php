<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class ArticleVoter extends Voter
{
    public function __construct(Security $security) 
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['ROLE_UPDATE_ARTICLE']) && $subject instanceof \App\Entity\Article;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'ROLE_UPDATE_ARTICLE':
                return $this->canUpdate($subject, $user);
                break;
        }

        return false;
    }


    protected function canUpdate($subject, $user) {

        if($this->security->isGranted("ROLE_ADMIN"))
        {
            return true;
        }
        
        return $user === $subject->getAuthor();
    }
}
