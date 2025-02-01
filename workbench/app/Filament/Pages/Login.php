<?php declare(strict_types=1);

namespace Workbench\App\Filament\Pages;

use Filament\Pages\Auth\Login as BasePage;

class Login extends BasePage
{
    public function mount(): void
    {
        parent::mount();

        if (app()->environment('local')) {
            $this->form->fill([
                'email' => 'admin@admin.com',
                'password' => 'password',
                'remember' => true,
            ]);
        }
    }
}
