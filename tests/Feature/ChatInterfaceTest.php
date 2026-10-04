<?php

namespace Tests\Feature;

use App\Livewire\ChatInterface;
use App\Services\VectorSearch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class ChatInterfaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_interface_renders_and_initializes_session(): void
    {
        Livewire::test(ChatInterface::class)
            ->assertStatus(200)
            ->assertSee('TirtAssistant')
            ->assertSet('isOpen', false);
    }

    public function test_chat_interface_can_send_message_and_remains_open(): void
    {
        $mockVectorSearch = Mockery::mock(VectorSearch::class);
        $mockVectorSearch->shouldReceive('search')
            ->once()
            ->andReturn([]);
        $mockVectorSearch->shouldReceive('getSources')
            ->once()
            ->andReturn([]);
        $this->app->instance(VectorSearch::class, $mockVectorSearch);

        Livewire::test(ChatInterface::class)
            ->set('isOpen', true)
            ->set('userMessage', 'Berapa biaya pasang?')
            ->call('sendMessage')
            ->assertSet('isOpen', true)
            ->assertSet('userMessage', '')
            ->assertSee('Berapa biaya pasang?');
    }
}
