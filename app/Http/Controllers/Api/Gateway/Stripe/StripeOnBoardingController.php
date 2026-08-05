<?php

namespace App\Http\Controllers\Api\Gateway\Stripe;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Google\Rpc\Help;
use Illuminate\Http\Request;
use Stripe\Account;
use Stripe\Stripe;
use Stripe\AccountLink;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\Balance;
use Stripe\Payout;

class StripeOnBoardingController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function accountConnect()
    {
        $user = auth('api')->user();

        try {
            $account = Account::create([
                'type' => 'express',
                'email' => $user->email,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                'settings' => [
                    'payouts' => [
                        'schedule' => [
                            'interval' => 'daily', // daily, weekly, monthly
                        ],
                    ],
                ]
            ]);

            $link = AccountLink::create([
                'account'       => $account->id,
                'refresh_url'   => route('api.payment.stripe.account.connect.refresh', ['account_id' => $account->id]),
                'return_url'    => route('api.payment.stripe.account.connect.success', ['account_id' => $account->id]),
                'type'          => 'account_onboarding'
            ]);

            $data = [
                'url' => $link->url
            ];

            return response()->json(['status' => 'success', 'data' => $data, 'message' => 'Redirecting to Stripe Express Dashboard..'], 200);
        } catch (ApiErrorException $e) {
            return response()->json(['status' => 'error', 'message' => 'Stripe API error: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function accountSuccess($account_id)
    {
        try {

            $account = Account::retrieve($account_id);
            $user = User::where('email', $account->email)->first();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not found in the database for this Stripe account.'], 404);
            }

            $user->update([
                'stripe_account_id' => $account_id
            ]);
            $loginLink = Account::createLoginLink($user->stripe_account_id);
            return redirect()->away($loginLink->url);
        } catch (Exception $e) {

            return response()->json(['status' => 'error', 'message' => 'Error processing onboarding success: ' . $e->getMessage()], 500);
        }
    }

    public function accountRefresh($account_id)
    {
        try {

            $link = AccountLink::create([
                'account'       => $account_id,
                'refresh_url'   => route('api.payment.stripe.account.connect.refresh', ['account_id' => $account_id]),
                'return_url'    => route('api.payment.stripe.account.connect.success', ['account_id' => $account_id]),
                'type'          => 'account_onboarding'
            ]);

            return redirect()->away($link->url);

        } catch (Exception $e) {

            return response()->json(['status' => 'error', 'message' => 'Error generating refresh link: ' . $e->getMessage()], 500);

        }
    }

    public function accountUrl()
    {
        $user = auth('api')->user();

        if ($user->stripe_account_id) {
            try {
                $loginLink = Account::createLoginLink($user->stripe_account_id);

                $data = [
                    'url' => $loginLink->url
                ];
                return response()->json(['status' => 'success', 'data' => $data, 'message' => 'Redirecting to Stripe Express Dashboard..'], 200);
            } catch (Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Error generating Stripe login link: ' . $e->getMessage(),], 500);
            }
        }
    }

    public function accountInfo()
    {
        $user = auth('api')->user();

        if ($user->stripe_account_id) {
            try {
                $account = Account::retrieve($user->stripe_account_id);
                /* $balance = Balance::retrieve([], [
                    'stripe_account' => $user->stripe_account_id,
                ]); */

                $data = [
                    'account_id' => $account->id,
                    'email' => $account->email,
                    'payouts_enabled' => $account->payouts_enabled,
                    /* 'available_balance' => $balance->available,
                    'pending_balance' => $balance->pending, */
                ];

                return response()->json(['status' => 'success', 'data' => $data, 'message' => 'Account info retrieved successfully.', 'code' => 200], 200);
            } catch (Exception $e) {
                Log::info($e->getMessage());
                return response()->json(['status' => 'error', 'message' => 'Error retrieving account info: ' . $e->getMessage(), 'code' => 500], 500);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'User does not have a connected Stripe account.', 'code' => 404], 200);
        }
    }

    public function withdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'  => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return Helper::jsonResponse(false, 'Validation failed', 422, $validator->errors());
        }

        try {
            $$data = $validator->validated();
            $user = auth('api')->user();

            if (!$user || !$user->stripe_account_id) {
                return Helper::jsonResponse(false, 'User does not have a connected Stripe account.', 404);
            }

            $account = Account::retrieve($auth->stripe_account_id);
            if (!$account) {
                return Helper::jsonResponse(false, 'Stripe account not found.', 404);
            }

            $availableBalance = 0;
            $balance = Balance::retrieve(['stripe_account' => $auth->stripe_account_id]);
            if (!empty($balance->available) && isset($balance->available[0]->amount)) {
                $availableBalance = $balance->available[0]->amount / 100;
            }
            if ($availableBalance <= 0) {
                return Helper::jsonResponse(false, 'You do not have enough balance to withdraw.', 400);
            }

            if ($validatedData['amount'] >= $availableBalance) {
                return Helper::jsonResponse(false, 'You do not have enough balance to withdraw.', 400);
            }

            Payout::create([
                'amount'   => $validatedData['amount'] * 100,
                'currency' => 'usd',
            ], ['stripe_account' => $auth->stripe_account_id]);

            return Helper::jsonResponse(true, 'Withdrawal request sent successfully.', 200);
        } catch (ApiErrorException $e) {

            return Helper::jsonResponse(false, $e->getMessage(), 400);
        } catch (Exception $e) {

            return Helper::jsonResponse(false, $e->getMessage(), 400);
        }
    }
}
