<?php declare(strict_types=1);

namespace Siteman\Cms\Commands\Generator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use Siteman\Cms\Settings\SettingsFormInterface;
use Spatie\LaravelSettings\Migrations\SettingsMigration;
use Spatie\LaravelSettings\Settings;

class SettingsGenerator
{
    public function generateSettings(string $className, string $namespace, string $group): string
    {
        $file = new PhpFile;
        $file->setStrictTypes();

        $ns = $file->addNamespace($namespace);

        $class = $ns->addClass($className);
        $class->setExtends(Settings::class);

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
        $class->addImplement(SettingsFormInterface::class);

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
            ->setBody("return [Textarea::make('description')->rows(2)];");

        return (new PsrPrinter)->printFile($file);
    }

    public function generateMigration(string $group): string
    {
        $file = new PhpFile;
        $file->setStrictTypes();

        $class = new ClassType(null);
        $class->setExtends(SettingsMigration::class);

        $class->addMethod('up')
            ->setPublic()
            ->setReturnType('void')
            ->setBody("\$this->migrator->add(?, 'Default value');", ["{$group}.description"]);

        return "<?php declare(strict_types=1);\n\n"
            .'return new class '.$class.';';
    }
}
