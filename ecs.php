<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\CodingStandard\Fixer\Annotation\RemovePHPStormAnnotationFixer;
use Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDefaultCommentFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/Serializer',
        __DIR__ . '/Tests',
    ])

    // add a single rule
    ->withRules([
        NoUnusedImportsFixer::class,
        RemovePHPStormAnnotationFixer::class,
        RemoveUselessDefaultCommentFixer::class
    ])

    // add a rule with configuration
    ->withConfiguredRule(
        ArraySyntaxFixer::class,
        ['syntax' => 'short']
    )

    ->withPhpCsFixerSets(
        false,
        false,
        false,
        false,
        false,
        true
    )

    // add sets - group of rules
    ->withPreparedSets(
        true,
        false,
        false,
        false,
        true,
        true,
        true,
        true,
        true,
        true,
        false,
        true
);
