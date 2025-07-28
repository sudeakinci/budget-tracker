<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Bank;
use App\Models\Transaction;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_transactions()
    {
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        Transaction::factory()->create(['user_id' => $user->id, 'bank_id' => $bank->id]);

        $response = $this->getJson('/transactions');
        $response->assertStatus(200)->assertJsonStructure([
            '*' => ['id', 'user_id', 'bank_id', 'type', 'amount', 'description', 'date', 'created_at', 'updated_at', 'user', 'bank']
        ]);
    }

    public function test_store_creates_transaction()
    {
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $data = [
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'type' => 'income',
            'amount' => 100,
            'description' => 'Test',
            'date' => now()->toDateString(),
        ];
        $response = $this->postJson('/transactions', $data);
        $response->assertStatus(201)->assertJsonFragment($data);
    }

    public function test_show_returns_transaction()
    {
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $transaction = Transaction::factory()->create(['user_id' => $user->id, 'bank_id' => $bank->id]);
        $response = $this->getJson('/transactions/' . $transaction->id);
        $response->assertStatus(200)->assertJsonFragment(['id' => $transaction->id]);
    }

    public function test_update_modifies_transaction()
    {
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $transaction = Transaction::factory()->create(['user_id' => $user->id, 'bank_id' => $bank->id]);
        $update = ['amount' => 200];
        $response = $this->putJson('/transactions/' . $transaction->id, $update);
        $response->assertStatus(200)->assertJsonFragment($update);
    }

    public function test_destroy_deletes_transaction()
    {
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $transaction = Transaction::factory()->create(['user_id' => $user->id, 'bank_id' => $bank->id]);
        $response = $this->deleteJson('/transactions/' . $transaction->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }
}
