<?php

namespace App\Services\Cart;

use App\Models\UserContact;
use Illuminate\Support\Facades\Auth;

class UserContactService
{
    protected function current(): ?UserContact
    {
        return UserContact::with('user')
            ->where('user_id', Auth::id())
            ->first();
    }

    public function show(): array
    {
        $c = $this->current();
        return $c ? $this->transform($c) : [];
    }

    public function upsert(array $data): array
    {
        $payload = [
            'address'    => $data['address'] ?? null,
            'phone'      => $data['phone'] ?? null,
            'order_note' => $data['order_note'] ?? null,
        ];

        $c = UserContact::updateOrCreate(
            ['user_id' => Auth::id()],
            $payload
        );

        $c->load('user');

        return $this->transform($c);
    }

    public function create(array $data): array
    {
        return $this->upsert($data);
    }

    public function delete(): void
    {
        if ($c = $this->current()) {
            $c->delete();
        }
    }

    public function snapshotForOrder(): array
    {
        $c = $this->current();
        $user = Auth::user();

        return [
            'user' => $user ? [
                'id'    => $user->id,
                'name'  => $user->name ?? null,
                'email' => $user->email ?? null,
            ] : null,
            'contact' => $c ? [
                'id'         => $c->id,
                'address'    => $c->address,
                'phone'      => $c->phone,
                'order_note' => $c->order_note,
            ] : null,
        ];
    }

    protected function transform(UserContact $c): array
    {
        return [
            'id'         => $c->id,
            'address'    => $c->address,
            'phone'      => $c->phone,
            'order_note' => $c->order_note,
        ];
    }
}
