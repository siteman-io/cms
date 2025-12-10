<?php declare(strict_types=1);

test('it can see the dashboard', function () {
    $this->actingAs(createUser());

    visit('/admin')->assertSee('Dashboard')->screenshot();
});
