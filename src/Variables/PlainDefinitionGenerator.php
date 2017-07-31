<?php

namespace ContinuousPipe\Flex\Variables;

class PlainDefinitionGenerator implements VariableDefinitionGenerator
{
    public function generateDefinition(string $name, string $value): array
    {
        return [
            'name' => $name,
            'value' => $value,
        ];
    }
}
