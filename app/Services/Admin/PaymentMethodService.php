<?php

namespace App\Services\Admin;

use App\Models\PaymentMethod;
use App\Repositories\Admin\PaymentMethodRepository;
use Illuminate\Support\Facades\Auth;

class PaymentMethodService
{
    protected $paymentMethodRepository;

    public function __construct(PaymentMethodRepository $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function getAllPaymentMethods()
    {
        return $this->paymentMethodRepository->getAllPaymentMethods();
    }

    public function getPaymentMethodById($id)
    {
        return $this->paymentMethodRepository->getPaymentMethodById($id);
    }

    public function createPaymentMethod(array $data, $id): PaymentMethod
    {
        $data['users_id'] = Auth::id();
        $data['icon'] = $data['icon'] ?? null;
        $data['name'] = $data['name'] ?? null;

        // if id is provided, update the existing payment method
        if ($id) {
            $paymentMethod = $this->paymentMethodRepository->getPaymentMethodById($id);
            if ($paymentMethod) {
                return $this->paymentMethodRepository->updatePaymentMethod($id, $data);
            }
        }

        // if no id is provided, create a new payment method
        return $this->paymentMethodRepository->createPaymentMethod($data);
    }

    public function updatePaymentMethod($id, $data)
    {
        $paymentMethod = $this->paymentMethodRepository->getPaymentMethodById($id);

        if (!$paymentMethod) {
            throw new \Exception('Payment method not found.');
        }

        if ($data['icon'] ?? null) {
            $data['icon'] = $data['icon'];
        }

        if ($data['name'] ?? null) {
            $data['name'] = $data['name'];
        }

        if ($data['users_id'] ?? null) {
            $data['users_id'] = Auth::id();
        }

        return $this->paymentMethodRepository->updatePaymentMethod($id, $data);
    }

    public function deletePaymentMethod($id)
    {
        return $this->paymentMethodRepository->deletePaymentMethod($id);
    }
}