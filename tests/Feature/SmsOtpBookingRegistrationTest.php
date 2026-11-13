<?php

declare(strict_types=1);

use App\Contracts\Messaging\OtpChannelInterface;
use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Repositories\OtpRepositoryInterface;
use App\Contracts\Sms\SmsGatewayInterface;
use App\Contracts\Whatsapp\WhatsappServiceInterface;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Specialist;
use App\Repositories\Customer\EloquentCustomerRepository;
use App\Repositories\Otp\CacheOtpRepository;
use App\Services\Sms\OtpVerificationService;
use App\Services\Sms\SimulatedSmsGateway;
use App\Services\Whatsapp\SimulatedWhatsappService;
use Carbon\Carbon;

describe('WhatsApp & SMS Multi-Channel Architecture, Bindings & Verification Service', function () {
    test('AppServiceProvider binds both WhatsApp and SMS interfaces alongside repository interfaces', function () {
        expect(app(WhatsappServiceInterface::class))->toBeInstanceOf(SimulatedWhatsappService::class);
        expect(app(SmsGatewayInterface::class))->toBeInstanceOf(SimulatedSmsGateway::class);
        expect(app(OtpChannelInterface::class))->toBeInstanceOf(SimulatedWhatsappService::class);
        expect(app(OtpRepositoryInterface::class))->toBeInstanceOf(CacheOtpRepository::class);
        expect(app(CustomerRepositoryInterface::class))->toBeInstanceOf(EloquentCustomerRepository::class);
    });

    test('OtpVerificationService defaults to WhatsApp channel and allows zero-config switching to SMS', function () {
        $whatsappService = app(OtpVerificationService::class);
        expect($whatsappService->getChannelName())->toBe('whatsapp');
        expect($whatsappService->getChannel())->toBeInstanceOf(SimulatedWhatsappService::class);

        // Switch to SMS channel dynamically without complicated setup
        config(['sms.channel' => 'sms']);
        $smsService = app(OtpVerificationService::class);
        expect($smsService->getChannelName())->toBe('sms');
        expect($smsService->getChannel())->toBeInstanceOf(SimulatedSmsGateway::class);

        // Reset to default whatsapp
        config(['sms.channel' => 'whatsapp']);
    });

    test('OtpVerificationService generates 1100 in simulation mode and sends via WhatsApp', function () {
        $otpService = app(OtpVerificationService::class);

        expect($otpService->isSimulationMode())->toBeTrue();
        expect($otpService->generateCode())->toBe('1100');

        // 1. Send OTP via WhatsApp
        $sendResult = $otpService->sendOtp('0551122334', 'لطيفة السبيعي');
        expect($sendResult['sent'])->toBeTrue()
            ->and($sendResult['status'])->toBe('otp_sent')
            ->and($sendResult['channel'])->toBe('whatsapp')
            ->and($sendResult['simulated_code'])->toBe('1100')
            ->and($sendResult['masked_phone'])->toBe('055***2334');

        // 2. Check OTP with wrong code (non-consuming check)
        $wrongCheck = $otpService->checkOtp('0551122334', '2299');
        expect($wrongCheck['valid'])->toBeFalse()
            ->and($wrongCheck['status'])->toBe('invalid_code');

        // 3. Check OTP with valid default code 1100 (and Eastern numerals ١١٠٠)
        $validCheck = $otpService->checkOtp('0551122334', '١١٠٠');
        expect($validCheck['valid'])->toBeTrue()
            ->and($validCheck['status'])->toBe('valid');

        // 4. Verify & consume OTP
        $verifyResult = $otpService->verifyOtp('0551122334', '1100');
        expect($verifyResult['verified'])->toBeTrue()
            ->and($verifyResult['status'])->toBe('verified')
            ->and($verifyResult['payload']['name'])->toBe('لطيفة السبيعي');

        // 5. Subsequent check after consumption returns otp_expired
        $afterConsume = $otpService->checkOtp('0551122334', '1100');
        expect($afterConsume['valid'])->toBeFalse()
            ->and($afterConsume['status'])->toBe('otp_expired');
    });

    test('OtpVerificationService locks out and invalidates OTP after exceeding max attempts', function () {
        config(['sms.otp.max_attempts' => 3]);
        $otpService = app(OtpVerificationService::class);

        $otpService->sendOtp('0559988771', 'منيرة العبدالله');

        $attempt1 = $otpService->verifyOtp('0559988771', '0001');
        expect($attempt1['verified'])->toBeFalse()
            ->and($attempt1['remaining_attempts'])->toBe(2);

        $attempt2 = $otpService->verifyOtp('0559988771', '0002');
        expect($attempt2['verified'])->toBeFalse()
            ->and($attempt2['remaining_attempts'])->toBe(1);

        $attempt3 = $otpService->verifyOtp('0559988771', '0003');
        expect($attempt3['verified'])->toBeFalse()
            ->and($attempt3['status'])->toBe('max_attempts_exceeded')
            ->and($attempt3['remaining_attempts'])->toBe(0);

        // Even the correct code 1100 is now rejected because the OTP was invalidated
        $attempt4 = $otpService->verifyOtp('0559988771', '1100');
        expect($attempt4['verified'])->toBeFalse()
            ->and($attempt4['status'])->toBe('otp_expired');
    });
});

