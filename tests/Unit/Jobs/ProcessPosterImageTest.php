<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Jobs\ProcessPosterImage;

class ProcessPosterImageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_resizes_the_poster_image_to_600px_wide()
    {
        Storage::fake('public');
        Storage::disk('public')->put(
            'posters/example-poster.png',
            file_get_contents(base_path('tests/__fixtures__/full-size-poster.png'))
        );
        $concert = \ConcertFactory::createPublished([
            'poster_image_path' => 'posters/example-poster.png'
        ]);

        ProcessPosterImage::dispatch($concert);

        $resizedImage = Storage::disk('public')->get('posters/example-poster.png');
        list($width) = getimagesizefromstring($resizedImage);
        $this->assertEquals(600, $width);
    }
}
