<?php

namespace Tests\Feature;

use App\Models\Tour;
use Tests\TestCase;
use App\Models\Travel;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TourListTest extends TestCase
{
    use RefreshDatabase;
    public function test_tours_list_by_travel_slug_returns_correct_tours(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');
        $response->assertJsonFragment(['id' => $tour->id]);
    }
    public function test_tours_price_is_shown_correctly(): void
    {
        $travel = Travel::factory()->create();
        Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 400.55
        ]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');
        $response->assertJsonFragment(['price' => '400.55']);

    }
    public function test_tours_list_returns_pagination(): void
    {
        /*
            Laravel pagination returns by default 15 record per page
            so we will create 16 tour records then we will test if there
            is two paginated pages(the first page 15 records and the secound 1 record)
         */
        $travel = Travel::factory()->create();
        Tour::factory(16)->create(['travel_id' => $travel->id,]);

        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');
        $response->assertStatus(200);
        // The first paginated page should have 15 records
        $response->assertJsonCount(15,'data');
        $response->assertJsonPath('meta.current_page', 1);
        $response->assertJsonPath('meta.last_page', 2);
    }
    public function test_tours_list_sorts_by_starting_date_correctly(): void
    {
        $travel = Travel::factory()->create();
        $laterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3)
        ]);
        $erlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now(),
            'ending_date' => now()->addDays(1)
        ]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id',  $erlierTour->id);
        $response->assertJsonPath('data.1.id',  $laterTour->id);

    }
    public function test_tours_list_sorts_by_price_correctly(): void
    {
        $travel = Travel::factory()->create();
        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200,
        ]);
        $cheapLaterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3)
        ]);
        $cheapErlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
            'starting_date' => now(),
            'ending_date' => now()->addDays(1)
        ]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours?sortBy=price&sortOrder=asc');
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id',  $cheapErlierTour->id);
        $response->assertJsonPath('data.1.id',  $cheapLaterTour->id);
        $response->assertJsonPath('data.2.id',  $expensiveTour->id);

    }
    public function test_tours_list_filters_by_price_correctly(): void
    {
        $travel = Travel::factory()->create();
        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200,
        ]);
        $cheapTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
        ]);
        $endpoint = '/api/v1/travels/' . $travel->slug . '/tours';
        $response = $this->get($endpoint.'?priceFrom=100');
        //check if the response contains both tour's price 100 and 200
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        $response = $this->get($endpoint.'?priceFrom=150');
        //check if the response contains only the tour's price 200 and missing the tour's price 150
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

         $response = $this->get($endpoint.'?priceTo=50');
        $response->assertJsonCount(0, 'data');

        $response = $this->get($endpoint.'?priceFrom=150&priceTo=250');
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        $response->assertStatus(200);
    }
     public function test_tours_list_filters_by_starting_date_correctly(): void
    {
        $travel = Travel::factory()->create();
        $lateTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3)

        ]);
        $earlireTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now(),
            'ending_date' => now()->addDays(1)
        ]);
        $endpoint = '/api/v1/travels/' . $travel->slug . '/tours';
        $response = $this->get($endpoint.'?dateFrom='.now());
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $earlireTour->id]);
        $response->assertJsonFragment(['id' => $lateTour->id]);

        $response = $this->get($endpoint.'?dateFrom='.now()->addDay());
        //check if the response contains only the tour's price 200 and missing the tour's price 150
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $earlireTour->id]);
        $response->assertJsonFragment(['id' => $lateTour->id]);

         $response = $this->get($endpoint.'?dateTo='.now()->subDay());
        $response->assertJsonCount(0, 'data');

        $response = $this->get($endpoint.'?dateFrom='.now()->addDay().'&dateTo='.now()->addDays(5));
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $earlireTour->id]);
        $response->assertJsonFragment(['id' => $lateTour->id]);

        $response->assertStatus(200);
    }
}