describe('Booking Customer Registration with Name + Phone & 4-Digit WhatsApp OTP (1100)', function () {
    test('booking modal renders visible name and phone inputs, 4 OTP digit boxes, WhatsApp branding, and regular messages icon, and hides guest booking', function () {
        $response = $this->get('/booking');

        $response->assertStatus(200);
        $response->assertSee('تسجيل العميلة وتأكيد الحجز');
        $response->assertSee('id="guestNameInput"', false);
        $response->assertSee('id="bookingPhoneInput"', false);
        $response->assertSee('id="smsOtpVerifyStep"', false);
        $response->assertSee('id="otpDigit0"', false);
        $response->assertSee('id="otpDigit1"', false);
        $response->assertSee('id="otpDigit2"', false);
        $response->assertSee('id="otpDigit3"', false);
        $response->assertSee('WhatsApp');
        $response->assertSee('fa-comment-dots');
        $response->assertSee('1100');
        $response->assertDontSee('المتابعة كـ زائر');
        $response->assertDontSee('تأكيد الحجز فوراً كـ زائر');
    });

    test('send-otp requires both customer name and valid Saudi phone number', function () {
        $specialist = Specialist::where('slug', 'sp_janet')->firstOrFail();
        $service = $specialist->services()->firstOrFail();
        $targetDate = Carbon::today('Asia/Riyadh')->addDays(4)->toDateString();

        // Missing name
        $missingNameResponse = $this->postJson(route('booking.send_otp'), [
            'name' => '',
            'phone' => '0504445566',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => $targetDate,
            'booking_time' => '04:00 م',
        ]);

        $missingNameResponse->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        // Invalid phone
        $invalidPhoneResponse = $this->postJson(route('booking.send_otp'), [
            'name' => 'دانة الفيصل',
            'phone' => '012345',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => $targetDate,
            'booking_time' => '04:00 م',
        ]);

        $invalidPhoneResponse->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    });

    test('full flow: submit name and phone -> send simulated 1100 WhatsApp OTP -> verify 1100 -> register customer and confirm booking as registered', function () {
        $specialist = Specialist::where('slug', 'sp_janet')->firstOrFail();
        $service = $specialist->services()->firstOrFail();
        $targetDate = Carbon::today('Asia/Riyadh')->addDays(4)->toDateString();

        // Step 1: Submit Name + Phone to send OTP via WhatsApp
        $sendOtpResponse = $this->postJson(route('booking.send_otp'), [
            'name' => 'العنود الدوسري',
            'phone' => '+966507778899',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => $targetDate,
            'booking_time' => '04:00 م',
            'guests_count' => 2,
        ]);

        $sendOtpResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'otp_sent',
                'channel' => 'whatsapp',
                'phone' => '0507778899',
                'name' => 'العنود الدوسري',
                'is_simulation' => true,
                'simulated_code' => '1100',
            ]);

        // Customer is NOT created before OTP verification
        $this->assertDatabaseMissing('customers', [
            'phone' => '0507778899',
        ]);

        // Step 2A: Entering an incorrect 4-digit code fails verification
        $wrongOtpResponse = $this->postJson(route('booking.verify_otp'), [
            'name' => 'العنود الدوسري',
            'phone' => '0507778899',
            'otp_code' => '9999',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => $targetDate,
            'booking_time' => '04:00 م',
            'guests_count' => 2,
        ]);

        $wrongOtpResponse->assertStatus(422)
            ->assertJson([
                'success' => false,
                'status' => 'invalid_code',
            ])
            ->assertJsonValidationErrors(['otp_code']);

        // Step 2B: Entering the simulated 4-digit code 1100 verifies, registers customer, logs in, and creates booking
        $verifyOtpResponse = $this->postJson(route('booking.verify_otp'), [
            'name' => 'العنود الدوسري',
            'phone' => '0507778899',
            'otp_code' => '1100',
            'service_id' => $service->code,
            'specialist_id' => $specialist->slug,
            'booking_date' => $targetDate,
            'booking_time' => '04:00 م',
            'guests_count' => 2,
        ]);

        $verifyOtpResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'confirmed',
                'customer_type' => Booking::CUSTOMER_TYPE_REGISTERED,
                'customer' => [
                    'name' => 'العنود الدوسري',
                    'phone' => '0507778899',
                ],
                'booking' => [
                    'customerName' => 'العنود الدوسري',
                    'guests_count' => 2,
                ],
            ]);

        $customer = Customer::where('phone', '0507778899')->firstOrFail();
        expect($customer->name)->toBe('العنود الدوسري');
        expect(session('logged_in_customer_id'))->toBe($customer->id);

        $booking = Booking::where('customer_id', $customer->id)->latest('id')->firstOrFail();
        expect($booking->customer_type)->toBe(Booking::CUSTOMER_TYPE_REGISTERED);
        expect($booking->isRegistered())->toBeTrue();
        expect($booking->isGuest())->toBeFalse();
        expect($booking->guests_count)->toBe(2);
    });
});
