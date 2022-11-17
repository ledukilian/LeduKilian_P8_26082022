<?php
namespace App\Security;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class TaskVoter extends Voter
{
    /* All task functionnalities */
    const EDIT = 'edit-task';
    const DELETE = 'delete-task';
    private Security $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        /* If attribute is not supported, return false */
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }

        /* If attribute is not a Task, return false */
        if (!$subject instanceof Task) {
            return false;
        }

        /* Else, return true, attribute and subject are supported */
        return true;
    }


    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;

        return match ($attribute) {
            self::EDIT => $this->isOwnerOrAdmin($task, $user),
            self::DELETE => $this->deleteRights($task, $user),
            default => false,
        };
    }

    private function deleteRights(Task $task, User $user): bool
    {
        /* Check if the user is the owner */
        if ($task->getUser() === $user) {
            return true;
            /* Anonymous task can only be deleted by admin users */
        } elseif ($task->getUser()->getRoles() === ['ROLE_ANONYMOUS'] && $this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        return false;
        ;
    }

    /**
     * @param Task $task
     * @param User  $user
     * @return bool
     */
    private function isOwnerOrAdmin(Task $task, User $user): bool
    {
        /* The user is owner of the task */
        if ($user === $task->getUser()) {
            return true;
            /* The user is Admin */
        } elseif ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        /* Else he can't delete the trick */
        return false;
    }

}
