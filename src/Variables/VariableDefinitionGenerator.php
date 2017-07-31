<?php

namespace ContinuousPipe\Flex\Variables;

/**
 * Will generate the variable definition. This extension point is especially used by ContinuousPipe
 * to encrypt variables such as shared CloudFlare accounts.
 *
 */
interface VariableDefinitionGenerator
{
    public function generateDefinition(string $name, string $value) : array;
}
