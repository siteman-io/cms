<?php

declare(strict_types=1);

namespace Siteman\Cms\Commands\Generator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;

class SettingsGenerator
{
    public function generateSettings(string $className, string $namespace, string $group): string
    {
        $file = new PhpFile;
        $file->setStrictTypes();

        $ns = $file->addNamespace($namespace);
        $ns->addUse('Spatie\LaravelSettings\Settings');

        $class = $ns->addClass($className);
        $class->setExtends('Spatie\LaravelSettings\Settings');

        $class->addProperty('description')
            ->setPublic()
            ->setType('?string')
            ->addComment('This property is just an example.');

        $class->addMethod('group')
            ->setPublic()
            ->setStatic()
            ->setReturnType('string')
            ->setBody('return ?;', [$group]);

        return (new PsrPrinter)->printFile($file);
    }

    public function generateSettingsForm(
        string $className,
        string $namespace,
        string $settingsClassName,
        string $settingsNamespace
    ): string {
        $file = new PhpFile;
        $file->setStrictTypes();

        $ns = $file->addNamespace($namespace);
        $ns->addUse('Filament\Forms\Components\Textarea');
        $ns->addUse('Siteman\Cms\Settings\SettingsFormInterface');
        $ns->addUse($settingsNamespace.'\\'.$settingsClassName);

        $class = $ns->addClass($className);
        $class->addImplement('Siteman\Cms\Settings\SettingsFormInterface');

        $class->addMethod('getSettingsClass')
            ->setPublic()
            ->setStatic()
            ->setReturnType('string')
            ->setBody('return ?;', [new Literal($settingsClassName.'::class')]);

        $class->addMethod('icon')
            ->setPublic()
            ->setReturnType('string')
            ->setBody("return 'heroicon-o-globe-alt';");

        $class->addMethod('schema')
            ->setPublic()
            ->setReturnType('array')
            ->setBody("return [\n    Textarea::make('description')->rows(2),\n];");

        return (new PsrPrinter)->printFile($file);
    }

    public function generateMigration(string $group): string
    {
        $file = new PhpFile;
        $file->setStrictTypes();
        $file->addUse('Spatie\LaravelSettings\Migrations\SettingsMigration');

        $class = new ClassType(null);
        $class->setExtends('Spatie\LaravelSettings\Migrations\SettingsMigration');

        $class->addMethod('up')
            ->setPublic()
            ->setReturnType('void')
            ->setBody("\$this->migrator->add(?, 'Default value');", ["{$group}.description"]);

        $printer = new PsrPrinter;

        return "<?php\n\ndeclare(strict_types=1);\n\n"
            ."use Spatie\\LaravelSettings\\Migrations\\SettingsMigration;\n\n"
            ."return new class extends SettingsMigration\n"
            ."{\n"
            ."    public function up(): void\n"
            ."    {\n"
            ."        \$this->migrator->add('{$group}.description', 'Default value');\n"
            ."    }\n"
            ."};\n";
    }
}
