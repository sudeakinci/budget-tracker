<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\PaymentTerm;
use App\Models\Transaction;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_transactions()
    {
        $user = User::factory()->create();
        $paymentTerm = PaymentTerm::factory()->create();
        Transaction::factory()->create(['user_id' => $user->id, 'payment_term_id' => $paymentTerm->id]);

        $response = $this->getJson('/api/transactions');
        $response->assertStatus(200);
    }

    public function test_store_creates_transaction()
    {
        $user = User::factory()->create();
        $paymentTerm = PaymentTerm::factory()->create();
        $data = [
            'owner' => $user->id,
            'user_id' => $user->id,
            'payment_term_id' => $paymentTerm->id,
            'amount' => 100,
            'description' => 'Test',
        ];
        $response = $this->postJson('/api/transactions', $data);
        $response->assertStatus(201);
    }

    public function test_show_returns_transaction()
    {
        $user = User::factory()->create();
        $paymentTerm = PaymentTerm::factory()->create();
        $transaction = Transaction::factory()->create(['user_id' => $user->id, 'payment_term_id' => $paymentTerm->id]);
        $response = $this->getJson('/api/transactions/' . $transaction->id);
        $response->assertStatus(200);
    }

    public function test_update_modifies_transaction()
    {
        $user = User::factory()->create();
        $paymentTerm = PaymentTerm::factory()->create();
        $transaction = Transaction::factory()->create(['user_id' => $user->id, 'payment_term_id' => $paymentTerm->id]);
        $update = ['amount' => 200];
        $response = $this->putJson('/api/transactions/' . $transaction->id, $update);
        $response->assertStatus(200);
    }

    public function test_destroy_deletes_transaction()
    {
        $user = User::factory()->create();
        $paymentTerm = PaymentTerm::factory()->create();
        $transaction = Transaction::factory()->create(['user_id' => $user->id, 'payment_term_id' => $paymentTerm->id]);
        $response = $this->deleteJson('/api/transactions/' . $transaction->id);
        $response->assertStatus(200);
    }
}
