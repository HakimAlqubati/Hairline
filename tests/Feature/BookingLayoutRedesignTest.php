<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('booking page renders desktop 65/35 split layout and mobile immersive booking components', function (): void {
    $response = $this->get(route('booking'));

    $response->assertSuccessful()
        ->assertSee('booking-immersive-mode', false)
        ->assertSee('id="bookingSplitLayout"', false)
        ->assertSee('id="desktopBookingSidebar"', false)
        ->assertSee('id="desktopBookingSummaryCard"', false)
        ->assertSee('id="desktopSummaryCountBadge"', false)
        ->assertSee('id="desktopSummaryItemsContainer"', false)
        ->assertSee('id="desktopBookingSubmitBtn"', false)
        ->assertSee('id="mobileBookingTopBar"', false)
        ->assertSee('id="mobileBookingCloseBtn"', false)
        ->assertSee('id="mobileBookingTopTitleText"', false)
        ->assertSee('id="mobileBookingBackBtn"', false)
        ->assertSee('id="mobileBookingBottomBar"', false)
        ->assertSee('id="mobileBookingSummaryToggleBtn"', false)
        ->assertSee('id="mobileBookingActionBtn"', false)
        ->assertSee('id="mobileBookingSummarySheet"', false)
        ->assertSee('id="mobileSheetItemsContainer"', false)
        ->assertSee('id="mobileSheetActionBtn"', false)
        ->assertSee('id="bookingCloseConfirmModal"', false);
});

test('non-booking pages do not enable mobile booking immersive mode on initial load', function (string $routeName): void {
    $response = $this->get(route($routeName));

    $response->assertSuccessful()
        ->assertDontSee('class="bg-surface text-sage-dark antialiased min-h-screen flex flex-col booking-immersive-mode"', false);
})->with(['home', 'services']);

test('verification modal keeps popup booking summary hidden so services are not repeated', function (): void {
    $response = $this->get(route('booking'));

    $response->assertSuccessful()
        ->assertSee('id="bookingVerificationModal"', false)
        ->assertSee('<div id="popupBookingSummary" class="hidden"></div>', false);
});
