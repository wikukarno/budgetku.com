<?php

namespace App\Repositories\Admin;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;

class PaymentMethodRepository
{
    // get all payment methods
    public function getAllPaymentMethods()
    {
        return PaymentMethod::where('users_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // get payment method by id
    public function getPaymentMethodById($id)
    {
        return PaymentMethod::find($id);
    }

    // create payment method
    public function createPaymentMethod($data)
    {
        return PaymentMethod::create($data);
    }

    // update payment method
    public function updatePaymentMethod($id, $data)
    {
        $paymentMethod = PaymentMethod::find($id);
        if ($paymentMethod) {
            $paymentMethod->update($data);
            return $paymentMethod;
        }
        return null;
    }

    // delete payment method
    public function deletePaymentMethod($id)
    {
        $paymentMethod = PaymentMethod::where('id', $id)
            ->where('users_id', Auth::id())
            ->first();
        if ($paymentMethod) {
            return $paymentMethod->delete();
        }
        return false;
    }
}