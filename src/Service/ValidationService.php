<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ValidationService.
 */
class ValidationService
{
    /** @var ValidatorInterface $validator */
    private ValidatorInterface $validator;

    /** @var array $errors */
    private array $errors = [];

    /**
     * ValidationService construct.
     * 
     * @param ValidatorInterface $validator
     */
    public function __construct(
        ValidatorInterface $validator
    ) {
        $this->validator = $validator;
    }

    /**
     * Validates or not an entity by collecting the errors
     * 
     * @param mixed $entity
     * 
     * @return bool
     */
    public function validate($entity): bool
    {
        $entityErrors = $this->validator->validate($entity);

        if (0 === $entityErrors->count()) {
            return true;
        }

        foreach ($entityErrors as $entityError) {
            $this->errors[] = $entityError->getMessage();
        }

        return false;
    }

    /**
     * Gets the errors array
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
