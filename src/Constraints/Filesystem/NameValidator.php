<?php

namespace TanoConsulting\DataValidatorBundle\Constraints\Filesystem;

use TanoConsulting\DataValidatorBundle\Constraint;
use TanoConsulting\DataValidatorBundle\Constraints\FilesystemValidator;
use TanoConsulting\DataValidatorBundle\ConstraintViolation;
use TanoConsulting\DataValidatorBundle\Context\ExecutionContextInterface;
use TanoConsulting\DataValidatorBundle\Exception\UnexpectedTypeException;

class NameValidator extends FilesystemValidator
{
    /**
     * @param string $value path
     * @param Constraint $constraint
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Name) {
            throw new UnexpectedTypeException($constraint, Name::class);
        }

        switch($this->context->getOperatingMode()) {
            case ExecutionContextInterface::MODE_COUNT:
                try {
                    $violationCount = 0;
                    $finder = $this->getFinder($constraint);
                    foreach ($finder->in($value) as $file) {
                        // no need to quote the regexp - double colons are not valid in filenames
                        if (!preg_match(':' . $constraint->matches . ':', $file->getBasename())) {
                            $violationCount++;
                        }
                    }
                    if ($violationCount) {
                        $this->context->addViolation(new ConstraintViolation($this->getMessage($constraint), $violationCount, $constraint));
                    }
                } catch (\Exception $e) {
                    $this->context->addViolation(new ConstraintViolation(preg_replace('/\n */', ' ', $e->getMessage()), null, $constraint));
                }
                break;

            case ExecutionContextInterface::MODE_FETCH:
                try {
                    $violations = [];
                    $finder = $this->getFinder($constraint);
                    foreach ($finder->in($value) as $file) {
                        if (!preg_match(':' . $constraint->matches . ':', $file->getBasename())) {
                            $violations[] = $file->getPathname();
                        }
                    }
                    if ($violations) {
                        $this->context->addViolation(new ConstraintViolation($this->getMessage($constraint), $violations, $constraint));
                    }
                } catch (\Exception $e) {
                    $this->context->addViolation(new ConstraintViolation(preg_replace('/\n */', ' ', $e->getMessage()), null, $constraint));
                }
                break;

            case ExecutionContextInterface::MODE_DRY_RUN:
                $this->context->addViolation(new ConstraintViolation($this->getMessage($constraint), null, $constraint));
                break;
        }
    }

    /// @todo move the message into the Constraint, as is done by upstream validator
    protected function getMessage($constraint)
    {
        return 'File/dir name does not match regexp: ' . $constraint->matches;
    }
}
