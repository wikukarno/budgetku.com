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
        $data['users_id'] = Auth::user()->id ?? null;
        $data['users_uuid'] = Auth::id();
        $data['icon'] = $data['icon'] ?? null;
        $data['name'] = $data['name'] ?? null;
        // When name is encrypted client-side, store armor and tag content version
        if (!empty($data['name_pgp'])) {
            $data['content_key_version'] = Auth::user()->key_version ?? 1;
        }

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

        // Always ensure ownership fields are consistent
        $data['users_id'] = Auth::user()->id ?? $paymentMethod->users_id;
        $data['users_uuid'] = Auth::id();
        if (!empty($data['name_pgp'])) {
            $data['content_key_version'] = Auth::user()->key_version ?? ($paymentMethod->content_key_version ?? 1);
        }

        return $this->paymentMethodRepository->updatePaymentMethod($id, $data);
    }

    public function deletePaymentMethod($id)
    {
        return $this->paymentMethodRepository->deletePaymentMethod($id);
    }
}
